# CHECKLIST DỰ ÁN ANH NGHĨA BAKERY

## ✅ BƯỚC 1: CHUẨN BỊ MÔI TRƯỜNGs

- [x] Cài đặt XAMPP/WAMP
- [x] Cài đặt Visual Studio Code + Extensions
- [x] Cài đặt Git
- [x] Tạo cấu trúc thư mục dự án
- [x] Tạo file .htaccess (bảo mật)
- [x] Tạo file .gitignore
- [x] Tạo README.md
- [x] Tạo index.php cơ bản

---

## ⏳ BƯỚC 2: THIẾT KẾ CƠ SỞ DỮ LIỆU

- [x] Tạo database `anb_db`
- [x] Thiết kế ERD (Entity Relationship Diagram)
- [x] Tạo bảng `users` (người dùng)
- [x] Tạo bảng `categories` (danh mục)
- [x] Tạo bảng `products` (sản phẩm)
- [x] Tạo bảng `orders` (đơn hàng)
- [x] Tạo bảng `order_details` (chi tiết đơn hàng)
- [x] Tạo các index và foreign key
- [x] Insert dữ liệu mẫu (sample data)
- [x] Test query cơ bản

---

## ⏳ BƯỚC 3: XÂY DỰNG TẦNG KẾT NỐI

- [x] `config/database.php` - Kết nối PDO/MySQLi
- [x] `config/config.php` - Hằng số hệ thống
- [x] `includes/classes/Database.php` - Class Database
- [x] `includes/classes/User.php` - Class User
- [x] `includes/classes/Product.php` - Class Product
- [x] `includes/classes/Cart.php` - Class Cart
- [x] `includes/classes/Order.php` - Class Order
- [x] `includes/classes/Category.php` - Class Category
- [x] `includes/functions.php` - Helper functions
- [x] `includes/session.php` - Session management
- [x] Test kết nối database

---

## ⏳ BƯỚC 4: PHÁT TRIỂN PHÂN HỆ KHÁCH HÀNG

### 4.1. Xác thực người dùng
- [x] `user/register.php` - Đăng ký
- [x] `user/login.php` - Đăng nhập
- [x] `user/logout.php` - Đăng xuất
- [x] Validation form (client + server)
- [x] Mã hóa mật khẩu (password_hash)

### 4.2. Trang chủ và Header/Footer
- [x] `index.php` - Trang chủ hoàn chỉnh
- [x] `includes/header.php` - Header user
- [x] `includes/footer.php` - Footer user
- [x] Menu điều hướng
- [x] Giỏ hàng mini (header)

### 4.3. Sản phẩm
- [x] `user/pages/products.php` - Danh sách sản phẩm
- [x] `user/pages/product-detail.php` - Chi tiết sản phẩm
- [x] Tìm kiếm sản phẩm
- [x] Lọc theo danh mục
- [ ] Lọc theo khoảng giá
- [x] Phân trang (pagination)
- [x] Sắp xếp (sort)

### 4.4. Giỏ hàng
- [x] `user/pages/cart.php` - Trang giỏ hàng
- [x] Thêm sản phẩm vào giỏ (AJAX)
- [x] Cập nhật số lượng (AJAX)
- [x] Xóa sản phẩm (AJAX)
- [x] Tính tổng tiền tự động

### 4.5. Checkout và Thanh toán
- [x] `user/pages/checkout.php` - Trang thanh toán
- [x] Form thông tin giao hàng
- [x] Chọn phương thức thanh toán
- [x] Tích hợp VNPay
- [x] Xử lý callback VNPay
- [ ] Gửi email xác nhận đơn hàng

### 4.6. Tài khoản
- [x] `user/pages/account.php` - Thông tin cá nhân
- [ ] `user/pages/order-history.php` - Lịch sử đơn hàng
- [x] Cập nhật thông tin tài khoản
- [x] Đổi mật khẩu

---

## ⏳ BƯỚC 5: PHÁT TRIỂN PHÂN HỆ QUẢN TRỊ

### 5.1. Xác thực Admin
- [x] `admin/login.php` - Đăng nhập admin
- [x] Middleware kiểm tra role
- [x] Session admin

### 5.2. Giao diện Admin
- [x] `admin/includes/header.php` - Header admin
- [x] `admin/includes/sidebar.php` - Sidebar menu
- [x] `admin/includes/footer.php` - Footer admin
- [ ] Layout AdminLTE hoặc Bootstrap

### 5.3. Dashboard
- [x] `admin/pages/dashboard.php` - Trang tổng quan
- [x] Thống kê đơn hàng mới
- [ ] Thống kê doanh thu
- [ ] Biểu đồ doanh thu (Chart.js)
- [x] Top sản phẩm bán chạy

