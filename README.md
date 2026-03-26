# BAKERY SHOP - WEBSITE BÁN BÁNH NGỌT

## CẤU TRÚC THỦ MỤC DỰ ÁN

```
bakery-shop/
│
├── admin/                          # PHÂN HỆ QUẢN TRỊ (Admin Panel)
│   ├── css/                        # CSS riêng cho admin
│   ├── js/                         # JavaScript riêng cho admin
│   ├── includes/                   # Các file include cho admin
│   │   ├── header.php             # Header admin
│   │   ├── sidebar.php            # Sidebar menu                    cần tách ra
│   │   └── footer.php             # Footer admin
│   ├── pages/                      # Các trang chức năng admin
│   │   ├── dashboard.php          # Trang tổng quan                 là trang index
│   │   ├── products.php           # Quản lý sản phẩm
│   │   ├── product-add.php        # thêm sản phẩm 
│   │   ├── product-edit.php       # sửa sản phẩm    
│   │   ├── categories.php         # Quản lý danh mục
│   │   ├── orders.php             # Quản lý đơn hàng
|   |   ├── order-detail.php       # xem chi tiết đơn hàng
│   │   ├── users.php              # Quản lý khách hàng
│   │   ├── user-edit.php          # sửa thông tin khách hàng
│   │   └── reports.php            # Báo cáo thống kê               chưa có
│   ├── index.php                   # Trang chủ admin (redirect to dashboard)
│   └── login.php                   # Đăng nhập admin               trong user/
│
├── assets/                         # TÀI NGUYÊN DÙNG CHUNG (CSS, JS, Images)
│   ├── css/                        # CSS cho front-end
│   │   ├── bootstrap.min.css      # Bootstrap framework
│   │   ├── style.css              # Custom styles
│   │   └── responsive.css         # Media queries
│   ├── js/                         # JavaScript cho front-end
│   │   ├── jquery.min.js          # jQuery library
│   │   ├── bootstrap.min.js       # Bootstrap JS
│   │   └── main.js                # Custom JavaScript
│   ├── images/                     # Hình ảnh tĩnh (logo, banner, icons)
│   │   ├── logo.png
│   │   ├── banner/
│   │   └── icons/
│   └── fonts/                      # Web fonts (nếu cần)
│
├── config/                         # CẤU HÌNH HỆ THỐNG
│   ├── database.php               # Kết nối database
│   ├── config.php                 # Cấu hình chung (site name, URL...)
│   └── vnpay_config.php           # Cấu hình VNPay               trong config.php
│
├── includes/                       # FILE DÙNG CHUNG (Classes, Functions)
│   ├── classes/                    # OOP Classes
│   │   ├── User.php               # Class xử lý user
│   │   ├── Category.php           # Class xử lý danh mục
│   │   ├── VNPay.php              # Class xử lý VNPay
│   │   ├── Product.php            # Class xử lý product
│   │   ├── Cart.php               # Class xử lý giỏ hàng
│   │   ├── Order.php              # Class xử lý đơn hàng
│   │   └── Database.php           # Class kết nối DB (nếu dùng OOP)
│   ├── layouts/
│   │   ├── header.php             # Header cho user
│   │   ├── footer.php             # Footer cho user
│   ├── functions.php              # Các hàm tiện ích chung
│   ├── session.php                # Quản lý session
│   └── init.php                   # Khởi tạo hệ thống
│
├── user/                           # PHÂN HỆ KHÁCH HÀNG (Front-end)
│   ├── css/                        # CSS riêng cho user (nếu cần)
│   ├── js/                         # JavaScript riêng cho user
│   ├── pages/                      # Các trang chức năng
│   │   ├── products.php           # Danh sách sản phẩm
│   │   ├── product-detail.php     # Chi tiết sản phẩm
│   │   ├── cart.php               # Giỏ hàng
│   │   ├── cart-add.php           # Thêm sản phẩm vào giỏ hàng
│   │   ├── checkout.php           # Thanh toán
│   │   ├── order.php              # Lịch sử đơn hàng
│   │   ├── order-success.php      # Thông báo đặt hàng thành công
│   │   ├── order-cancel.php       # Hủy đơn hàng
│   │   ├── payment-return.php     # Trả về từ VNPay
│   │   ├── search.php             # Tìm kiếm sản phẩm
│   │   └── account.php            # Thông tin tài khoản
│   ├── login.php                   # Đăng nhập user
│   ├── register.php                # Đăng ký user
│   └── logout.php                  # Đăng xuất
│
├── uploads/                        # FILE UPLOAD (Ảnh sản phẩm...)
│   ├── products/                   # Ảnh sản phẩm
│   │   ├── product_1.jpg
│   │   └── product_2.jpg
│   └── temp/                       # File tạm thời
│
├── database/                       # SQL SCRIPTS
│   ├── bakery_db.sql              # File SQL tạo database
│   └── sample_data.sql            # Dữ liệu mẫu
│
├── .htaccess                       # Cấu hình Apache (URL rewrite, security)
├── index.php                       # TRANG CHỦ WEBSITE
├── about.php                       # Trang giới thiệu                còn thiếu
├── contact.php                     # Trang liên hệ                   còn thiếu
└── README.md                       # File này - Hướng dẫn dự án

```

