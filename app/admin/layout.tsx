import LogoutButton from "./LogoutButton";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="h-screen w-full overflow-hidden bg-muted/20 flex flex-col md:flex-row">
      <aside className="w-full md:w-64 bg-card border-r border-border h-full p-6 flex flex-col">
        <h2 className="text-2xl font-bold text-foreground mb-8 cursor-pointer tracking-tight">
          Admin Rabedo
        </h2>
        <nav className="flex flex-col gap-2 flex-1">
          <a href="#" className="px-4 py-3 bg-primary text-primary-foreground rounded-lg font-medium transition-colors">
            Quản lý Bài viết
          </a>
          <div className="mt-auto">
            <LogoutButton />
          </div>
        </nav>
      </aside>
      <main className="flex-1 p-4 md:p-8 overflow-hidden h-full">
        {children}
      </main>
    </div>
  );
}
