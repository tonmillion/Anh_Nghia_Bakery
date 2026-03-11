# 🚀 HƯỚNG DẪN NHANH - BAKERY SHOP

## BƯỚC 1 ĐÃ HOÀN THÀNH! ✅

Chúc mừng! Bạn đã hoàn thành **Bước 1: Chuẩn bị môi trường phát triển**.

---

## 📦 CÁC FILE ĐÃ TẠO

### Tài liệu hướng dẫn:
1. **HUONG_DAN_CAI_DAT.md** - Hướng dẫn chi tiết cài đặt XAMPP, VS Code, Git
2. **README.md** - Giải thích cấu trúc dự án và quy ước code
3. **CHECKLIST.md** - Theo dõi tiến độ hoàn thành dự án
4. **GIT_GUIDE.md** - Hướng dẫn sử dụng Git cơ bản
5. **QUICK_START.md** - File này!

### File cấu hình:
1. **.htaccess** (gốc) - Bảo mật và URL rewriting
2. **config/.htaccess** - Bảo vệ file config
3. **uploads/.htaccess** - Chặn thực thi PHP trong uploads
4. **.gitignore** - Không commit file nhạy cảm

### Code:
1. **index.php** - Trang chủ cơ bản để test

---

## 🏗️ CẤU TRÚC THỦ MỤC ĐÃ TẠO

```
bakery-shop/
├── admin/                  # Phân hệ quản trị
│   ├── css/
│   ├── js/
│   ├── includes/
│   └── pages/
├── assets/                 # CSS, JS, Images chung
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── config/                 # File cấu hình
│   └── .htaccess
├── database/               # SQL scripts
├── includes/               # Classes, functions
├── uploads/                # File upload
│   └── .htaccess
├── user/                   # Phân hệ khách hàng
│   ├── css/
│   ├── js/
│   └── pages/
├── .gitignore
├── .htaccess
├── CHECKLIST.md
├── GIT_GUIDE.md
├── HUONG_DAN_CAI_DAT.md
├── index.php
├── README.md
└── QUICK_START.md (file này)
```

---

## ⚡ CÀI ĐẶT VÀ CHẠY DỰ ÁN

### 1. Copy dự án về máy

**Cách 1: Download trực tiếp**
- Download toàn bộ thư mục `bakery-shop`
- Copy vào: `C:\xampp\htdocs\` (Windows)
- Hoặc: `/Applications/XAMPP/htdocs/` (Mac)
- Hoặc: `/opt/lampp/htdocs/` (Linux)

**Cách 2: Sử dụng Git**
```bash
cd C:\xampp\htdocs
git clone [URL repository] bakery-shop
cd bakery-shop
```

### 2. Khởi động XAMPP
- Mở XAMPP Control Panel
- Start **Apache**
- Start **MySQL**

### 3. Kiểm tra hoạt động
- Mở trình duyệt
- Truy cập: `http://localhost/bakery-shop`
- Bạn sẽ thấy trang chủ cơ bản với thông báo "Bước 1 hoàn thành!"

---

## 📚 ĐỌC CÁC FILE HƯỚNG DẪN THEO THỨ TỰ

### Bắt đầu:
1. **HUONG_DAN_CAI_DAT.md** ← Đọc đầu tiên nếu chưa cài đặt công cụ
2. **README.md** ← Hiểu cấu trúc dự án
3. **GIT_GUIDE.md** ← Học cách dùng Git

### Theo dõi tiến độ:
4. **CHECKLIST.md** ← Đánh dấu tiến độ hàng ngày

---

## 🎯 BƯỚC TIẾP THEO

### Bước 2: Thiết kế Database
Đã sẵn sàng tiếp tục? Chúng ta sẽ:
1. Tạo database `bakery_db`
2. Thiết kế các bảng: users, categories, products, orders...
3. Tạo relationships (foreign keys)
4. Insert dữ liệu mẫu

**Hãy nói "làm bước 2" khi bạn sẵn sàng!**

---

## ✅ CHECKLIST KIỂM TRA BƯỚC 1

Trước khi sang bước 2, hãy đảm bảo:

