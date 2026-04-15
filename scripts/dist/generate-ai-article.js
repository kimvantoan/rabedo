"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
require("dotenv/config");
const client_1 = require("@prisma/client");
const fs = __importStar(require("fs"));
const path = __importStar(require("path"));
const crypto_1 = __importDefault(require("crypto"));
// ─── Lock File Guard ──────────────────────────────────────────────────────────
// Prevents multiple instances from running concurrently (e.g. overlapping crons)
// Use __dirname so paths are correct regardless of cron's working directory
const SCRIPT_DIR = path.resolve(__dirname);
const APP_ROOT = path.resolve(SCRIPT_DIR, '..');
const LOCK_FILE = path.join(SCRIPT_DIR, '.generate-ai-article.lock');
const LOCK_TIMEOUT_MS = 10 * 60 * 1000; // 10 minutes — kill stale lock
function acquireLock() {
    if (fs.existsSync(LOCK_FILE)) {
        const stat = fs.statSync(LOCK_FILE);
        const age = Date.now() - stat.mtimeMs;
        if (age < LOCK_TIMEOUT_MS) {
            console.log(`[LOCK] Another instance is running (lock age: ${Math.round(age / 1000)}s). Exiting.`);
            return false;
        }
        console.log(`[LOCK] Stale lock detected (age: ${Math.round(age / 1000)}s). Overriding.`);
    }
    fs.writeFileSync(LOCK_FILE, String(process.pid));
    return true;
}
function releaseLock() {
    try {
        if (fs.existsSync(LOCK_FILE))
            fs.unlinkSync(LOCK_FILE);
    }
    catch { }
}
// ─────────────────────────────────────────────────────────────────────────────
const prisma = new client_1.PrismaClient();
const GEMINI_API_KEY = process.env.GEMINI_API_KEY || '';
function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}
async function downloadFromPollinations(keywords, prefix) {
    console.log(`Fetching AI generated image for: ${keywords} ...`);
    const cleanKeywords = keywords.replace(/[,_]/g, ' ');
    const prompt = encodeURIComponent(cleanKeywords + " realistic, high quality landscape photography, 8k resolution, photorealistic");
    const imgUrl = `https://image.pollinations.ai/prompt/${prompt}?width=1200&height=800&nologo=true`;
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30s timeout (reduced from 60s)
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
        // Use APP_ROOT (derived from __dirname) to get correct path in cron context
        const uploadDir = path.join(APP_ROOT, 'public', 'storage', 'content-images');
        if (!fs.existsSync(uploadDir)) {
            fs.mkdirSync(uploadDir, { recursive: true });
        }
        fs.writeFileSync(path.join(uploadDir, filename), buffer);
        console.log(`  → AI Image saved successfully: ${filename}`);
        return `content-images/${filename}`;
    }
    catch (error) {
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
- Bắt buộc chèn 4-5 ảnh minh hoạ rải đều trong bài viết bằng cú pháp [IMAGE: highly descriptive english image generation prompt].
  (Ví dụ: [IMAGE: a breathtaking view of terraced rice fields in Sapa during golden hour, warm sunlight, majestic mountains, highly detailed, photorealistic, cinematic lighting])
- Prompt ảnh phải hoàn toàn bằng Tiếng Anh, dài và mô tả chi tiết phong cảnh, ánh sáng, hoặc sự việc đang xảy ra trong bài.
- KHÔNG chứa thẻ <h1>.

TRẢ VỀ ĐÚNG FORMAT VĂN BẢN DUY NHẤT SAU (Không dùng Markdown JSON code block, phân tách bằng đúng 3 dòng phân cách như bên dưới):
---TITLE---
Tiêu đề blog hấp dẫn
---KEYWORD---
highly descriptive english prompt for the thumbnail image
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
        const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        if (!response.ok) {
            console.error(`Gemini API request failed: ${await response.text()}`);
            return;
        }
        const data = await response.json();
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
            if (keywordMatch)
                thumbKw = keywordMatch[1].trim();
            content = contentMatch[1].trim();
        }
        else {
            console.error('Failed to parse text format from Gemini.');
            return;
        }
        // 2. Download thumbnail
        const thumbnailPath = await downloadFromPollinations(thumbKw, 'thumbnail');
        console.log(`Thumbnail saved: ${thumbnailPath || 'FAILED'}`);
        // 3. Process [IMAGE:xxx] inside content — all downloads in parallel
        const regex = /\[IMAGE:\s*([^\]]+?)\s*\]/gui;
        let match;
        const tasks = [];
        while ((match = regex.exec(content)) !== null) {
            tasks.push({ original: match[0], rawKeyword: match[1].trim(), keyword: match[1].trim().toLowerCase().replace(/[\s_]+/g, ',') });
        }
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
        // 4. Save to Database using Prisma
        const fakeAuthors = [
            'Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng',
            'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang',
            'Ngọc Hà', 'Tiến Phong', 'Bảo Châu', 'Việt Anh', 'Hồng Nhung'
        ];
        const author = fakeAuthors[Math.floor(Math.random() * fakeAuthors.length)];
        const slug = slugify(title) + '-' + crypto_1.default.randomBytes(4).toString('hex');
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
    }
    catch (err) {
        console.error('Error in automated generation:', err);
    }
    finally {
        await prisma.$disconnect();
    }
}
// ─── Lock-guarded entry point ─────────────────────────────────────────────────
// Always release lock on exit (crash, SIGTERM, SIGINT, etc.)
process.on('exit', releaseLock);
process.on('SIGTERM', () => { releaseLock(); process.exit(0); });
process.on('SIGINT', () => { releaseLock(); process.exit(0); });
if (!acquireLock()) {
    process.exit(0); // Another instance running — exit cleanly without spawning anything
}
main().finally(releaseLock);
