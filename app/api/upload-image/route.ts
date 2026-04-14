import { NextRequest, NextResponse } from 'next/server';
import { getUserFromRequest } from '@/lib/auth';
import { writeFile, mkdir } from 'fs/promises';
import { join } from 'path';

export async function POST(request: NextRequest) {
  try {
    const user = await getUserFromRequest(request);

    if (!user) {
      return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });
    }

    const formData = await request.formData();
    const file = formData.get('image') as File | null;

    if (!file) {
      return NextResponse.json({ message: 'No image uploaded' }, { status: 400 });
    }

    const bytes = await file.arrayBuffer();
    const buffer = Buffer.from(bytes);

    // Xử lý tạo tên file random tránh trùng
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
    const originalName = file.name;
    const extension = originalName.split('.').pop() || 'tmp';
    const filename = `img_${uniqueSuffix}.${extension}`;

    // Tạo thư mục nếu chưa có
    const uploadDir = join(process.cwd(), 'public', 'storage', 'content-images');
    try {
      await mkdir(uploadDir, { recursive: true });
    } catch (e) {
      // Ignore if exists
    }

    const destPath = join(uploadDir, filename);
    await writeFile(destPath, buffer);

    // Trả về url có thể gọi ở frontend
    const url = `/storage/content-images/${filename}`;

    return NextResponse.json({ url });
  } catch (error: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: error.message }, { status: 500 });
  }
}
