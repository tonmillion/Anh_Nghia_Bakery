# HƯỚNG DẪN CÀI ĐẶT MÔI TRƯỜNG PHÁT TRIỂN

## BƯỚC 1.1: CÀI ĐẶT CÔNG CỤ CẦN THIẾT

### 1. Cài đặt XAMPP (Windows/Mac/Linux)

**Windows:**
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Chọn phiên bản PHP 7.4 hoặc cao hơn (khuyến nghị PHP 8.0+)
3. Chạy file cài đặt và làm theo hướng dẫn
4. Cài đặt vào thư mục: `C:\xampp`
5. Chọn cài đặt: Apache, MySQL, PHP, phpMyAdmin

**Mac:**
1. Tải XAMPP for Mac
2. Kéo thả vào thư mục Applications
3. Mở XAMPP từ Applications

**Linux (Ubuntu/Debian):**
```bash
# Tải XAMPP
wget https://www.apachefriends.org/xampp-files/8.2.12/xampp-linux-x64-8.2.12-0-installer.run

# Cấp quyền thực thi
chmod +x xampp-linux-x64-8.2.12-0-installer.run

# Chạy cài đặt
sudo ./xampp-linux-x64-8.2.12-0-installer.run
```

**Khởi động XAMPP:**
- Windows: Mở XAMPP Control Panel → Start Apache và MySQL
- Mac/Linux: Mở XAMPP Manager → Start Apache và MySQL

**Kiểm tra cài đặt:**
- Mở trình duyệt, truy cập: `http://localhost`
- Nếu thấy trang XAMPP Dashboard → Thành công!
- Truy cập phpMyAdmin: `http://localhost/phpmyadmin`

---

### 2. Cài đặt Visual Studio Code

**Tải và cài đặt:**
1. Truy cập: https://code.visualstudio.com/
2. Tải phiên bản phù hợp với hệ điều hành
3. Cài đặt theo hướng dẫn

**Cài đặt Extensions quan trọng:**

Mở VS Code → Click biểu tượng Extensions (Ctrl+Shift+X) → Tìm và cài đặt:

1. **PHP Intelephense** - Hỗ trợ code PHP
2. **PHP Debug** - Debug PHP code
3. **MySQL** - Quản lý database trong VS Code
4. **Live Server** - Chạy server local để test
5. **Auto Rename Tag** - Tự động đổi tên tag HTML
6. **Bracket Pair Colorizer** - Làm nổi bật cặp ngoặc
7. **Path Intellisense** - Gợi ý đường dẫn file
8. **GitLens** - Quản lý Git tốt hơn
9. **Prettier** - Format code tự động
10. **Material Icon Theme** - Icon đẹp cho file/folder

**Cấu hình VS Code cho PHP:**

File → Preferences → Settings → Tìm "php" → Cấu hình:
```json
{
  "php.validate.executablePath": "C:/xampp/php/php.exe", // Windows
  // "php.validate.executablePath": "/Applications/XAMPP/bin/php", // Mac
  // "php.validate.executablePath": "/opt/lampp/bin/php", // Linux
  "php.suggest.basic": true,
  "editor.formatOnSave": true
}
```

---

### 3. Cài đặt Git (Quản lý mã nguồn)

**Windows:**
1. Tải từ: https://git-scm.com/download/win
2. Cài đặt với các tùy chọn mặc định
3. Chọn "Use Git from the Windows Command Prompt"

**Mac:**
```bash
# Cài đặt qua Homebrew (nếu chưa có Homebrew)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Cài đặt Git
brew install git
```

**Linux:**
```bash
sudo apt update
sudo apt install git
```

**Cấu hình Git lần đầu:**
```bash
git config --global user.name "Tên của bạn"
git config --global user.email "email@example.com"
```

**Kiểm tra cài đặt:**
```bash
git --version
# Kết quả: git version 2.x.x
```

---

### 4. Trình duyệt và Developer Tools

**Khuyến nghị sử dụng:**
- **Google Chrome** hoặc **Mozilla Firefox** (có Developer Tools mạnh mẽ)

**Cài đặt Extensions hữu ích cho Chrome:**
1. **EditThisCookie** - Quản lý cookies
2. **JSON Formatter** - Hiển thị JSON đẹp
3. **Wappalyzer** - Phát hiện công nghệ website
4. **ColorZilla** - Lấy mã màu từ trang web

**Học cách sử dụng Developer Tools:**
- Mở: F12 hoặc Ctrl+Shift+I
- **Elements**: Xem HTML/CSS
- **Console**: Xem lỗi JavaScript
- **Network**: Theo dõi requests (quan trọng cho AJAX, API)
- **Application**: Xem cookies, session storage

---

## BƯỚC 1.2: CẤU HÌNH XAMPP CHO DỰ ÁN

### Tạo Virtual Host (Tùy chọn nhưng khuyến nghị)

