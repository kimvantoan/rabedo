"use client";

import { useState, useRef, useEffect, Suspense } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { ArrowLeft, Save, Image as ImageIcon, UploadCloud } from "lucide-react";
import RichTextEditor from "@/components/rich-text-editor";

function EditorForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const editId = searchParams.get('id');
  const isEditMode = !!editId;

  const [title, setTitle] = useState("");
  const [thumbnailFile, setThumbnailFile] = useState<File | null>(null);
  const [thumbnailPreview, setThumbnailPreview] = useState<string>("");
  const [content, setContent] = useState("");
  const [saving, setSaving] = useState(false);

  const fileInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (isEditMode) {
      fetchExistingArticle();
    }
  }, [editId]);

  const fetchExistingArticle = async () => {
    try {
      const match = document.cookie.match(/(^|;\s*)admin-token=([^;]*)/);
      const token = match ? match[2] : null;
      const apiUrl = '';

      const res = await fetch(`${apiUrl}/api/articles/${editId}`, {
        headers: { "Authorization": `Bearer ${token}`, "Accept": "application/json" }
      });
      if (res.ok) {
        const data = await res.json();
        setTitle(data.title);
        setContent(data.content);
        if (data.thumbnail) {
          // Xử lý hiển thị đường dẫn ảnh tĩnh có sẵn
          const imgUrl = data.thumbnail.startsWith('http') ? data.thumbnail : `${apiUrl}${data.thumbnail}`;
          setThumbnailPreview(imgUrl);
        }
      }
    } catch (err) {
      console.error(err);
    }
  };

  const handleThumbnailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setThumbnailFile(file);
      setThumbnailPreview(URL.createObjectURL(file));
    }
  };

  const triggerFileInput = () => fileInputRef.current?.click();

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!title || !content) {
      alert("Vui lòng điền tiêu đề và nội dung bài viết!");
      return;
    }

    setSaving(true);
    try {
      const match = document.cookie.match(/(^|;\s*)admin-token=([^;]*)/);
      const token = match ? match[2] : null;
      const apiUrl = '';

      const formData = new FormData();
      formData.append('title', title);
      formData.append('content', content);
      if (thumbnailFile) {
        formData.append('thumbnail', thumbnailFile);
      }

      // Xóa bỏ Laravel _method spoofing
      // Mặc định Next.js app/api hỗ trợ method PUT Native

      const res = await fetch(`${apiUrl}/api/articles${isEditMode ? `/${editId}` : ''}`, {
        method: isEditMode ? "PUT" : "POST",
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/json"
        },
        body: formData
      });

      if (res.ok) {
        alert(`Đã ${isEditMode ? 'cập nhật' : 'xuất bản tạo mới'} bài viết thành công!`);
        router.push("/admin");
      } else {
        const err = await res.json();
        alert("Lỗi xuất bản: " + (err.message || 'Dữ liệu không hợp lệ'));
      }
    } catch (err) {
      alert("Đã xảy ra lỗi mạng kết nối tới máy chủ.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="w-full h-full flex flex-col overflow-hidden bg-background">
      {/* Premium Top Navigation Bar */}
      <div className="flex items-center justify-between px-6 lg:px-10 py-4 border-b border-border bg-card/50 backdrop-blur-sm shrink-0">
        <div className="flex items-center gap-4">
          <button
            type="button"
            onClick={() => router.back()}
            className="w-10 h-10 flex items-center justify-center rounded-full hover:bg-muted text-muted-foreground transition-colors"
          >
            <ArrowLeft className="w-5 h-5" />
          </button>
          <div className="hidden sm:flex items-center gap-2 text-sm font-medium text-muted-foreground">
            <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span>Chế độ {isEditMode ? "Cập nhật bài viết hiện tại" : "Soạn thảo bài đăng mới"}</span>
          </div>
        </div>

        <button
          onClick={handleSave}
          disabled={saving}
          type="button"
          className="inline-flex items-center justify-center rounded-full text-sm font-semibold transition-all focus-visible:outline-none bg-primary text-primary-foreground shadow flex-shrink-0 hover:bg-primary/90 hover:shadow-md hover:-translate-y-0.5 h-10 px-6 lg:px-8 disabled:opacity-50 disabled:pointer-events-none"
        >
          <Save className="w-4 h-4 mr-2" />
          {saving ? 'Đang gửi...' : (isEditMode ? 'Cập nhật' : 'Xuất bản bài viết')}
        </button>
      </div>

      {/* Main Form Area */}
      <div className="flex-1 overflow-y-auto w-full flex justify-center scroll-smooth">
        <form onSubmit={handleSave} className="w-full max-w-[1600px] flex flex-col px-6 sm:px-12 md:px-20 lg:px-32 py-12 gap-10">

          {/* Title Area */}
          <div className="flex flex-col">
            <input
              type="text"
              placeholder="Tiêu đề bài viết..."
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full bg-transparent border-0 text-2xl sm:text-3xl font-bold focus:outline-none focus:ring-0 placeholder:text-muted-foreground/40 text-foreground tracking-tight leading-tight px-0"
              required
            />
          </div>

          {/* Thumbnail Block - File Upload */}
          <div className="flex flex-col gap-3 bg-muted/10 border border-border/50 rounded-2xl p-5 sm:p-6 transition-all hover:bg-muted/30">
            <div className="flex items-center gap-2 text-sm font-semibold text-foreground/80 mb-2">
              <ImageIcon className="w-4 h-4 text-primary" />
              Ảnh bìa bài viết (Upload)
            </div>

            {thumbnailPreview ? (
              <div className="relative w-full max-w-2xl h-[180px] sm:h-[240px] rounded-xl overflow-hidden border border-border/40 group shadow bg-muted object-cover">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={thumbnailPreview} alt="Preview thumbnail" className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" />
                <div className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    type="button"
                    onClick={triggerFileInput}
                    className="px-6 py-2.5 bg-background text-foreground hover:bg-muted font-medium rounded-lg shadow-lg flex items-center gap-2"
                  >
                    <UploadCloud className="w-4 h-4" /> Thay đổi ảnh
                  </button>
                </div>
              </div>
            ) : (
              <div
                onClick={triggerFileInput}
                className="w-full h-48 border-2 border-dashed border-border hover:border-primary/50 hover:bg-muted/20 bg-background/50 rounded-xl flex flex-col items-center justify-center text-muted-foreground transition-all cursor-pointer"
              >
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                  <UploadCloud className="w-6 h-6 text-primary" />
                </div>
                <span className="font-medium text-foreground">Click để tải ảnh bìa từ máy tính</span>
                <span className="text-xs mt-1">Định dạng hỗ trợ: JPG, PNG, WEBP</span>
              </div>
            )}

            <input
              type="file"
              ref={fileInputRef}
              accept="image/*"
              onChange={handleThumbnailChange}
              className="hidden"
            />
          </div>

          {/* Rich Text Editor */}
          <div className="flex flex-col flex-1 pb-24">
            <RichTextEditor value={content} onChange={setContent} />
          </div>
        </form>
      </div>
    </div>
  );
}

export default function EditorPageWrapper() {
  return (
    <Suspense fallback={<div className="p-8 text-center text-muted-foreground">Đang tải trình soạn thảo...</div>}>
      <EditorForm />
    </Suspense>
  )
}
