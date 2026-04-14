"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { PlusCircle, Edit, Trash2, Search, User, ChevronLeft, ChevronRight, ExternalLink } from "lucide-react";

export default function AdminPage() {
  const [articles, setArticles] = useState<any[]>([]);
  const [total, setTotal] = useState(0);
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [loading, setLoading] = useState(true);

  const ITEMS_PER_PAGE = 10;

  const fetchArticles = async () => {
    setLoading(true);
    try {
      const match = document.cookie.match(/(^|;\s*)admin-token=([^;]*)/);
      const token = match ? match[2] : null;
      const apiUrl = '';

      const res = await fetch(`${apiUrl}/api/articles?page=${currentPage}&search=${searchQuery}&type=Admin`, {
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/json"
        }
      });
      const data = await res.json();
      if (res.ok) {
        setArticles(data.data);
        setTotal(data.total);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      fetchArticles();
    }, 400); // 400ms debounce cho ô tìm kiếm
    return () => clearTimeout(delayDebounceFn);
  }, [currentPage, searchQuery]);

  const handleDelete = async (id: number) => {
    if (!confirm("CẢNH BÁO: Bạn có chắc chắn muốn xóa bài viết này vĩnh viễn khỏi hệ thống?")) return;
    try {
      const match = document.cookie.match(/(^|;\s*)admin-token=([^;]*)/);
      const token = match ? match[2] : null;
      const apiUrl = '';
      const res = await fetch(`${apiUrl}/api/articles/${id}`, {
        method: 'DELETE',
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/json"
        }
      });
      if (res.ok) {
        fetchArticles(); // Tải lại danh sách
      } else {
        alert("Có lỗi xảy ra khi xóa bài.");
      }
    } catch (err) {
      alert("Lỗi kết nối với máy chủ.");
    }
  };

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(e.target.value);
    setCurrentPage(1); // Reset to page 1 on new search
  };

  const totalPages = Math.max(1, Math.ceil(total / ITEMS_PER_PAGE));

  return (
    <div className="w-full h-full flex flex-col overflow-hidden">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4 shrink-0">
        <div>
          <h1 className="text-3xl font-bold tracking-tight mb-2">Quản lý bài viết</h1>
          <p className="text-muted-foreground">Khu vực kiểm duyệt và xuất bản nội dung của Admin.</p>
        </div>
        <Link
          href="/admin/editor"
          className="inline-flex items-center justify-center rounded-full text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow-md hover:bg-primary/90 hover:shadow-lg hover:-translate-y-0.5 h-11 px-6 whitespace-nowrap"
        >
          <PlusCircle className="mr-2 h-5 w-5" />
          Tạo bài viết mới
        </Link>
      </div>

      <div className="bg-card flex-1 min-h-0 rounded-2xl shadow-sm border border-border overflow-hidden flex flex-col">
        {/* Toolbar */}
        <div className="p-4 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-muted/10 shrink-0">
          <div className="relative w-full sm:w-96 max-w-xl">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <input
              type="text"
              placeholder="Tìm kiếm tiêu đề bài viết online..."
              value={searchQuery}
              onChange={handleSearch}
              className="w-full pl-9 pr-4 py-2 bg-background border border-border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
            />
          </div>
          <div className="text-sm text-muted-foreground">
            {loading ? "Đang tải dữ liệu..." : <>Tổng cộng tìm thấy: <strong className="text-foreground">{total}</strong> bài viết</>}
          </div>
        </div>

        {/* Table */}
        <div className="overflow-auto flex-1 relative">
          <table className="w-full text-left text-sm whitespace-nowrap">
            <thead className="bg-muted/30 text-muted-foreground text-xs uppercase font-semibold sticky top-0 z-10">
              <tr>
                <th className="px-6 py-4 w-1/2">Tiêu đề</th>
                <th className="px-6 py-4">Ngày đăng</th>
                <th className="px-6 py-4">Link Bài Viết</th>
                <th className="px-6 py-4 text-right">Thao tác</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {articles.map((article) => (
                <tr key={article.id} className="hover:bg-muted/10 transition-colors">
                  <td className="px-6 py-4">
                    <div className="font-semibold text-foreground text-base max-w-[250px] sm:max-w-xs md:max-w-md lg:max-w-lg xl:max-w-2xl truncate" title={article.title}>
                      {article.title}
                    </div>
                    <div className="text-muted-foreground text-xs mt-1.5 flex items-center">
                      <User className="w-3.5 h-3.5 mr-1.5" /> {article.author || "Quản trị viên"}
                    </div>
                  </td>
                  <td className="px-6 py-4 text-muted-foreground font-medium">
                    {new Date(article.created_at).toLocaleDateString("vi-VN", { day: '2-digit', month: '2-digit', year: 'numeric' })}
                  </td>
                  <td className="px-6 py-4">
                    <a
                      href={`/articles/${article.slug || article.id}`}
                      target="_blank"
                      rel="noreferrer"
                      className="inline-flex items-center gap-1.5 text-primary hover:underline hover:text-primary/80 transition-colors bg-primary/5 px-2.5 py-1 rounded-md text-sm font-medium"
                    >
                      <ExternalLink className="w-3.5 h-3.5" /> Xem bài viết
                    </a>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="flex justify-end gap-2">
                      <Link href={`/admin/editor?id=${article.id}`} className="p-2 text-muted-foreground hover:text-primary bg-muted/20 hover:bg-primary/10 rounded-lg transition-colors" title="Chỉnh sửa">
                        <Edit className="w-4 h-4" />
                      </Link>
                      <button onClick={() => handleDelete(article.id)} className="p-2 text-muted-foreground hover:text-destructive bg-muted/20 hover:bg-destructive/10 rounded-lg transition-colors" title="Xóa">
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}

              {!loading && articles.length === 0 && (
                <tr>
                  <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                    Không tìm thấy bài viết nào. Hãy thử tạo bài viết mới!
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="p-4 border-t border-border flex items-center justify-between bg-muted/5 shrink-0">
            <div className="text-sm text-muted-foreground">
              Hiển thị trang <span className="font-medium text-foreground">{currentPage}</span> / <span className="font-medium text-foreground">{totalPages}</span>
            </div>
            <div className="flex items-center gap-1">
              <button
                onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                disabled={currentPage === 1}
                className="p-2 border border-border rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Trang trước"
              >
                <ChevronLeft className="w-4 h-4" />
              </button>

              <div className="flex items-center mx-2 gap-1">
                {(() => {
                  let startPage = Math.max(1, currentPage - 2);
                  let endPage = Math.min(totalPages, startPage + 4);

                  if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
                  }

                  const pages = [];
                  for (let p = startPage; p <= endPage; p++) {
                    pages.push(p);
                  }

                  return pages.map((pageNum) => (
                    <button
                      key={pageNum}
                      onClick={() => setCurrentPage(pageNum)}
                      className={`w-8 h-8 flex items-center justify-center rounded-md text-sm transition-colors ${currentPage === pageNum
                          ? "bg-primary text-primary-foreground font-semibold"
                          : "text-muted-foreground hover:bg-muted border border-transparent hover:border-border"
                        }`}
                    >
                      {pageNum}
                    </button>
                  ));
                })()}
              </div>

              <button
                onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                disabled={currentPage === totalPages}
                className="p-2 border border-border rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Trang tiếp theo"
              >
                <ChevronRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
