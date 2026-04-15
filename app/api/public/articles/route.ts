export const dynamic = 'force-dynamic';
import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url);
    const type = searchParams.get('type');
    const search = searchParams.get('search');
    const page = parseInt(searchParams.get('page') || '1');
    const perPage = 10;

    let where: any = {};
    if (type) {
      where.type = type;
    }
    if (search) {
      where.title = {
        contains: search,
      };
    }

    const total = await prisma.articles.count({ where });
    const articles = await prisma.articles.findMany({
      where,
      orderBy: { created_at: 'desc' },
      skip: (page - 1) * perPage,
      take: perPage,
    });
    const safeArticles = articles.map((article: any) => ({
      ...article,
      id: Number(article.id),
      created_at: article.created_at?.toISOString() ?? null,
      updated_at: article.updated_at?.toISOString() ?? null,
    }));

    return NextResponse.json({
      data: safeArticles,
      current_page: page,
      per_page: perPage,
      total,
      last_page: Math.ceil(total / perPage),
    });
  } catch (error: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: error.message }, { status: 500 });
  }
}
