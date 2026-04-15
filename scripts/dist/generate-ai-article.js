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
Object.defineProperty(exports, "__esModule", { value: true });
require("dotenv/config");
const client_1 = require("@prisma/client");
const fs = __importStar(require("fs"));
const path = __importStar(require("path"));
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
        systemInstruction: {
            parts: [{ text: 'Bạn là nhà báo du lịch. Bắt buộc trả về đúng định dạng ---TITLE---, ---KEYWORD---, ---CONTENT---' }]
        },
        contents: [
            { parts: [{ text: promptText }] }
        ],
        generationConfig: {
            temperature: 0.8,
            maxOutputTokens: 8192,
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
        let thumbKw = 'vietnamese travel destination beautiful';
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
        // ── 2. Create Thumbnail URL directly (No Download) ────────────────────────
        let cleanThumbKw = encodeURIComponent((thumbKw || 'vietnam travel').trim() + " Unsplash photography");
        const thumbnailPath = `https://tse1.mm.bing.net/th?q=${cleanThumbKw}&w=1200&h=800&c=7&rs=1`;
        // ── 3. Replace [IMAGE:xxx] placeholders directly (No Download) ─────────
        const regex = /\[IMAGE:\s*([^\]]+?)\s*\]/gui;
        content = content.replace(regex, (match, p1) => {
            const prompt = p1.trim();
            const encodedPrompt = encodeURIComponent(prompt + " Unsplash photography");
            const imgUrl = `https://tse1.mm.bing.net/th?q=${encodedPrompt}&w=800&h=533&c=7&rs=1`;
            const caption = prompt.replace(/\b\w/g, (l) => l.toUpperCase());
            return `<figure><img src="${imgUrl}" alt="${prompt}" loading="lazy" referrerpolicy="no-referrer" style="width:100%;height:auto;display:block;margin:24px 0;border-radius:8px;" /><figcaption style="text-align:center;font-size:13px;color:#888;margin-top:12px;margin-bottom:24px;font-style:italic;">${caption}</figcaption></figure>`;
        });
        // 4. Save to Database using Prisma
        const fakeAuthors = [
            'Minh Nhật', 'Thanh Hương', 'Quốc Bảo', 'Lan Anh', 'Trí Dũng',
            'Phương Linh', 'Hoàng Nam', 'Yến Nhi', 'Đức Thịnh', 'Thu Trang',
            'Ngọc Hà', 'Tiến Phong', 'Bảo Châu', 'Việt Anh', 'Hồng Nhung'
        ];
        const author = fakeAuthors[Math.floor(Math.random() * fakeAuthors.length)];
        const finalTitle = title.substring(0, 200);
        const slug = slugify(finalTitle).substring(0, 100) + '-' + Math.random().toString(36).substring(2, 10);
        const article = await prisma.articles.create({
            data: {
                title: finalTitle,
                slug: slug,
                content: content,
                thumbnail: thumbnailPath ? thumbnailPath : null,
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
