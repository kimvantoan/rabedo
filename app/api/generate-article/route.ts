import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';
import * as fs from 'fs';
import * as path from 'path';
import crypto from 'crypto';

// ─── Secret Key Protection ────────────────────────────────────────────────────
// Call with: GET /api/generate-article?secret=YOUR_SECRET
const GENERATE_SECRET = process.env.GENERATE_SECRET || '';

function slugify(text: string) {
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
}

async function downloadFromPollinations(keywords: string, prefix: string): Promise<string | null> {
  const cleanKeywords = keywords.replace(/[,_]/g, ' ');
  const prompt = encodeURIComponent(cleanKeywords + " realistic, high quality landscape photography, 8k resolution, photorealistic");
  const imgUrl = `https://image.pollinations.ai/prompt/${prompt}?width=1200&height=800&nologo=true`;

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000);
    const response = await fetch(imgUrl, { signal: controller.signal });
    clearTimeout(timeoutId);

    if (!response.ok) return null;

    const arrayBuf = await response.arrayBuffer();
    const buffer = Buffer.from(arrayBuf);
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
    const filename = `${prefix}_${uniqueSuffix}.jpg`;

    const uploadDir = path.join(process.cwd(), 'public', 'storage', 'content-images');
    if (!fs.existsSync(uploadDir)) fs.mkdirSync(uploadDir, { recursive: true });

    fs.writeFileSync(path.join(uploadDir, filename), buffer);
    return `content-images/${filename}`;
  } catch {
    return null;
  }
}

export async function GET(request: NextRequest) {
  // ── Auth check ──────────────────────────────────────────────────────────────
  if (!GENERATE_SECRET) {
    return NextResponse.json({ error: 'GENERATE_SECRET not configured' }, { status: 500 });
  }
  const secret = request.nextUrl.searchParams.get('secret');
  if (secret !== GENERATE_SECRET) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const GEMINI_API_KEY = process.env.GEMINI_API_KEY || '';
  if (!GEMINI_API_KEY) {
    return NextResponse.json({ error: 'Missing GEMINI_API_KEY' }, { status: 500 });
  }

  try {
    // ── 1. Generate content via Gemini ──────────────────────────────────────
    const promptText = `
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
`;

    const payload = {
      systemInstruction: { parts: [{ text: 'Bạn là nhà báo du lịch. Bắt buộc trả về đúng định dạng ---TITLE---, ---KEYWORD---, ---CONTENT---' }] },
      contents: [{ parts: [{ text: promptText }] }],
      generationConfig: { temperature: 0.8, maxOutputTokens: 8192, responseMimeType: 'text/plain' }
    };

    const response = await fetch(
      `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${GEMINI_API_KEY}`,
      { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }
    );

    if (!response.ok) {
      return NextResponse.json({ error: 'Gemini API failed', detail: await response.text() }, { status: 502 });
    }

    const data: any = await response.json();
    const rawText = data?.candidates?.[0]?.content?.parts?.[0]?.text;
    if (!rawText) return NextResponse.json({ error: 'Gemini returned empty response' }, { status: 502 });

    const titleMatch = rawText.match(/---TITLE---\s*([\s\S]*?)\s*---KEYWORD---/);
    const keywordMatch = rawText.match(/---KEYWORD---\s*([\s\S]*?)\s*---CONTENT---/);
    const contentMatch = rawText.match(/---CONTENT---\s*([\s\S]*)$/);

    if (!titleMatch || !contentMatch) {
      return NextResponse.json({ error: 'Failed to parse Gemini response format' }, { status: 502 });
    }

    const title = titleMatch[1].trim();
    const thumbKw = keywordMatch ? keywordMatch[1].trim() : 'vietnamese travel destination beautiful';
    let content = contentMatch[1].trim();

    // ── 2. Create Thumbnail URL directly (No Download) ────────────────────────
    let cleanThumbKw = encodeURIComponent((thumbKw || 'vietnam travel').trim() + " Unsplash photography");
    const thumbnailPath = `https://tse1.mm.bing.net/th?q=${cleanThumbKw}&w=1200&h=800&c=7&rs=1`;

    // ── 3. Replace [IMAGE:xxx] placeholders directly (No Download) ─────────
    const regex = /\[IMAGE:\s*([^\]]+?)\s*\]/gui;
    content = content.replace(regex, ((match: string, p1: string) => {
      const prompt = p1.trim();
      const encodedPrompt = encodeURIComponent(prompt + " Unsplash photography");
      const imgUrl = `https://tse1.mm.bing.net/th?q=${encodedPrompt}&w=800&h=533&c=7&rs=1`;
      const caption = prompt.replace(/\b\w/g, (l: string) => l.toUpperCase());
      return `<figure><img src="${imgUrl}" alt="${prompt}" loading="lazy" referrerpolicy="no-referrer" style="width:100%;height:auto;display:block;margin:24px 0;border-radius:8px;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:12px;margin-bottom:24px;font-style:italic;">${caption}</figcaption></figure>`;
    }));

    // ── 4. Save to database ──────────────────────────────────────────────────
    const fakeAuthors = ['Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng', 'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang'];
    const author = fakeAuthors[Math.floor(Math.random() * fakeAuthors.length)];
    const finalTitle = title.substring(0, 200);
    const slug = slugify(finalTitle).substring(0, 100) + '-' + Math.random().toString(36).substring(2, 10);

    const article = await prisma.articles.create({
      data: {
        title: finalTitle,
        slug,
        content,
        thumbnail: thumbnailPath ? thumbnailPath : null,
        author,
        type: 'news',
        created_at: new Date(),
        updated_at: new Date(),
      }
    });

    return NextResponse.json({ success: true, id: Number(article.id), title: article.title, slug: article.slug });

  } catch (err: any) {
    console.error('[generate-article] Error:', err);
    return NextResponse.json({ error: err.message }, { status: 500 });
  }
}
