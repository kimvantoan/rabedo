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
Bạn là một blogger du lịch nổi tiếng. Đầu tiên, hãy TỰ DO SÁNG TẠO MỘT CHỦ ĐỀ BLOG DU LỊCH HOÀN TOÀN MỚI, cực kỳ ngẫu nhiên và hấp dẫn.
Sau đó, hãy viết một bài blog hoàn chỉnh về chủ đề đó.

PHONG CÁCH VIẾT (BẮT BUỘC):
- Viết như một người đang kể lại hành trình, trải nghiệm thực tế — ấm áp, gần gũi, đầy cảm xúc.
- Chỉ dùng <h2> cho những tiêu đề chính chuyển giai đoạn câu chuyện (không đánh số).
- BÀI VIẾT PHẢI THẬT DÀI VÀ CHI TIẾT (TỐI THIỂU 1500-2000 TỪ).
- Bắt buộc chèn 4-5 ảnh minh hoạ rải đều trong bài viết bằng cú pháp [IMAGE: highly descriptive english image generation prompt].
  (Ví dụ: [IMAGE: a breathtaking view of terraced rice fields in Sapa during golden hour, warm sunlight, majestic mountains, highly detailed, photorealistic, cinematic lighting])
- Prompt ảnh phải hoàn toàn bằng Tiếng Anh, dài và mô tả chi tiết phong cảnh, ánh sáng, hoặc sự việc đang xảy ra trong bài.
- KHÔNG chứa thẻ <h1>.

TRẢ VỀ ĐÚNG FORMAT:
---TITLE---
Tiêu đề blog hấp dẫn
---KEYWORD---
highly descriptive english prompt for the thumbnail image
---CONTENT---
Toàn bộ HTML nội dung blog
`;

    const payload = {
      systemInstruction: { parts: [{ text: 'Bạn là blogger du lịch nổi tiếng. Bắt buộc trả về đúng định dạng ---TITLE---, ---KEYWORD---, ---CONTENT---' }] },
      contents: [{ parts: [{ text: promptText }] }],
      generationConfig: { temperature: 0.85, maxOutputTokens: 4096, responseMimeType: 'text/plain' }
    };

    const geminiRes = await fetch(
      `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${GEMINI_API_KEY}`,
      { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }
    );

    if (!geminiRes.ok) {
      return NextResponse.json({ error: 'Gemini API failed', detail: await geminiRes.text() }, { status: 502 });
    }

    const data: any = await geminiRes.json();
    const rawText = data?.candidates?.[0]?.content?.parts?.[0]?.text;
    if (!rawText) return NextResponse.json({ error: 'Gemini returned empty response' }, { status: 502 });

    const titleMatch = rawText.match(/---TITLE---\s*([\s\S]*?)\s*---KEYWORD---/);
    const keywordMatch = rawText.match(/---KEYWORD---\s*([\s\S]*?)\s*---CONTENT---/);
    const contentMatch = rawText.match(/---CONTENT---\s*([\s\S]*)$/);

    if (!titleMatch || !contentMatch) {
      return NextResponse.json({ error: 'Failed to parse Gemini response format' }, { status: 502 });
    }

    const title = titleMatch[1].trim();
    const thumbKw = keywordMatch ? keywordMatch[1].trim() : 'vietnam travel';
    let content = contentMatch[1].trim();

    // ── 2. Download thumbnail ────────────────────────────────────────────────
    const thumbnailPath = await downloadFromPollinations(thumbKw, 'thumbnail');

    // ── 3. Replace [IMAGE:xxx] placeholders — download ALL in parallel ─────────
    const regex = /\[IMAGE:\s*([^\]]+?)\s*\]/gui;
    let match;
    const tasks: { original: string; keyword: string; rawKeyword: string }[] = [];
    while ((match = regex.exec(content)) !== null) {
      tasks.push({ original: match[0], rawKeyword: match[1].trim(), keyword: match[1].trim().toLowerCase().replace(/[\s_]+/g, ',') });
    }

    // Download images sequentially to avoid Pollinations rate-limiting
    const imgPaths = [];
    for (const t of tasks) {
      const p = await downloadFromPollinations(t.keyword, 'content');
      imgPaths.push(p);
    }

    for (let i = 0; i < tasks.length; i++) {
      const { original, rawKeyword } = tasks[i];
      const imgPath = imgPaths[i];
      const newHtml = imgPath
        ? `<figure><img src="/storage/${imgPath}" alt="${rawKeyword}" style="width:100%;height:auto;display:block;margin:24px 0;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:-16px;margin-bottom:24px;">${rawKeyword.replace(/,/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</figcaption></figure>`
        : '';
      content = content.replace(original, newHtml);
    }

    // ── 4. Save to database ──────────────────────────────────────────────────
    const fakeAuthors = ['Minh Nhat', 'Thanh Huong', 'Quoc Bao', 'Lan Anh', 'Tri Dung', 'Phuong Linh', 'Hoang Nam', 'Yen Nhi', 'Duc Thinh', 'Thu Trang'];
    const author = fakeAuthors[Math.floor(Math.random() * fakeAuthors.length)];
    const slug = slugify(title) + '-' + crypto.randomBytes(4).toString('hex');

    const article = await prisma.articles.create({
      data: {
        title,
        slug,
        content,
        thumbnail: thumbnailPath ? `/storage/${thumbnailPath}` : null,
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
