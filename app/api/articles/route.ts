import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';
import { getUserFromRequest } from '@/lib/auth';
import { writeFile, mkdir } from 'fs/promises';
import { join } from 'path';

function slugify(text: string) {
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}

export async function GET(request: NextRequest) {
  try {
    const user = await getUserFromRequest(request);
    if (!user) return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });

    const { searchParams } = new URL(request.url);
    const type = searchParams.get('type');
    const search = searchParams.get('search');
    const page = parseInt(searchParams.get('page') || '1');
    const perPage = 10;

    let where: any = {};
    if (type) where.type = type;
    if (search) where.title = { contains: search };

    const total = await prisma.articles.count({ where });
    const articles = await prisma.articles.findMany({
      where,
      orderBy: { created_at: 'desc' },
      skip: (page - 1) * perPage,
      take: perPage,
    });
    const safeArticles = articles.map((a: any) => ({
      ...a,
      id: Number(a.id),
      created_at: a.created_at?.toISOString() ?? null,
      updated_at: a.updated_at?.toISOString() ?? null,
    }));

    return NextResponse.json({
      data: safeArticles,
      current_page: page,
      per_page: perPage,
      total,
      last_page: Math.ceil(total / perPage),
    });
  } catch (err: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: err.message }, { status: 500 });
  }
}

export async function POST(request: NextRequest) {
  try {
    const user = await getUserFromRequest(request);
    if (!user) return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });

    const formData = await request.formData();
    const title = formData.get('title') as string;
    const content = formData.get('content') as string;
    const thumbnailFile = formData.get('thumbnail') as File | null;

    if (!title || !content) {
      return NextResponse.json({ message: 'Missing title or content' }, { status: 400 });
    }

    let thumbnailUrl = null;
    if (thumbnailFile && thumbnailFile.name) {
      const bytes = await thumbnailFile.arrayBuffer();
      const buffer = Buffer.from(bytes);
      const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
      const extension = thumbnailFile.name.split('.').pop() || 'tmp';
      const filename = `thumb_${uniqueSuffix}.${extension}`;
      const uploadDir = join(process.cwd(), 'public', 'storage', 'thumbnails');
      
      try { await mkdir(uploadDir, { recursive: true }); } catch (e) {}
      await writeFile(join(uploadDir, filename), buffer);
      
      thumbnailUrl = `/storage/thumbnails/${filename}`;
    }

    const uniqueSlug = slugify(title) + '-' + Date.now();

    const article = await prisma.articles.create({
      data: {
        title,
        content,
        slug: uniqueSlug,
        thumbnail: thumbnailUrl,
        author: user.name || 'Admin',
        type: 'Admin',
        created_at: new Date(),
        updated_at: new Date(),
      }
    });

    return NextResponse.json({
      message: 'Đã lưu bài viết thành công!',
      data: { ...article, id: Number(article.id) }
    }, { status: 201 });
  } catch (err: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: err.message }, { status: 500 });
  }
}