---

## GIẢI THÍCH CHI TIẾT

### 📁 Thư mục `admin/`
Chứa toàn bộ giao diện và chức năng quản trị viên:
- **Dashboard**: Thống kê tổng quan (doanh thu, đơn hàng, sản phẩm)
- **Quản lý sản phẩm**: CRUD sản phẩm, upload ảnh, quản lý tồn kho
- **Quản lý đơn hàng**: Xem, cập nhật trạng thái đơn hàng
- **Quản lý khách hàng**: Danh sách users, xem lịch sử mua hàng

### 📁 Thư mục `assets/`
Chứa tài nguyên tĩnh dùng chung:
- **CSS**: Bootstrap + Custom styles
- **JS**: jQuery, Bootstrap, custom scripts
- **Images**: Logo, banner, icons trang trí
- **Fonts**: Font chữ tùy chỉnh (nếu có)

### 📁 Thư mục `config/`
Chứa các file cấu hình quan trọng:
- `database.php`: Thông tin kết nối DB (host, user, pass, dbname)
- `config.php`: Các hằng số như SITE_URL, SITE_NAME
- `vnpay_config.php`: TMN Code, Hash Secret của VNPay

> ⚠️ **Quan trọng**: Không commit file config có thông tin nhạy cảm lên Git public!

### 📁 Thư mục `includes/`
Chứa code xử lý logic nghiệp vụ:
- **Classes**: Các class OOP (User, Product, Cart, Order)
- **Functions**: Hàm tiện ích (format_currency, sanitize_input...)
- **Session**: Khởi tạo và quản lý session
- **Header/Footer**: File include dùng chung

### 📁 Thư mục `user/`
Phần giao diện người dùng cuối:
- Xem sản phẩm, tìm kiếm, lọc
- Thêm vào giỏ hàng
- Đặt hàng và thanh toán
- Xem lịch sử đơn hàng

