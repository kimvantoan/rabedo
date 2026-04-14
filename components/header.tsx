import Link from "next/link";

export function Header() {
  return (
    <header className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="container mx-auto px-4 h-16 flex items-center justify-center">
        <Link href="/" className="flex items-center gap-2 transition-transform hover:scale-105 active:scale-95">
          <img src="/logo.png" alt="RabedoNews Logo" className="h-10 w-auto object-contain" />
        </Link>
      </div>
    </header>
  );
}
