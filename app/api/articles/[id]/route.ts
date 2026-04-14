import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';
import { getUserFromRequest } from '@/lib/auth';
import { writeFile, mkdir, unlink } from 'fs/promises';
import { join } from 'path';

export async function GET(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const user = await getUserFromRequest(request);
    if (!user) return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });

    const { id } = await params;
    const article = await prisma.articles.findUnique({ where: { id: Number(id) } });

    if (!article) return NextResponse.json({ message: 'Not found' }, { status: 404 });

    return NextResponse.json({
      ...article,
      id: Number(article.id),
      created_at: article.created_at?.toISOString() ?? null,
      updated_at: article.updated_at?.toISOString() ?? null,
    });
  } catch (err: any) {
    return NextResponse.json({ message: 'Error', error: err.message }, { status: 500 });
  }
}

export async function PUT(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const user = await getUserFromRequest(request);
    if (!user) return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });

    const { id } = await params;
    const article = await prisma.articles.findUnique({ where: { id: Number(id) } });
    if (!article) return NextResponse.json({ message: 'Not found' }, { status: 404 });

    const formData = await request.formData();
    const title = formData.get('title') as string | null;
    const content = formData.get('content') as string | null;
    const thumbnailFile = formData.get('thumbnail') as File | null;

    let updateData: any = { updated_at: new Date() };
    if (title) updateData.title = title;
    if (content) updateData.content = content;

    if (thumbnailFile && thumbnailFile.name) {
      // Delete old file
      if (article.thumbnail) {
        try {
          const oldPath = join(process.cwd(), 'public', article.thumbnail);
          await unlink(oldPath);
        } catch (e) {}
      }

      const bytes = await thumbnailFile.arrayBuffer();
      const buffer = Buffer.from(bytes);
      const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
      const extension = thumbnailFile.name.split('.').pop() || 'tmp';
      const filename = `thumb_${uniqueSuffix}.${extension}`;
      const uploadDir = join(process.cwd(), 'public', 'storage', 'thumbnails');
      
      try { await mkdir(uploadDir, { recursive: true }); } catch (e) {}
      await writeFile(join(uploadDir, filename), buffer);
      
      updateData.thumbnail = `/storage/thumbnails/${filename}`;
    }

    const updated = await prisma.articles.update({
      where: { id: Number(id) },
      data: updateData,
    });

    return NextResponse.json({
      message: 'Đã cập nhật bài viết thành công!',
      data: { ...updated, id: Number(updated.id) }
    });
  } catch (err: any) {
    return NextResponse.json({ message: 'Error', error: err.message }, { status: 500 });
  }
}

export async function DELETE(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const user = await getUserFromRequest(request);
    if (!user) return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });

    const { id } = await params;
    const article = await prisma.articles.findUnique({ where: { id: Number(id) } });
    
    if (!article) return NextResponse.json({ message: 'Not found' }, { status: 404 });

    if (article.thumbnail) {
      try {
        const oldPath = join(process.cwd(), 'public', article.thumbnail);
        await unlink(oldPath);
      } catch (e) {}
    }

    await prisma.articles.delete({ where: { id: Number(id) } });

    return NextResponse.json({ message: 'Đã xóa bài viết khỏi hệ thống!' });
  } catch (err: any) {
    return NextResponse.json({ message: 'Error', error: err.message }, { status: 500 });
  }
}
