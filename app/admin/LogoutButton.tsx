"use client";

import { LogOut } from "lucide-react";
import { useRouter } from "next/navigation";

export default function LogoutButton() {
  const router = useRouter();

  const handleLogout = async () => {
    // Erase the cookie natively
    document.cookie = "admin-token=; path=/; max-age=0; expires=Thu, 01 Jan 1970 00:00:00 GMT";

    // Redirect securely forcing a hard refresh to re-evaluate middleware
    window.location.href = "/login";
  };

  return (
    <button
      onClick={handleLogout}
      className="w-full mt-2 block px-4 py-3 text-destructive hover:bg-destructive/10 rounded-lg font-medium transition-colors border border-destructive/20 text-center flex items-center justify-center gap-2"
    >
      <LogOut className="w-4 h-4" />
      Đăng xuất
    </button>
  );
}
