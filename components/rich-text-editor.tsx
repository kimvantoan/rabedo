"use client";

import { useMemo, useRef, useCallback } from "react";
import dynamic from "next/dynamic";
import "react-quill-new/dist/quill.snow.css";

// Vượt rào cơ chế ẩn ref của next/dynamic bằng cách chuyển đổi qua forwardedRef (Pipe Ref)
const QuillWrapper = dynamic(
  async () => {
    const { default: RQ } = await import("react-quill-new");
    return function Comp({ forwardedRef, ...props }: any) {
      return <RQ ref={forwardedRef} {...props} />;
    };
  },
  { ssr: false }
);

interface RichTextEditorProps {
  value: string;
  onChange: (value: string) => void;
}

export default function RichTextEditor({ value, onChange }: RichTextEditorProps) {
  const reactQuillRef = useRef<any>(null);

  // Sử dụng function truyền thống để bắt this.quill từ thanh Hook của thư viện thay vì dùng Ref ảo
  const imageHandler = function (this: any) {
    const quill = this.quill;
    const input = document.createElement("input");
    input.setAttribute("type", "file");
    input.setAttribute("accept", "image/*");
    input.click();

    input.onchange = async () => {
      const file = input.files ? input.files[0] : null;
      if (!file) return;

      const formData = new FormData();
      formData.append("image", file);

      try {
        const match = document.cookie.match(/(^|;\s*)admin-token=([^;]*)/);
        const token = match ? match[2] : null;
        const apiUrl = process.env.NEXT_PUBLIC_API_URL;

        const res = await fetch(`${apiUrl}/api/upload-image`, {
          method: "POST",
          headers: {
            "Authorization": `Bearer ${token}`,
            "Accept": "application/json"
          },
          body: formData
        });

        if (res.ok) {
          const data = await res.json();
          if (quill) {
            const range = quill.getSelection(true);
            const imageUrl = data.url.startsWith('http') ? data.url : `${apiUrl}${data.url}`;
            quill.insertEmbed(range.index, "image", imageUrl);
            quill.setSelection(range.index + 1);
          }
        } else {
          alert("Server báo sai cấu trúc hình ảnh hoặc file tải lên thất bại!");
        }
      } catch (err) {
        alert("Có lỗi mạng khi đẩy hình ảnh vào kho Laravel Storage!");
      }
    };
  };

  const modules = useMemo(
    () => ({
      toolbar: {
        container: [
          [{ header: [1, 2, 3, false] }],
          ["bold", "italic", "underline", "strike", "blockquote"],
          [{ color: [] }, { background: [] }],
          [{ align: [] }],
          [{ list: "ordered" }, { list: "bullet" }],
          ["link", "image", "video"],
          ["clean"],
        ],
        handlers: {
          image: imageHandler,
        },
      },
    }),
    [] // Lắng nghe phụ thuộc rỗng (Ngăn chặn nhảy con trỏ)
  );

  return (
    <div className="w-full flex-1 flex flex-col group relative">
      <QuillWrapper
        forwardedRef={reactQuillRef}
        theme="snow"
        value={value || ""}
        onChange={(html: string) => onChange(html)}
        modules={modules}
        placeholder="Bắt đầu viết nội dung của bạn..."
        className="w-full [&>.ql-toolbar]:rounded-t-2xl [&>.ql-toolbar]:border-border [&>.ql-toolbar]:bg-muted/40 [&>.ql-container]:rounded-b-2xl [&>.ql-container]:border-border [&>.ql-container]:min-h-[500px] [&>.ql-editor]:min-h-[500px] [&>.ql-editor]:text-[17px] [&>.ql-editor]:leading-[1.8] [&>.ql-editor]:text-foreground/90 font-sans transition-all duration-300 hover:shadow-sm"
      />
    </div>
  );
}
