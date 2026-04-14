import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';

export async function GET(request: NextRequest, { params }: { params: Promise<{ slug: string }> }) {
  try {
    const { slug } = await params;

    let whereObj: any = { slug };

    // Support fetching by ID or Slug dynamically like Laravel did
    if (!isNaN(Number(slug))) {
      whereObj = { OR: [{ slug }, { id: Number(slug) }] };
    }

    const article = await prisma.articles.findFirst({
      where: whereObj,
    });

    if (!article) {
      return NextResponse.json({ message: 'Không tìm thấy bài viết' }, { status: 404 });
    }

    const safeArticle = {
      ...article,
      id: Number(article.id),
      created_at: article.created_at?.toISOString() ?? null,
      updated_at: article.updated_at?.toISOString() ?? null,
    };

    return NextResponse.json(safeArticle);
  } catch (error: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: error.message }, { status: 500 });
  }
}