### 📁 Thư mục `uploads/`
Lưu trữ file upload từ người dùng:
- **products/**: Ảnh sản phẩm (đặt tên theo product_id.jpg)
- **temp/**: File tạm (sẽ xóa định kỳ)

> ⚠️ **Bảo mật**: Phải cấu hình để không cho execute PHP trong thư mục này!

### 📁 Thư mục `database/`
Chứa file SQL:
- `bakery_db.sql`: Script tạo database và bảng
- `sample_data.sql`: Insert dữ liệu mẫu để test

---

## FLOW XỬ LÝ CƠ BẢN

### 1️⃣ Người dùng truy cập trang chủ
```
index.php
  ↓
includes/header.php (menu, logo, giỏ hàng)
  ↓
Hiển thị sản phẩm nổi bật (query từ DB)
  ↓
includes/footer.php
```

### 2️⃣ Người dùng xem danh sách sản phẩm
```
user/pages/products.php
  ↓
includes/classes/Product.php → getAllProducts()
  ↓
Hiển thị với phân trang
```

### 3️⃣ Thêm vào giỏ hàng
```
AJAX request → includes/classes/Cart.php
  ↓
addItem($product_id, $quantity)
  ↓
Lưu vào $_SESSION['cart']
  ↓
Trả về JSON response
```

### 4️⃣ Đặt hàng
```
user/pages/checkout.php
  ↓
includes/classes/Order.php → createOrder()
  ↓
Insert vào bảng orders và order_details
  ↓
Nếu chọn VNPay → Redirect đến cổng thanh toán
  ↓
VNPay callback → Cập nhật payment_status
```

### 5️⃣ Admin xử lý đơn hàng
```
admin/pages/orders.php
  ↓
includes/classes/Order.php → updateStatus()
  ↓
Update order_status trong DB
```

---

## NGUYÊN TẮC TỔ CHỨC CODE

### ✅ DOs (Nên làm)
1. **Phân tách rõ ràng**: Front-end (user) và Back-end (admin)
2. **Tái sử dụng**: Dùng includes cho header/footer
3. **OOP**: Sử dụng Class để quản lý logic
4. **Security**: Luôn validate input, escape output
5. **Comment**: Ghi chú code phức tạp

### ❌ DON'Ts (Không nên)
1. Đặt code xử lý logic trực tiếp trong file view
2. Hard-code thông tin nhạy cảm (password, API key)
3. Trộn lẫn HTML và PHP quá nhiều
4. Copy-paste code → Nên tạo function
5. Bỏ qua error handling

---

## CÁC FILE QUAN TRỌNG CẦN TẠO TIẾP

### Bước tiếp theo (Bước 2):
- [ ] `database/bakery_db.sql` - Script tạo database
- [ ] `config/database.php` - File kết nối DB
- [ ] `config/config.php` - Cấu hình hệ thống
- [ ] `.htaccess` - Cấu hình Apache

### Bước 3:
- [ ] `includes/classes/Database.php` - Class Database
- [ ] `includes/classes/User.php` - Class User
- [ ] `includes/classes/Product.php` - Class Product
- [ ] `includes/functions.php` - Helper functions

---

## GHI CHÚ BẢO MẬT

### File cần bảo vệ không cho truy cập trực tiếp:
```apache
# File .htaccess trong thư mục config/
<Files "*">
    Order Deny,Allow
    Deny from all
</Files>
```

### Ngăn chặn directory listing:
```apache
# File .htaccess gốc
Options -Indexes
```

### Chặn execute PHP trong uploads:
```apache
# File .htaccess trong uploads/
<Files *.php>
    deny from all
</Files>
```

---

## CONVENTIONS (Quy ước đặt tên)

### File names:
- Dùng lowercase, gạch nối: `product-detail.php`, `order-history.php`
- File class viết hoa chữ cái đầu: `User.php`, `Product.php`

### Database tables:
- Lowercase, số nhiều: `users`, `products`, `orders`
- Foreign key: `category_id`, `user_id`

### Variables:
- Snake_case: `$user_name`, `$total_amount`
- Constants: UPPERCASE: `SITE_URL`, `DB_HOST`

### Functions:
- camelCase hoặc snake_case nhất quán: `getUserById()` hoặc `get_user_by_id()`

---

## CHECKLIST HOÀN THÀNH BƯỚC 1

- [x] Cài đặt XAMPP
- [x] Cài đặt VS Code với extensions
- [x] Cài đặt Git
- [x] Tạo cấu trúc thư mục dự án
- [x] **TIẾP THEO**: Tạo database và các bảng (Bước 2)

🎉 **Bạn đã hoàn thành Bước 1!**

Sẵn sàng chuyển sang **Bước 2: Thiết kế Database** chưa?
