import Link from "next/link";
import { cn } from "@/lib/utils";

interface ArticleCardProps {
  title: string;
  description: string;
  thumbnail: string;
  slug: string;
  date?: string;
  author?: string;
  className?: string;
}

export function ArticleCard({
  title,
  description,
  thumbnail,
  slug,
  date,
  author,
  className,
}: ArticleCardProps) {
  return (
    <Link
      href={`/articles/${slug}`}
      className={cn(
        "group flex flex-col overflow-hidden rounded-2xl border border-border bg-card text-card-foreground shadow-sm transition-all hover:shadow-md hover:-translate-y-1",
        className
      )}
    >
      <div className="relative aspect-[16/9] w-full overflow-hidden bg-muted">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={thumbnail}
          alt={title}
          className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
        />
      </div>
      <div className="flex flex-1 flex-col p-6">
        <div className="flex items-center gap-2 text-xs text-muted-foreground mb-3">
          {date && <span>{date}</span>}
          {date && author && <span>•</span>}
          {author && <span className="font-medium text-foreground">{author}</span>}
        </div>
        <h3 className="mb-2 text-xl font-bold leading-tight tracking-tight text-foreground line-clamp-2">
          {title}
        </h3>
        <p className="text-muted-foreground text-sm line-clamp-3 leading-relaxed flex-1">
          {description}
        </p>
      </div>
    </Link>
  );
}
