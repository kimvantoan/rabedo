import { ArticleCard } from "@/components/article-card";
import { Header } from "@/components/header";
import prisma from "@/lib/prisma";

export default async function Home() {
  let articles: any[] = [];

  try {
    articles = await prisma.articles.findMany({
      where: { type: 'news' },
      orderBy: { created_at: 'desc' },
      take: 12,
    });
  } catch (err) {
    console.error(err);
  }

  // Hàm loại bỏ thẻ HTML để trích xuất văn bản làm Mô tả ngắn gọn (Description)
  const stripHtml = (html: string) => {
    if (!html) return "";
    const text = html.replace(/<[^>]*>?/gm, '');
    return text.length > 160 ? text.substring(0, 160) + '...' : text;
  };

  return (
    <div className="min-h-screen bg-background">
      <Header />
      <main className="container mx-auto px-4 py-16 max-w-7xl">
        <div className="mb-12">
          <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight text-foreground mb-4">
            Tin Tức Mới Nhất
          </h1>
          <p className="text-lg text-muted-foreground max-w-2xl">
            Cập nhật những bài viết, tin điểm nhấn và những hành trình khám phá mới nhất.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          {articles.map((article) => {
            const thumbnail = article.thumbnail
              ? (article.thumbnail.startsWith('http') ? article.thumbnail : article.thumbnail)
              : 'https://images.unsplash.com/photo-1499750310107-5fef28a66643';

            const publishDate = new Date(article.created_at ?? new Date()).toLocaleDateString("vi-VN", {
              day: '2-digit', month: '2-digit', year: 'numeric'
            });

            return (
              <ArticleCard
                key={article.id}
                title={article.title}
                description={stripHtml(article.content || "")}
                thumbnail={thumbnail}
                slug={article.slug || article.id.toString()}
                date={publishDate}
                author={article.author || "Quản trị viên"}
              />
            );
          })}

          {articles.length === 0 && (
            <div className="col-span-full py-20 text-center text-muted-foreground bg-muted/20 rounded-2xl border border-dashed border-border/60">
              Hiện tại chưa có bài viết nào được xuất bản.
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
