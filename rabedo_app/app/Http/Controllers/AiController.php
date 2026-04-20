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
        if (!\Illuminate\Support\Facades\Auth::check() && !app()->runningInConsole() && !in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $geminiKey = env('GEMINI_API_KEY', '');
        if (!$geminiKey) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bạn chưa cấu hình GEMINI_API_KEY trong file .env'], 500);
            }
            return redirect()->back()->with('error', 'Bạn chưa cấu hình GEMINI_API_KEY trong file .env');
        }

        $locations = ['Da Lat', 'Sapa', 'Phu Quoc', 'Hoi An', 'Da Nang', 'Ha Long Bay', 'Ninh Binh', 'Ha Giang', 'Mang Den', 'Phu Yen', 'Quy Nhon', 'Con Dao', 'Ho Chi Minh City', 'Hanoi'];
        $location = $locations[array_rand($locations)];

        $themes = [
            'Write a travel review of [LOC] from the perspective of someone who has actually been there. Structure: first impressions -> highlights -> areas for improvement -> advice. Include personal emotions.',
            'Write an article "10 things you must know before traveling to [LOC]". Focus on: culture, safety tips, budget hacks, ideal timing.',
            'Write an article "What to eat when visiting [LOC]". List 8-10 local specialties: describe the taste, popular places, prices. Add a section "avoiding tourist traps".',
            'Write a budget travel guide for [LOC]. Include: cheap eats, transportation, affordable accommodation, free activities. Include estimated costs.',
            'Write an article "Top most beautiful photography spots in [LOC]". Describe the scenery, ideal angles, golden hour times, and basic photography tips for each place.',
            'Write a guide on solo traveling to [LOC]. Include: safety, meeting locals, communal accommodation, and the emotional aspect of traveling alone.',
            'Write an article "Hidden gems in [LOC]". Introduce 6-8 spots not found in mainstream guidebooks. How to find them, why they are special.',
            'Create a detailed travel itinerary for [LOC] covering morning-noon-afternoon-evening, local eateries, transportation, and insider tips.'
        ];
        
        $selectedTheme = str_replace('[LOC]', $location, $themes[array_rand($themes)]);

        $promptText = <<<EOT
Act as a professional travel expert and travel blogger.
Task: {$selectedTheme}

WRITING STYLE:
- MOST IMPORTANT: Focus 100% on the given topic. DO NOT ramble off-topic.
- MANDATORY: THE ENTIRE ARTICLE MUST BE WRITTEN IN ENGLISH. NO VIETNAMESE OR OTHER LANGUAGES.
- The tone should be friendly, authentic, and like a friend telling a story. Suitable for all types of travelers.
- Write a professional blog post/article (USE PROPER HTML HEADINGS <h2>, <h3> intertwined with paragraph texts <p>).
- THE ARTICLE MUST BE DEEP AND DETAILED BUT CONCISE (Minimum 1000 words), use long paragraphs. Do not use empty promotional language.

IMAGE INSERTION RULES (MANDATORY):
- Insert about 2-3 illustrative images evenly distributed among the paragraphs using the syntax: [IMAGE: Search Query]
- (Example: [IMAGE: {$location} travel beautiful] or [IMAGE: {$location} food local])
- The image prompt MUST be VERY SHORT ENGLISH SEARCH KEYWORDS to pull stock images from Unsplash. DO NOT write full sentences.
- NEVER use the <h1> tag.

