<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiController extends Controller
{
    public function generate(Request $request)
    {
        // Require admin authentication to trigger this manually via UI (unless running locally)
        if (!auth()->check() && !app()->runningInConsole() && !in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $geminiKey = env('GEMINI_API_KEY', '');
        if (!$geminiKey) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bạn chưa cấu hình GEMINI_API_KEY trong file .env'], 500);
            }
            return redirect()->back()->with('error', 'Bạn chưa cấu hình GEMINI_API_KEY trong file .env');
        }

        $promptText = <<<EOT
Hãy đóng vai một phóng viên, biên tập viên tin tức du lịch chuyên nghiệp.
Nhiệm vụ: Sáng tạo một **Bài báo/Phóng sự tin tức du lịch** hoàn toàn mới (ví dụ: phát hiện một địa điểm mới, sự kiện văn hóa, phân tích xu hướng du lịch, điểm đến hoang sơ, ẩm thực độc lạ).

PHONG CÁCH VIẾT (BẮT BUỘC):
- Viết dưới dạng **Tin tức báo chí (News/Reportage)**, dùng **văn xuôi** trang trọng, khách quan nhưng hấp dẫn và cuốn hút. Không viết kiểu nhật ký blog cá nhân.
- Tựa đề (Title) mang tính giật tít báo chí, thu hút sự chú ý.
- BÀI VIẾT PHẢI RẤT DÀI, CHI TIẾT VÀ CHUYÊN SÂU (Tối thiểu 2000 chữ). Tuyệt đối không được viết nửa chừng rồi cắt ngang câu. Phải có phần mở đầu, diễn biến trang trọng và kết luận rõ ràng.
- Đan xen các đoạn phỏng vấn giả định, số liệu hoặc góc nhìn văn hóa đa chiều.
- Bắt buộc chèn 4-5 ẢNH THỰC TẾ minh hoạ rải đều giữa các đoạn văn bằng cú pháp: [IMAGE: Search Query]
  (Ví dụ: [IMAGE: Vietnam Da Lat Night Market] hoặc [IMAGE: Sapa Rice Terraces] hoặc [IMAGE: Hanoi Old Quarter Food])
- Prompt ảnh BẮT BUỘC là NHỮNG TỪ KHOÁ TÌM KIẾM (Search Query) cực ngắn bằng tiếng Anh ghép lại, chỉ chứa Tên địa danh và Đặc tả để lôi ảnh thật từ Bing Search. KHÔNG viết thành câu.
- KHÔNG dùng thẻ <h1>. Chỉ dùng thẻ <h2>, <h3> đan xen văn xuôi (<p>).

TRẢ VỀ ĐÚNG FORMAT:
---TITLE---
Tiêu đề báo chí giật tít
---KEYWORD---
Short english search query for the main thumbnail image
---CONTENT---
Toàn bộ HTML nội dung bài báo
EOT;

        $payload = [
            'systemInstruction' => ['parts' => [['text' => 'Bạn là nhà báo du lịch. Bắt buộc trả về đúng định dạng ---TITLE---, ---KEYWORD---, ---CONTENT---']]],
            'contents' => [['parts' => [['text' => $promptText]]]],
            'generationConfig' => ['temperature' => 0.8, 'maxOutputTokens' => 8192, 'responseMimeType' => 'text/plain']
        ];

        // Call Gemini
        $response = Http::timeout(60)->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", $payload);

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'Lỗi kết nối API Gemini: ' . $response->body());
        }

        $data = $response->json();
        $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$rawText) {
            return redirect()->back()->with('error', 'Gemini trả về kết quả rỗng.');
        }

        preg_match('/---TITLE---\s*([\s\S]*?)\s*---KEYWORD---/', $rawText, $titleMatch);
        preg_match('/---KEYWORD---\s*([\s\S]*?)\s*---CONTENT---/', $rawText, $keywordMatch);
        preg_match('/---CONTENT---\s*([\s\S]*)$/', $rawText, $contentMatch);

        if (!$titleMatch || !$contentMatch) {
            return redirect()->back()->with('error', 'Không thể dịch mã định dạng kết quả từ Gemini.');
        }

        $title = trim($titleMatch[1]);
        $thumbKw = !empty($keywordMatch) ? trim($keywordMatch[1]) : 'vietnam travel';
        $content = trim($contentMatch[1]);

        $cleanThumbKw = urlencode(trim($thumbKw) . " Unsplash photography");
        $thumbnailPath = "https://tse1.mm.bing.net/th?q={$cleanThumbKw}&w=1200&h=800&c=7&rs=1";

        $content = preg_replace_callback('/\[IMAGE:\s*([^\]]+?)\s*\]/ui', function ($matches) {
            $prompt = trim($matches[1]);
            $encodedPrompt = urlencode($prompt . " Unsplash photography");
            $imgUrl = "https://tse1.mm.bing.net/th?q={$encodedPrompt}&w=800&h=533&c=7&rs=1";
            $caption = ucwords($prompt);
            return '<figure><img src="' . $imgUrl . '" alt="' . htmlspecialchars($prompt) . '" loading="lazy" referrerpolicy="no-referrer" style="width:100%;height:auto;display:block;margin:24px 0;border-radius:8px;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:12px;margin-bottom:24px;font-style:italic;">' . htmlspecialchars($caption) . '</figcaption></figure>';
        }, $content);

        $fakeAuthors = ['Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng', 'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang'];
        $author = $fakeAuthors[array_rand($fakeAuthors)];
        $finalTitle = substr($title, 0, 200);
        $slug = substr(Str::slug($finalTitle), 0, 100) . '-' . strtolower(Str::random(8));

        $article = new Article();
        $article->title = $finalTitle;
        $article->slug = $slug;
        $article->content = $content;
        $article->thumbnail = $thumbnailPath;
        $article->author = $author;
        $article->type = 'Mới nhất'; 
        $article->save();

        return redirect()->route('admin.dashboard')->with('success', 'Đã tự động tạo một bài viết mới từ AI thành công!');
    }
}
