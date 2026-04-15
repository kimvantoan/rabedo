import { notFound } from "next/navigation";
import Link from "next/link";
import { ChevronRight, Home } from "lucide-react";
import { Metadata } from "next";
import { Header } from "@/components/header";
import prisma from "@/lib/prisma";

// Hàm hỗ trợ loại bỏ thẻ HTML để lấy mô tả ngắn
const stripHtml = (html: string) => {
  if (!html) return "";
  const text = html.replace(/<[^>]*>?/gm, '');
  return text.length > 160 ? text.substring(0, 160) + '...' : text;
};

// Dynamic SEO Metadata
export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params;

  try {
    const article = await prisma.articles.findUnique({ where: { slug } });
    if (!article) return { title: "Không tìm thấy bài viết" };

    const description = stripHtml(article.content);
    return {
      title: `${article.title} | RabedoNews`,
      description: description,
      openGraph: {
        title: article.title,
        description: description,
        images: [
          {
            url: article.thumbnail ? (article.thumbnail.startsWith('http') ? article.thumbnail : article.thumbnail) : '/placeholder.png',
            width: 1200,
            height: 630,
            alt: article.title,
          }
        ]
      }
    };
  } catch (err) {
    return { title: "Không tìm thấy bài viết" };
  }
}

export default async function ArticleDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;

  let article;
  try {
    article = await prisma.articles.findUnique({ where: { slug } });
    if (!article) return notFound();
  } catch (err) {
    return notFound();
  }

  // Tiền xử lý nội dung để loại bỏ các khoảng trắng liền mạch ép cứng nội dung
  let contentHTML = article.content || '<p>No content available.</p>';
  contentHTML = contentHTML.replace(/&nbsp;/g, ' ').replace(/\u00A0/g, ' ');

  // Định dạng ngày
  const publishDate = new Date(article.created_at ?? new Date()).toLocaleDateString("vi-VN", {
    month: 'long', day: 'numeric', year: 'numeric'
  });

  return (
    <div className="bg-white min-h-screen pb-12">
      <Header />

      {/* Breadcrumb - Chỉ hiển thị cho bài viết không phải của Admin */}
      {article.type !== 'Admin' ? (
        <div className="container mx-auto px-4 lg:px-6 pt-5 pb-6">
          <div className="max-w-[800px] mx-auto">
            <nav className="flex items-center gap-2 text-[12px] font-medium text-gray-400 uppercase tracking-widest justify-start">
              <Link href="/" className="hover:text-black transition-colors"><Home className="w-3.5 h-3.5" /></Link>
              <ChevronRight className="w-3 h-3" />
              <Link href="/" className="hover:text-black transition-colors">Trang chủ</Link>
              <ChevronRight className="w-3 h-3" />
              <span className="text-gray-800 truncate max-w-[200px] md:max-w-md">{article.title}</span>
            </nav>
          </div>
        </div>
      ) : (
        <div className="h-10 w-full" /> // Spacer để bù khoảng trống cho tiêu đề Admin
      )}

      {/* Main Container for Header and Content */}
      <main className="container mx-auto px-4 lg:px-6 mb-20 md:mb-24">
        <div className="max-w-[800px] mx-auto w-full text-left">

          {/* Header */}
          <header className="mb-10 md:mb-12">
            <h1 className="text-[32px] md:text-[40px] lg:text-[44px] font-extrabold text-[#1a1a1a] leading-[1.3] tracking-tight mb-5">
              {article.title}
            </h1>
            <div className="flex items-center gap-1.5 text-[14px] text-gray-400 font-sans flex-wrap">
              <span className="uppercase text-gray-400 text-[12px] font-medium">Bởi</span>
              <span className="font-bold text-gray-900 uppercase mr-1 text-[13px] tracking-wide">
                {article.author || "Quản trị viên"}
              </span>
              <span className="text-gray-300">—</span>
              <span className="ml-1">{publishDate}</span>
              <span className="mx-1">trong</span>
              <span className="text-gray-400 font-medium">Tin tức</span>
            </div>
          </header>

          {/* Typography Content */}
          <article className="prose prose-lg md:prose-xl max-w-none w-full text-[#333]
            [&_h2]:text-2xl [&_h2]:md:text-[32px] [&_h2]:mt-12 [&_h2]:mb-6 [&_h2]:font-extrabold [&_h2]:text-gray-900 [&_h2]:tracking-tight [&_h2]:leading-tight
            [&_h3]:text-[22px] [&_h3]:md:text-[26px] [&_h3]:mt-10 [&_h3]:mb-4 [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:leading-snug
            [&_h4]:text-[19px] [&_h4]:md:text-[22px] [&_h4]:mt-8 [&_h4]:mb-3 [&_h4]:font-bold [&_h4]:text-gray-700
            [&_p]:text-[18px] [&_p]:md:text-[20px] [&_p]:leading-[1.65] [&_p]:text-[#333] [&_p]:mb-7 [&_p]:font-sans [&_p]:whitespace-normal [&_p]:break-words [&_p]:text-justify
            [&_blockquote]:border-l-4 [&_blockquote]:border-red-600 [&_blockquote]:pl-6 [&_blockquote]:md:pl-8 [&_blockquote]:italic [&_blockquote]:text-[22px] [&_blockquote]:md:text-[26px] [&_blockquote]:text-gray-900 [&_blockquote]:bg-gray-50 [&_blockquote]:py-6 [&_blockquote]:pr-6 [&_blockquote]:rounded-r-xl [&_blockquote]:my-10 [&_blockquote]:leading-relaxed [&_blockquote]:text-justify
            [&_img]:rounded-none [&_img]:w-full [&_img]:my-10 [&_img]:shadow-md
            [&_figcaption]:text-center [&_figcaption]:text-[13px] [&_figcaption]:text-gray-500 [&_figcaption]:mt-3
            [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-8 [&_ul]:space-y-2 [&_li]:text-[18px] [&_li]:md:text-[20px] [&_li]:font-sans [&_li]:text-[#333] [&_li]:whitespace-normal [&_li]:break-words [&_li]:text-justify
          ">
            <div dangerouslySetInnerHTML={{ __html: contentHTML }} />
          </article>
        </div>
      </main>

    </div>
  );
}