RETURN THE EXACT FORMAT BELOW AND CONTAIN NO OTHER EXTERNAL TEXT OR APOLOGIES:
---TITLE---
A very catchy English title for the article
---KEYWORD---
Short english keyword for sunset travel {$location}
---CONTENT---
The complete HTML content of the article written in English
EOT;

        $payload = [
            'systemInstruction' => ['parts' => [['text' => 'You are a professional English travel journalist. You must strictly return the format ---TITLE---, ---KEYWORD---, ---CONTENT--- and write completely in English.']]],
            'contents' => [['parts' => [['text' => $promptText]]]],
            'generationConfig' => ['temperature' => 0.8, 'maxOutputTokens' => 8192, 'responseMimeType' => 'text/plain']
        ];

        // Call Gemini
        $response = Http::timeout(60)->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", $payload);

        if (!$response->successful()) {
            if ($request->expectsJson()) return response()->json(['error' => 'Lỗi kết nối API Gemini: ' . $response->body()], 500);
            return redirect()->back()->with('error', 'Lỗi kết nối API Gemini: ' . $response->body());
        }

        $data = $response->json();
        $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$rawText) {
            if ($request->expectsJson()) return response()->json(['error' => 'Gemini trả về kết quả rỗng.'], 500);
            return redirect()->back()->with('error', 'Gemini trả về kết quả rỗng.');
        }

        preg_match('/---TITLE---\s*([\s\S]*?)\s*---KEYWORD---/', $rawText, $titleMatch);
        preg_match('/---KEYWORD---\s*([\s\S]*?)\s*---CONTENT---/', $rawText, $keywordMatch);
        preg_match('/---CONTENT---\s*([\s\S]*)$/', $rawText, $contentMatch);

        if (!$titleMatch || !$contentMatch) {
            if ($request->expectsJson()) return response()->json(['error' => 'Không thể dịch mã định dạng kết quả từ Gemini.'], 500);
            return redirect()->back()->with('error', 'Không thể dịch mã định dạng kết quả từ Gemini.');
        }

        $title = trim($titleMatch[1]);
        $thumbKw = !empty($keywordMatch) ? trim($keywordMatch[1]) : 'vietnam travel';
        $content = trim($contentMatch[1]);

        $cleanThumbKw = trim($thumbKw);
        $baseThumbnailUrl = $this->fetchUnsplashImage($cleanThumbKw, 1200, 800);
        $thumbnailPath = route('proxy.image', ['url' => $baseThumbnailUrl], false);

        $content = preg_replace_callback('/\[IMAGE:\s*([^\]]+?)\s*\]/ui', function ($matches) {
            $prompt = trim($matches[1]);
            $baseImgUrl = $this->fetchUnsplashImage($prompt, 800, 533);
            $imgUrl = route('proxy.image', ['url' => $baseImgUrl], false);
            $caption = ucwords($prompt);
            return '<figure><img src="' . $imgUrl . '" alt="' . htmlspecialchars($prompt) . '" loading="lazy" referrerpolicy="no-referrer" style="width:100%;height:auto;display:block;margin:24px 0;border-radius:0;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:12px;margin-bottom:24px;font-style:italic;">' . htmlspecialchars($caption) . '</figcaption></figure>';
        }, $content);

        $fakeAuthors = [
            'Arthur Pendelton', 'George Harrington', 'James Kensington', 'William Ashford',
            'Oliver Croft', 'Benjamin Sterling', 'Harry Davies', 'Thomas Redcliff',
            'Samuel Kingsley', 'Jack Montgomery', 'Amelia Thorne', 'Olivia Blackwood',
            'Eleanor Stanhope', 'Charlotte Bradley', 'Emily Fairburn', 'Isla Chambers',
            'Poppy Lancaster', 'Ava Pemberton', 'Isabella Carlisle', 'Jessica Whitmore'
        ];
        $author = $fakeAuthors[array_rand($fakeAuthors)];
        $finalTitle = substr($title, 0, 200);
        $slug = substr(Str::slug($finalTitle), 0, 100) . '-' . strtolower(Str::random(8));

        $article = new Article();
        $article->title = $finalTitle;
        $article->slug = $slug;
        $article->content = $content;
        $article->thumbnail = $thumbnailPath;
        $article->author = $author;
        $article->type = 'Latest'; 
        $article->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã tự động tạo một bài viết mới từ AI thành công!'
            ]);
        }
        return redirect()->route('admin.dashboard')->with('success', 'Đã tự động tạo một bài viết mới từ AI thành công!');
    }

    public function imageProxy(Request $request)
    {
        $url = $request->query('url');
        if (!$url || !str_starts_with($url, 'http')) {
            return abort(404);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->timeout(30)->get($url);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?? '';
                if (str_starts_with($contentType, 'image/')) {
                    return response($response->body(), 200)
                        ->header('Content-Type', $contentType)
                        ->header('Cache-Control', 'public, max-age=604800');
                }
            }
        } catch (\Exception $e) {
            // fallback
        }

        return redirect($url); // Fallback to direct URL if proxy fails
    }

    private function fetchUnsplashImage($query, $width, $height)
    {
        $vietnamFallbacks = [
            'https://images.unsplash.com/photo-1503300806790-80dde376911e?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1525385313625-862284db65c6?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1576788134819-397d7cd14e5f?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1577791063779-c5803119b709?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1652960414428-f1ca14d24b06?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1675860571831-f932501df328?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1707292098561-a251b9aa4014?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1707292098544-755fa220732b?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1726346215358-d8e2f5898848?w=1200&q=80&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1755657763706-cb23214f2d0e?w=1200&q=80&auto=format&fit=crop'
        ];
        $defaultFallback = $vietnamFallbacks[array_rand($vietnamFallbacks)];
        
        $apiKey = env('UNSPLASH_ACCESS_KEY');
        
        if (!$apiKey) {
            return $defaultFallback;
        }

        try {
            $response = Http::timeout(10)->get("https://api.unsplash.com/photos/random", [
                'query' => $query,
                'orientation' => $width > $height ? 'landscape' : 'portrait',
                'client_id' => $apiKey,
            ]);

            if ($response->successful()) {
                $urls = $response->json('urls');
                return $urls['regular'] ?? ($urls['full'] ?? ($urls['raw'] ?? $defaultFallback));
            }
        } catch (\Exception $e) {
            // connection error
        }

        return $defaultFallback;
    }
}
