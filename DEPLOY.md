# Hướng Dẫn Triển Khai (Deploy) Rabedo News Lên cPanel

Dự án này đã được trang bị sẵn công cụ đóng gói tối thượng chạy trên PowerShell là `scripts/deploy.ps1`. Bạn hoàn toàn được giải phóng khỏi thao tác gom file thủ công!

## PHẦN 1: ĐÓNG GÓI VÀ TRIỂN KHAI LÊN SERVER

### Bước 1: Khởi Chạy Mã Đóng Gói (Build)
Trên máy tính Local, hãy mở PowerShell trực tiếp tại thư mục gốc chứa Code, và chạy lệnh:
```powershell
.\scripts\deploy.ps1
```
Ngay lập tức, Script này sẽ thực hiện hàng loạt phép thần thông:
- Dịch TypeScript sang JS thô.
- Gọi lệnh Build Standalone của Next.js.
- Cóp nhặt giao diện `.next/static` và `public/` đút vào lõi Server.
- Khắc phục các rào cản chí mạng trên cPanel như lỗi Symlink (Bằng cách đổi tên `node_modules` thành `bundled_modules`).
- Ép khuôn file khởi động thành `app.js` để luồn lách qua hệ thống Passenger của Cpanel.

Cuối cùng, nó sẽ phun ra duy nhất một file cực kỳ gọn mang tên: **`rabedo-deploy.zip`**.

### Bước 2: Tải lên cPanel và Thiết lập
1. Quăng cục `rabedo-deploy.zip` lên Cpanel (ví dụ bỏ thẳng vào `public_html/`). Đừng quên upload cả file `.env.local` lên vùng với nó.
2. Giải nén (Extract) cục zip đó ra. Tại cPanel Terminal hoặc VPS SSH, bạn dùng lệnh sau để tiến hành giải nén đè nhanh gọn:
   ```bash
   unzip -o rabedo-deploy.zip
   ```
   *(Nhớ đổi tên file `.env.local` thành `.env` ở trên host để hệ thống nạp được biến môi trường).*
3. Kích nổ Server: Ở giao diện cPanel -> Cuộn xuống tìm **Setup Node.js App** -> Bấm **Create Application**.
4. Chọn bản Node.js 18 hoặc 20. Khai báo `Application Root` là thư mục vừa giải nén.
5. **ĐIỂM CHÚT CHỐT:** Tại ô `Application startup file`, nhập chính xác chữ: **`app.js`**. (Đừng nhập `server.js` vì trên Cpanel sẽ bị chối cổng).
6. Bấm **START APP**. Cỗ máy Web News của bạn đã chính thức được cất cánh toàn cầu!

---

## PHẦN 2: CẬP NHẬT NHANH (QUICK PATCH) BẰNG FILE rabedo-patch.zip

Nếu server của bạn **đã được thiết lập và đang chạy bình thường** (qua Phần 1), bạn chỉ muốn Cập nhật Web (Up code mới, thay đổi API, tính năng mới) mà không muốn làm sáo trộn thư mục:

1. Copy file **`rabedo-patch.zip`** lên Cpanel (quăng vào trong mục `public_html/` hoặc thư mục code hiện tại của bạn).
2. Chuột phải chọn **Extract (Giải nén)** hoặc dùng chuỗi lệnh thần thánh trên Terminal / SSH để ốp thẳng đè lên các tệp tin cũ:
   ```bash
   unzip -o rabedo-patch.zip
   ```
   *(File patch này chỉ ghi đè thư mục `.next`, thư mục `scripts` và file khởi động mà KHÔNG làm ảnh hưởng đến thư mục ảnh hay `.env` của bạn).*
3. Quay lại giao diện **Setup Node.js App** của Cpanel -> Tìm tới dòng Application Web của bạn -> Bấm Nút **RESTART** (nhiều mũi tên xoay tròn).
Ngay lập tức, Server sẽ nối lại mã nguồn và áp dụng toàn bộ chức năng mới cập nhật ngay tức khắc! Mượt cực kỳ.

---

## PHẦN 3: THIẾT LẬP AI TỰ ĐỘNG GEN BÀI VIẾT (CRON JOB)

Chức năng tinh hoa của Blog này là không cần bạn viết bài! Hệ thống sẽ nối não với Gemini và mượn mắt Bing Search để cày ngày cày đêm cho bạn qua Cron Job.

### Cách 1: Kích điện bằng Link API (Khuyên Dùng)
Rất dễ và sạch sẽ. Đăng nhập Cpanel -> Cuộn tìm tab **Cron Jobs** -> Chọn mốc thời gian vắt sữa AI (Ví dụ: Chạy 4 Tiếng một lần: `0 */4 * * *`) -> Điền dòng này vào ô Command:
```bash
curl -s "https://YOURDOMAIN.COM/api/generate-article?secret=rabedo_gen_2026_secret" > /dev/null 2>&1
```
*(Chỉ cần đổi `YOURDOMAIN.COM` thành Tên Miền bạn đang sài).*

### Cách 2: Gọi bằng Command trực tiếp trong hệ điều hành (Chạy ngầm Backend)
Do lệnh biên dịch `deploy.ps1` ở trên đã tống luôn một bản Copy của Cỗ Máy Đào Vàng vào rồi, bạn hoàn toàn có thể chạy script ở chế độ Background mà không lo rào cản IP hay Cache Name.
Ở ô Cronjob Command, chèn câu lệnh sau:
```bash
cd /duong/dan/toi/thu/muc/web && /usr/bin/node scripts/dist/generate-ai-article.js >> /var/log/rabedo-ai.log 2>&1
```
*(Hãy thay đường dẫn /duong/dan/... khớp với host của bạn. Dòng này bắt con Server âm thầm thực hiện lệnh gọi AI và ném mọi log cặn bã về file `.log` hòng triệt tiêu rác tệp hệ thống).*