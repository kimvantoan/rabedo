import 'dotenv/config';
import { PrismaClient } from '@prisma/client';
import * as fs from 'fs';
import * as path from 'path';
import crypto from 'crypto';

const prisma = new PrismaClient();

const GEMINI_API_KEY = process.env.GEMINI_API_KEY || '';
const APP_URL = process.env.NEXT_PUBLIC_API_URL;

function slugify(text: string) {
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
}

async function downloadFromPollinations(keywords: string, prefix: string): Promise<string | null> {
  console.log(`Fetching AI generated image for: ${keywords} ...`);
  const cleanKeywords = keywords.replace(/[,_]/g, ' ');
  const prompt = encodeURIComponent(cleanKeywords + " realistic, high quality landscape photography, 8k resolution, photorealistic");
  const imgUrl = `https://image.pollinations.ai/prompt/${prompt}?width=1200&height=800&nologo=true`;

  try {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 60000);
    
    const response = await fetch(imgUrl, { signal: controller.signal });
    clearTimeout(timeoutId);
    
    if (!response.ok) {
      console.warn(`Failed to generate AI image for: ${keywords}`);
      return null;
    }

    const arrayBuf = await response.arrayBuffer();
    const buffer = Buffer.from(arrayBuf);
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
    const filename = `${prefix}_${uniqueSuffix}.jpg`;

    // Save to Next.js public/storage/content-images
    const uploadDir = path.join(process.cwd(), 'public', 'storage', 'content-images');
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }

    fs.writeFileSync(path.join(uploadDir, filename), buffer);
    console.log(`  → AI Image saved successfully: ${filename}`);
    return `content-images/${filename}`;
  } catch (error: any) {
    console.warn(`Image download error '${keywords}': ${error.message}`);
    return null;
  }
}

async function main() {
  console.log('Starting automated AI Article Generation (Dynamic Topic) via Next.js...');

  if (!GEMINI_API_KEY) {
    console.error('Missing GEMINI_API_KEY in .env');
    return;
  }

  const promptText = `
Bạn là một blogger du lịch nổi tiếng. Đầu tiên, hãy TỰ DO SÁNG TẠO MỘT CHỦ ĐỀ BLOG DU LỊCH HOÀN TOÀN MỚI, cực kỳ ngẫu nhiên và hấp dẫn (Ví dụ: một điểm đến hoang sơ ít người biết, quán xá bí ẩn, làng chài cổ, kinh nghiệm sinh tồn, vẻ đẹp thay đổi theo mùa, văn hoá ẩm thực độc lạ, tips tiết kiệm...).
Sau đó, hãy viết một bài blog hoàn chỉnh về chủ đề đó.

PHONG CÁCH VIẾT (BẮT BUỘC):
- Viết như một người đang kể lại hành trình, trải nghiệm thực tế — ấm áp, gần gũi, đầy cảm xúc.
- Chỉ dùng <h2> cho những tiêu đề chính chuyển giai đoạn câu chuyện (không đánh số).
- Đan xen cảm nhận cá nhân, mô tả cảnh vật sống động, chia sẻ tip thực tế.
- BÀI VIẾT PHẢI THẬT DÀI VÀ CHI TIẾT (TỐI THIỂU 1500-2000 TỪ).
- Trong nội dung phải chèn 4-5 ảnh minh họa rải đều khắp bài bằng placeholder: [IMAGE: your english keywords]
  Ví dụ: [IMAGE: sapa terraced rice fields in morning mist]
- KHÔNG chứa thẻ <h1>.

TRẢ VỀ ĐÚNG FORMAT VĂN BẢN DUY NHẤT SAU (Không dùng Markdown JSON code block, phân tách bằng đúng 3 dòng phân cách như bên dưới):
---TITLE---
Tiêu đề blog hấp dẫn
---KEYWORD---
english travel keyword thumbnail
---CONTENT---
Toàn bộ HTML nội dung blog phong cách kể chuyện
`;

  const payload = {
    systemInstruction: {
      parts: [{ text: 'Bạn là blogger du lịch nổi tiếng. Bắt buộc trả về đúng định dạng ---TITLE---, ---KEYWORD---, ---CONTENT---' }]
    },
    contents: [
      { parts: [{ text: promptText }] }
    ],
    generationConfig: {
      temperature: 0.85,
      maxOutputTokens: 4096,
      responseMimeType: 'text/plain',
    }
  };

  try {
    const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=${GEMINI_API_KEY}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      console.error(`Gemini API request failed: ${await response.text()}`);
      return;
    }

    const data: any = await response.json();
    let rawText = data?.candidates?.[0]?.content?.parts?.[0]?.text;

    if (!rawText) {
      console.error('Gemini returned empty response.');
      return;
    }

    let title = '';
    let thumbKw = 'vietnam travel';
    let content = '';

    const titleMatch = rawText.match(/---TITLE---\s*([\s\S]*?)\s*---KEYWORD---/);
    const keywordMatch = rawText.match(/---KEYWORD---\s*([\s\S]*?)\s*---CONTENT---/);
    const contentMatch = rawText.match(/---CONTENT---\s*([\s\S]*)$/);

    if (titleMatch && contentMatch) {
      title = titleMatch[1].trim();
      if (keywordMatch) thumbKw = keywordMatch[1].trim();
      content = contentMatch[1].trim();
    } else {
      console.error('Failed to parse text format from Gemini.');
      return;
    }

    // 2. Download thumbnail
    const thumbnailPath = await downloadFromPollinations(thumbKw, 'thumbnail');
    console.log(`Thumbnail saved: ${thumbnailPath || 'FAILED'}`);

    // 3. Process [IMAGE:xxx] inside content
    const regex = /\[IMAGE:\s*([^\]]+?)\s*\]/gui;
    let match;
    const replacements: { original: string, newHtml: string }[] = [];

    // Find all matches first
    while ((match = regex.exec(content)) !== null) {
      const original = match[0];
      const rawKeyword = match[1].trim();
      const keyword = rawKeyword.toLowerCase().replace(/[\s_]+/g, ',');

      const imgPath = await downloadFromPollinations(keyword, 'content');

      if (imgPath) {
        const publicUrl = `/storage/${imgPath}`;
        const newHtml = `<figure><img src="${publicUrl}" alt="${rawKeyword}" style="width:100%;height:auto;display:block;margin:24px 0;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:-16px;margin-bottom:24px;">${rawKeyword.replace(/,/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</figcaption></figure>`;
        replacements.push({ original, newHtml });
      } else {
        replacements.push({ original, newHtml: '' });
      }
    }

    // Replace all matches
    for (const rep of replacements) {
      content = content.replace(rep.original, rep.newHtml);
    }

    // 4. Save to Database using Prisma
    const fakeAuthors = [
      'Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng',
      'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang',
      'Ngọc Hà', 'Tiến Phong', 'Bảo Châu', 'Việt Anh', 'Hồng Nhung'
    ];
    const author = fakeAuthors[Math.floor(Math.random() * fakeAuthors.length)];
    const slug = slugify(title) + '-' + crypto.randomBytes(4).toString('hex');

    const article = await prisma.articles.create({
      data: {
        title: title,
        slug: slug,
        content: content,
        thumbnail: thumbnailPath ? `/storage/${thumbnailPath}` : null,
        author: author,
        type: 'news',
        created_at: new Date(),
        updated_at: new Date(),
      }
    });

    console.log(`✅ Article created! ID: ${article.id} — ${article.title}`);

  } catch (err: any) {
    console.error('Error in automated generation:', err);
  } finally {
    await prisma.$disconnect();
  }
}

main();