### 5.4. Quản lý Sản phẩm
- [x] `admin/pages/products.php` - Danh sách sản phẩm
- [x] Thêm sản phẩm mới
- [x] Sửa sản phẩm
- [x] Xóa/Ẩn sản phẩm
- [x] Upload ảnh sản phẩm
- [x] Quản lý tồn kho

### 5.5. Quản lý Danh mục
- [x] `admin/pages/categories.php` - Quản lý danh mục
- [x] CRUD danh mục
- [x] Sắp xếp thứ tự hiển thị

### 5.6. Quản lý Đơn hàng
- [x] `admin/pages/orders.php` - Danh sách đơn hàng
- [x] Xem chi tiết đơn hàng
- [x] Cập nhật trạng thái đơn hàng
- [x] In hóa đơn
- [x] Lọc đơn theo trạng thái
- [x] Xác nhận thanh toán

### 5.7. Quản lý Khách hàng
- [x] `admin/pages/customers.php` - Danh sách khách hàng
- [x] Xem lịch sử mua hàng
- [x] Khóa/Mở khóa tài khoản

### 5.8. Báo cáo
- [ ] `admin/pages/reports.php` - Báo cáo thống kê
- [ ] Doanh thu theo ngày/tháng
- [x] Sản phẩm bán chạy
- [ ] Export Excel

---

## ⏳ BƯỚC 6: GIAO DIỆN RESPONSIVE

- [ ] Tích hợp Bootstrap 5
- [ ] Thiết kế responsive cho mobile
- [ ] Thiết kế responsive cho tablet
- [ ] Menu hamburger (mobile)
- [ ] Grid layout linh hoạt
- [ ] Test trên nhiều thiết bị

---

## ⏳ BƯỚC 7: BẢO MẬT VÀ TỐI ƯU

### 7.1. Bảo mật
- [ ] Prepared statements (SQL Injection)
- [ ] Escape output (XSS)
- [ ] CSRF token
- [ ] Validation nghiêm ngặt
- [ ] Rate limiting login
- [ ] Bảo mật upload file

### 7.2. Tối ưu hiệu năng
- [ ] Index database
- [ ] Lazy loading images
- [ ] Minify CSS/JS
- [ ] GZIP compression
- [ ] Caching (nếu cần)
- [ ] Optimize queries

---

## ⏳ BƯỚC 8: TÍCH HỢP VNPAY

- [x] Đăng ký tài khoản VNPay Sandbox
- [x] Lấy TMN Code và Hash Secret
- [x] `config/vnpay_config.php` - Cấu hình
- [x] Tạo URL thanh toán
- [x] Xử lý IPN (callback)
- [x] Verify checksum
- [x] Cập nhật trạng thái thanh toán
- [x] Test thanh toán sandbox

---

## ⏳ BƯỚC 9: KIỂM THỬ

### 9.1. Kiểm thử chức năng
- [ ] Test đăng ký/đăng nhập
- [ ] Test tìm kiếm sản phẩm
- [ ] Test giỏ hàng
- [ ] Test đặt hàng
- [ ] Test thanh toán COD
- [ ] Test thanh toán VNPay
- [ ] Test admin dashboard
- [ ] Test quản lý sản phẩm
- [ ] Test quản lý đơn hàng

### 9.2. Kiểm thử bảo mật
- [ ] Test SQL injection
- [ ] Test XSS
- [ ] Test CSRF
- [ ] Test session hijacking
- [ ] Test file upload vulnerability
- [ ] Test authentication bypass

### 9.3. Kiểm thử hiệu năng
- [ ] Test tốc độ tải trang
- [ ] Test với nhiều sản phẩm
- [ ] Test đồng thời nhiều user
- [ ] Sử dụng Apache JMeter

### 9.4. Kiểm thử tương thích
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile (iOS/Android)

---

## ⏳ BƯỚC 10: TRIỂN KHAI

- [ ] Chọn hosting (có SSL)
- [ ] Export database
- [ ] Upload code
- [ ] Import database
- [ ] Cấu hình database.php
- [ ] Cấu hình vnpay_config.php
- [ ] Kiểm tra .htaccess
- [ ] Test lại toàn bộ chức năng
- [ ] Bật HTTPS
- [ ] Tắt display_errors
- [ ] Setup backup tự động
- [ ] Setup monitoring

---

## 📊 TIẾN ĐỘ TỔNG QUAN

- **Hoàn thành**: 8/60 tasks (13%)
- **Đang làm**: Bước 2
- **Thời gian ước tính**: 4-6 tuần
- **Ngày bắt đầu**: [Điền ngày bắt đầu]
- **Deadline dự kiến**: [Điền deadline]

---

## 📝 GHI CHÚ

- Mỗi khi hoàn thành một task, đánh dấu [x]
- Ghi lại vấn đề gặp phải và cách giải quyết
- Update tiến độ hàng ngày

---

**Last updated**: 2024-02-09