**Lợi ích:** Thay vì truy cập `http://localhost/bakery-shop`, bạn có thể dùng `http://bakery-shop.local`

**Cấu hình:**

1. **Chỉnh file hosts:**

Windows: `C:\Windows\System32\drivers\etc\hosts`
Mac/Linux: `/etc/hosts`

Thêm dòng:
```
127.0.0.1    bakery-shop.local
```

2. **Cấu hình Apache Virtual Host:**

File: `C:\xampp\apache\conf\extra\httpd-vhost.conf` (Windows)
File: `/Applications/XAMPP/etc/extra/httpd-vhosts.conf` (Mac)

Thêm vào cuối file:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/bakery-shop"
    ServerName bakery-shop.local
    <Directory "C:/xampp/htdocs/bakery-shop">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. **Restart Apache** từ XAMPP Control Panel

4. **Kiểm tra:** Truy cập `http://bakery-shop.local`

---

### Cấu hình PHP

**File cấu hình:** `C:\xampp\php\php.ini`

**Các thiết lập quan trọng cần kiểm tra:**

```ini
; Tăng kích thước upload file (cho ảnh sản phẩm)
upload_max_filesize = 20M
post_max_size = 25M

; Hiển thị lỗi khi phát triển (TẮT khi lên production)
display_errors = On
error_reporting = E_ALL

; Bật extension cần thiết (bỏ dấu ; ở đầu dòng)
extension=mysqli
extension=pdo_mysql
extension=gd          ; Xử lý ảnh
extension=mbstring    ; Xử lý chuỗi UTF-8
extension=curl        ; Gọi API (VNPay)

; Timezone (cho Việt Nam)
date.timezone = Asia/Ho_Chi_Minh
```

**Sau khi sửa, RESTART Apache!**

---

### Cấu hình MySQL/MariaDB

**Đăng nhập phpMyAdmin:**
1. Truy cập: `http://localhost/phpmyadmin`
2. Username: `root`
3. Password: (để trống)

**Tạo user và password cho dự án (Bảo mật):**

```sql
-- Tạo user mới
CREATE USER 'bakery_admin'@'localhost' IDENTIFIED BY 'Bakery@2024';

-- Cấp quyền
GRANT ALL PRIVILEGES ON bakery_db.* TO 'bakery_admin'@'localhost';

-- Áp dụng thay đổi
FLUSH PRIVILEGES;
```

**Lưu thông tin này để dùng cho file config sau:**
- Host: localhost
- Username: bakery_admin
- Password: Bakery@2024
- Database: bakery_db

---

## KIỂM TRA HOÀN TẤT BƯỚC 1.1

✅ XAMPP đã cài đặt và chạy được Apache + MySQL
✅ Truy cập được `http://localhost` và phpMyAdmin
✅ VS Code đã cài đặt kèm extensions PHP
✅ Git đã cài đặt và cấu hình
✅ Trình duyệt có Developer Tools
✅ PHP đã cấu hình upload_max_filesize và extensions
✅ MySQL đã tạo user cho dự án

**Nếu tất cả đã OK, chuyển sang BƯỚC 1.2: Tạo cấu trúc thư mục dự án!**

---

## XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi 1: Apache không khởi động được

**Nguyên nhân:** Port 80 hoặc 443 đã được sử dụng (thường bởi Skype, IIS)

**Giải pháp:**
1. XAMPP Control Panel → Click Config bên cạnh Apache
2. Chọn `httpd.conf`
3. Tìm `Listen 80` → Đổi thành `Listen 8080`
4. Lưu lại và restart Apache
5. Truy cập: `http://localhost:8080`

### Lỗi 2: MySQL không khởi động

**Giải pháp:**
1. Kiểm tra port 3306 có bị chiếm không
2. Hoặc đổi port MySQL trong config

### Lỗi 3: PHP không chạy, tải file .php thay vì thực thi

**Giải pháp:**
- Đảm bảo file nằm trong `htdocs` của XAMPP
- Truy cập qua `http://localhost/...` chứ không phải mở file trực tiếp
- Kiểm tra Apache đã start chưa

### Lỗi 4: Extension PHP không load được

**Giải pháp:**
1. Kiểm tra `php.ini` đã bỏ dấu `;` chưa
2. Kiểm tra đường dẫn `extension_dir` trong php.ini
3. Restart Apache sau khi sửa

---

## TÀI NGUYÊN HỌC TẬP THÊM

**Học PHP cơ bản:**
- W3Schools PHP Tutorial: https://www.w3schools.com/php/
- PHP Manual: https://www.php.net/manual/en/

**Học MySQL:**
- W3Schools SQL: https://www.w3schools.com/sql/
- MySQL Documentation: https://dev.mysql.com/doc/

**Video tutorials (YouTube):**
- "PHP Tutorial for Beginners" - Programming with Mosh
- "MySQL Tutorial for Beginners" - Programming with Mosh

Bạn đã sẵn sàng cho BƯỚC 1.2 chưa? 🚀