- [ ] XAMPP đã cài đặt và chạy được
- [ ] Truy cập được `http://localhost`
- [ ] Truy cập được `http://localhost/phpmyadmin`
- [ ] VS Code đã cài đặt
- [ ] Extensions PHP đã cài trong VS Code
- [ ] Git đã cài đặt
- [ ] Đã copy thư mục bakery-shop vào htdocs
- [ ] Truy cập được `http://localhost/bakery-shop`

**Nếu TẤT CẢ đã OK → Bạn đã sẵn sàng cho Bước 2! 🎉**

---

## 🆘 TROUBLESHOOTING (Khắc phục lỗi)

### Lỗi: Apache không khởi động
**Giải pháp:** Port 80 bị chiếm → Đổi sang port 8080 trong httpd.conf

### Lỗi: MySQL không khởi động  
**Giải pháp:** Port 3306 bị chiếm → Kiểm tra service MySQL khác đang chạy

### Lỗi: Không truy cập được localhost/bakery-shop
**Kiểm tra:**
1. Apache đã start chưa?
2. Thư mục có trong `htdocs` không?
3. Tên thư mục có đúng không? (không có khoảng trắng)

### Lỗi: index.php hiển thị code thay vì chạy
**Giải pháp:** 
1. Chắc chắn truy cập qua `http://localhost/...` (không mở file trực tiếp)
2. Apache đã start chưa?

---

## 💡 MẸO VÀ LỜI KHUYÊN

### Làm việc hiệu quả:
1. **Commit thường xuyên** - Mỗi tính năng nhỏ hoàn thành → commit ngay
2. **Test ngay lập tức** - Viết xong code → test luôn, đừng để sau
3. **Comment code phức tạp** - Tương lai bạn sẽ cảm ơn bạn hiện tại
4. **Đọc error message** - Lỗi thường nói rõ vấn đề ở đâu
5. **Sử dụng var_dump()** - Debug PHP đơn giản nhất

### Shortcuts hữu ích (VS Code):
- `Ctrl + /` - Comment/Uncomment
- `Ctrl + D` - Select next occurrence
- `Ctrl + Shift + F` - Tìm kiếm trong toàn project
- `Alt + Up/Down` - Di chuyển dòng code
- `Ctrl + Space` - Autocomplete

### Quản lý thời gian:
- **Tuần 1**: Bước 1-2 (Chuẩn bị + Database)
- **Tuần 2**: Bước 3 (Classes và functions)
- **Tuần 3**: Bước 4 (Phân hệ khách hàng)
- **Tuần 4**: Bước 5 (Phân hệ admin)
- **Tuần 5**: Bước 6-8 (UI + Bảo mật + VNPay)
- **Tuần 6**: Bước 9-10 (Test + Deploy)

---

## 📞 HỖ TRỢ

Nếu gặp khó khăn:
1. Đọc lại file hướng dẫn tương ứng
2. Google error message (thường có giải pháp)
3. Kiểm tra CHECKLIST.md xem đã làm đủ chưa
4. Xem lại code ví dụ trong README.md

---

## 🎓 TÀI NGUYÊN HỌC TẬP BỔ SUNG

**PHP:**
- W3Schools PHP: https://www.w3schools.com/php/
- PHP.net Manual: https://www.php.net/manual/en/

**MySQL:**
- W3Schools SQL: https://www.w3schools.com/sql/
- MySQL Tutorial: https://dev.mysql.com/doc/

**Bootstrap:**
- Bootstrap 5 Docs: https://getbootstrap.com/docs/5.0/

**Git:**
- Git Book (Free): https://git-scm.com/book/en/v2

---

## 🎉 CONGRATULATIONS!

Bạn đã hoàn thành **BƯỚC 1** thành công!

Cấu trúc dự án đã được tạo hoàn chỉnh với:
✅ Thư mục và file tổ chức khoa học
✅ Bảo mật cơ bản (.htaccess)
✅ Git repository sẵn sàng
✅ Tài liệu hướng dẫn đầy đủ

**Tiếp theo:** Chúng ta sẽ tạo database và thiết kế các bảng dữ liệu.

---

**Ready for Bước 2? Let's go! 🚀**

---

*Last updated: 2024-02-09*
*Version: 1.0*
