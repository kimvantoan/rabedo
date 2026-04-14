<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateAiArticle extends Command
{
    protected $signature = 'ai:generate-article';
    protected $description = 'Automatically generates a 3000-word daily travel article with images via Gemini AI';

    /**
     * Danh sách chủ đề du lịch kèm từ khóa ảnh tiếng Anh cho Unsplash
     */
    private array $topics = [
        ['topic' => "Top 5 khách sạn nổi tiếng và sang trọng nhất tại Hà Nội",        'keywords' => 'hanoi,hotel,vietnam'],
        ['topic' => "Top 5 địa điểm đáng đi nhất ở Đà Nẵng không thể bỏ lỡ",          'keywords' => 'danang,vietnam,beach'],
        ['topic' => "Kinh nghiệm du lịch bụi Sapa 3 ngày 2 đêm chi tiết nhất",         'keywords' => 'sapa,vietnam,mountain'],
        ['topic' => "Khám phá tinh hoa ẩm thực đường phố Huế",                          'keywords' => 'hue,vietnam,food'],
        ['topic' => "Review chi tiết các khu nghỉ dưỡng 5 sao ở Phú Quốc",             'keywords' => 'phuquoc,resort,island'],
        ['topic' => "Khám phá nét đẹp hoang sơ và kỳ bí của Côn Đảo",                  'keywords' => 'condao,vietnam,island'],
        ['topic' => "Cẩm nang phượt xe máy Mù Cang Chải mùa lúa chín",                 'keywords' => 'vietnam,ricefield,mountain'],
        ['topic' => "Top 7 quán cafe view đẹp lung linh nhất tại Đà Lạt",              'keywords' => 'dalat,cafe,vietnam'],
        ['topic' => "Bí kíp săn mây Tà Xùa cho người mới bắt đầu",                     'keywords' => 'vietnam,cloud,mountain'],
        ['topic' => "Cẩm nang du lịch Vịnh Hạ Long trọn gói tiết kiệm",               'keywords' => 'halong,bay,vietnam'],
        ['topic' => "Hà Giang: Cẩm nang sinh tồn và điểm check-in cực đỉnh",           'keywords' => 'hagiang,vietnam,road'],
        ['topic' => "Những homestay hoài cổ và vintage nhất tại Hội An",                'keywords' => 'hoian,vietnam,lantern'],
        ['topic' => "Khám phá Đảo ngọc Nam Du: Lịch trình chi tiết và món ngon",       'keywords' => 'vietnam,island,sea'],
        ['topic' => "Bản đồ check-in các điểm ngắm san hô đẹp nhất Nha Trang",         'keywords' => 'nhatrang,beach,coral'],
        ['topic' => "Review thực tế khu sinh thái Tràng An - Ninh Bình mùa lễ hội",    'keywords' => 'ninhbinh,vietnam,boat'],
    ];

    public function handle(): int
    {
        $this->info('Starting automated AI Article Generation...');

        $selected   = $this->topics[array_rand($this->topics)];
        $topic      = $selected['topic'];
        $imgKeyword = $selected['keywords'];

        $this->info("Selected Topic: $topic");

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            $this->error('Missing GEMINI_API_KEY in api/.env');
            return Command::FAILURE;
        }

        // ─── 1. Gọi Gemini tạo bài viết ───────────────────────────────────────
        $prompt = <<<TEXT
Bạn là một blogger du lịch nổi tiếng, viết bài theo phong cách kể chuyện tự nhiên, chân thực và truyền cảm hứng về chủ đề: "{$topic}".

PHONG CÁCH VIẾT (BẮT BUỘC):
- Viết như một người đang kể lại hành trình, trải nghiệm thực tế — ấm áp, gần gũi, đầy cảm xúc.
- KHÔNG viết theo dạng liệt kê số thứ tự (1., 2., 3...). KHÔNG dùng bullet list (-) và (<ul><li>) quá nhiều.
- Nội dung chủ yếu là văn xuôi chảy, các đoạn <p> kể chuyện tự nhiên.
- Chỉ dùng <h2> cho những tiêu đề chính chuyển giai đoạn câu chuyện (không đánh số).
- Chỉ dùng <h3> khi cần nhấn mạnh một điểm đặc biệt trong giai đoạn đó.
- Đan xen cảm nhận cá nhân, mô tả cảnh vật sống động, chia sẻ tip thực tế một cách tự nhiên trong đoạn văn.
- Độ dài khoảng 3000 từ. Chất lượng quan trọng hơn độ dài.
- Trong nội dung, chèn 3-4 ảnh minh họa bằng placeholder: [IMAGE:english_keyword]
  Ví dụ: [IMAGE:halong_bay_sunset] hoặc [IMAGE:sapa_rice_terrace]
  Đặt ngay sau đoạn văn mô tả khung cảnh tương ứng.
- KHÔNG chứa thẻ <h1>.

TRẢ VỀ JSON DUY NHẤT (không thêm bất kỳ text nào bên ngoài JSON):
{
  "title": "Tiêu đề blog hấp dẫn, có cảm xúc",
  "thumbnail_keyword": "english_travel_keyword",
  "content": "Toàn bộ HTML nội dung blog phong cách kể chuyện"
}
TEXT;

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => 'Bạn là blogger du lịch nổi tiếng với phong cách viết chân thực, cảm xúc, như người bạn đang kể câu chuyện hành trình của mình. Bắt buộc trả về đúng JSON theo yêu cầu.']]
            ],
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature'      => 0.85,
                'responseMimeType' => 'application/json',
            ]
        ];

        $response = Http::timeout(120)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
            $payload
        );

        if (!$response->successful()) {
            $this->error("Gemini API request failed: " . $response->body());
            return Command::FAILURE;
        }

        $data    = $response->json();
        $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$rawText) {
            $this->error("Gemini returned empty response.");
            return Command::FAILURE;
        }

        $result = null;
        // Gemini đôi khi trả về JSON bọc trong markdown ``` — cần bóc vỏ
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($rawText));
        $cleaned = preg_replace('/\s*```$/i', '', trim($cleaned));
        $result  = json_decode($cleaned, true);

        if (!isset($result['title'], $result['content'])) {
            $this->error("Failed to parse title/content from Gemini JSON.");
            $this->error("Raw (first 500 chars): " . substr($rawText, 0, 500));
            return Command::FAILURE;
        }

        $title      = $result['title'];
        $content    = $result['content'];
        $thumbKw    = $result['thumbnail_keyword'] ?? $imgKeyword;

        // ─── 2. Tải và lưu ảnh thumbnail ──────────────────────────────────────
        $thumbnail = $this->downloadAndSaveImage($thumbKw, 'thumbnail');
        $this->info("Thumbnail saved: " . ($thumbnail ?? 'FAILED'));

        // ─── 3. Xử lý [IMAGE:keyword] → tải ảnh + thay bằng <img> tag ─────────
        $appUrl = rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/');
        $content = preg_replace_callback(
            '/\[IMAGE:\s*([a-zA-Z0-9_\-\s]+?)\s*\]/',
            function (array $matches) use ($appUrl) {
                // Chuẩn hóa keyword: lowercase, bỏ khoảng trắng dư thừa, thay space bằng dấu phẩy
                $rawKeyword = trim($matches[1]);
                $keyword    = strtolower(preg_replace('/[\s_]+/', ',', $rawKeyword));
                $imgPath    = $this->downloadAndSaveImage($keyword, 'content');

                if ($imgPath) {
                    $publicUrl = $appUrl . '/storage/' . $imgPath;
                    return '<figure><img src="' . $publicUrl . '" alt="' . htmlspecialchars($rawKeyword) . '" style="width:100%;height:auto;display:block;margin:24px 0;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:-16px;margin-bottom:24px;">' . ucwords(str_replace(',', ' ', $keyword)) . '</figcaption></figure>';
                }

                return '';
            },
            $content
        );

        // ─── 4. Lưu bài viết vào Database ─────────────────────────────────────
        $fakeAuthors = [
            'Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng',
            'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang',
            'Ngọc Hà', 'Tiến Phong', 'Bảo Châu', 'Việt Anh', 'Hồng Nhung',
        ];
        $author = $fakeAuthors[array_rand($fakeAuthors)];

        $article = Article::create([
            'title'     => $title,
            'slug'      => Str::slug($title) . '-' . uniqid(),
            'content'   => $content,
            'thumbnail' => $thumbnail ? Storage::url($thumbnail) : null,
            'author'    => $author,
            'type'      => 'news',
        ]);

        $this->info("✅ Article created! ID: {$article->id} — {$article->title}");
        return Command::SUCCESS;
    }

    /**
     * Tải ảnh liên quan từ Wikimedia Commons (miễn phí, không cần API key).
     * Bước 1: Tìm kiếm ảnh theo keyword
     * Bước 2: Lấy URL thật của ảnh rồi download
     */
    private function downloadAndSaveImage(string $keywords, string $prefix = 'img'): ?string
    {
        try {
            // Nếu có Unsplash key thì dùng Unsplash (ảnh đẹp hơn)
            $unsplashKey = env('UNSPLASH_ACCESS_KEY');
            if ($unsplashKey) {
                return $this->downloadFromUnsplash($keywords, $prefix, $unsplashKey);
            }

            // Không có key → dùng Wikimedia Commons (miễn phí, không cần key)
            return $this->downloadFromWikimedia($keywords, $prefix);

        } catch (\Throwable $e) {
            $this->warn("Image download error '$keywords': " . $e->getMessage());
            return null;
        }
    }

    private function downloadFromUnsplash(string $keywords, string $prefix, string $key): ?string
    {
        $searchResponse = Http::timeout(15)
            ->withHeaders(['Authorization' => "Client-ID {$key}"])
            ->get('https://api.unsplash.com/search/photos', [
                'query' => $keywords, 'per_page' => 5, 'orientation' => 'landscape',
            ]);
        if (!$searchResponse->successful()) return null;
        $results = $searchResponse->json()['results'] ?? [];
        if (empty($results)) return null;
        $imgUrl = $results[array_rand($results)]['urls']['regular'] ?? null;
        if (!$imgUrl) return null;
        $imgResponse = Http::timeout(20)->get($imgUrl);
        if (!$imgResponse->successful()) return null;
        $filename = "content-images/{$prefix}_" . Str::random(12) . ".jpg";
        Storage::disk('public')->put($filename, $imgResponse->body());
        return $filename;
    }

    private function downloadFromWikimedia(string $keywords, string $prefix): ?string
    {
        // Bước 1: Tìm file ảnh trên Wikimedia Commons
        $searchRes = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'RabedoBlog/1.0 (contact@rabedo.com)'])
            ->get('https://commons.wikimedia.org/w/api.php', [
                'action'     => 'query',
                'list'       => 'search',
                'srnamespace'=> 6,  // namespace 6 = File
                'srsearch'   => $keywords . ' vietnam travel landscape',
                'srlimit'    => 8,
                'format'     => 'json',
            ]);

        if (!$searchRes->successful()) {
            $this->warn("Wikimedia search failed for: $keywords");
            return null;
        }

        $hits = $searchRes->json()['query']['search'] ?? [];
        if (empty($hits)) {
            $this->warn("No Wikimedia results for: $keywords");
            return null;
        }

        // Bước 2: Lấy URL thật của ảnh từ tên file
        shuffle($hits);
        foreach ($hits as $hit) {
            $fileName = $hit['title']; // vd: "File:Halong_bay.jpg"

            $infoRes = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'RabedoBlog/1.0'])
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action'  => 'query',
                    'titles'  => $fileName,
                    'prop'    => 'imageinfo',
                    'iiprop'  => 'url|mime',
                    'iiurlwidth' => 1200,
                    'format'  => 'json',
                ]);

            if (!$infoRes->successful()) continue;

            $pages   = $infoRes->json()['query']['pages'] ?? [];
            $page    = reset($pages);
            $imgInfo = $page['imageinfo'][0] ?? null;

            if (!$imgInfo) continue;

            $mime   = $imgInfo['mime'] ?? '';
            $imgUrl = $imgInfo['thumburl'] ?? $imgInfo['url'] ?? null;

            // Chỉ lấy ảnh JPEG/PNG, bỏ qua SVG/OGG/PDF
            if (!$imgUrl || !str_starts_with($mime, 'image/') || in_array($mime, ['image/svg+xml', 'image/gif'])) {
                continue;
            }

            $imgResponse = Http::timeout(20)->get($imgUrl);
            if (!$imgResponse->successful()) continue;

            $ext = $mime === 'image/png' ? 'png' : 'jpg';
            $filename = "content-images/{$prefix}_" . Str::random(12) . ".{$ext}";
            Storage::disk('public')->put($filename, $imgResponse->body());

            $this->info("  → Image saved from Wikimedia: $fileName");
            return $filename;
        }

        $this->warn("Could not download any image for: $keywords");
        return null;
    }
}
