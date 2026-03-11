-- ============================================================
-- SAMPLE DATA FOR BAKERY SHOP DATABASE
-- File: database/sample_data.sql
-- Mô tả: Dữ liệu mẫu với password đã hash đúng
-- ============================================================

USE anb_db;

-- ============================================================
-- 1. USERS DATA (password đã hash bằng password_hash())
-- ============================================================
-- Password cho tất cả user: 123456
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi


DELETE FROM orders;



DELETE FROM users;

INSERT INTO users (user_id, username, password, full_name, email, phone, address, role, is_active, created_at) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', 'admin@bakeryshop.vn', '0901234567', '123 Đường ABC, Quận 1, TP.HCM', 'admin', 1, NOW()),
(2, 'nguyenvana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'nguyenvana@gmail.com', '0912345678', '456 Đường XYZ, Quận 2, TP.HCM', 'customer', 1, NOW()),
(3, 'tranthib', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'tranthib@gmail.com', '0923456789', '789 Đường DEF, Quận 3, TP.HCM', 'customer', 1, NOW()),
(4, 'phamvanc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn C', 'phamvanc@gmail.com', '0934567890', '321 Đường GHI, Quận 4, TP.HCM', 'customer', 1, NOW()),
(5, 'lehoangd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Hoàng D', 'lehoangd@gmail.com', '0945678901', '654 Đường JKL, Quận 5, TP.HCM', 'customer', 1, NOW());

-- ============================================================
-- 2. CATEGORIES DATA
-- ============================================================


DELETE FROM products;




DELETE FROM categories;

INSERT INTO categories (category_id, category_name, description, display_order, is_active, created_at) VALUES
(1, 'Bánh Kem', 'Các loại bánh kem sinh nhật, bánh kem tươi cao cấp', 1, 1, NOW()),
(2, 'Bánh Mì', 'Bánh mì tươi, bánh mì sandwich, bánh mì ngọt', 2, 1, NOW()),
(3, 'Cookies', 'Bánh quy bơ, cookies chocolate, cookies hạt', 3, 1, NOW()),
(4, 'Bánh Bông Lan', 'Bánh bông lan trứng muối, bông lan phô mai', 4, 1, NOW()),
(5, 'Bánh Ngọt Pháp', 'Macaron, Eclair, Croissant, Pain au Chocolat', 5, 1, NOW()),
(6, 'Bánh Truyền Thống', 'Bánh Trung Thu, bánh pía, bánh dẻo', 6, 1, NOW());

-- ============================================================
-- 3. PRODUCTS DATA
-- ============================================================



INSERT INTO products (product_id, product_name, category_id, description, price, stock_quantity, image_url, is_active, view_count, sold_count, created_at) VALUES
-- Bánh Kem
(1, 'Bánh Kem Sinh Nhật Dâu Tây', 1, 'Bánh kem tươi với nhân dâu tây tươi ngon, lớp kem mềm mịn', 350000, 15, 'uploads/products/banh-kem-dau.jpg', 1, 120, 25, NOW()),
(2, 'Bánh Kem Socola Đen', 1, 'Bánh kem socola đậm đà với lớp ganache socola Bỉ cao cấp', 380000, 12, 'uploads/products/banh-kem-socola.jpg', 1, 95, 18, NOW()),
(3, 'Bánh Kem Tiramisu', 1, 'Bánh kem Tiramisu truyền thống Ý với cà phê Espresso thơm nồng', 420000, 10, 'uploads/products/banh-tiramisu.jpg', 1, 150, 32, NOW()),
(4, 'Bánh Kem Trà Xanh', 1, 'Bánh kem trà xanh Nhật Bản với matcha nguyên chất', 360000, 8, 'uploads/products/banh-kem-tra-xanh.jpg', 1, 88, 15, NOW()),

-- Bánh Mì
(5, 'Bánh Mì Bơ Tỏi', 2, 'Bánh mì giòn tan với bơ tỏi thơm phức', 45000, 50, 'uploads/products/banh-mi-bo-toi.jpg', 1, 200, 85, NOW()),
(6, 'Bánh Mì Sandwich Gà', 2, 'Sandwich với gà nướng, rau xanh và sốt mayonnaise', 55000, 40, 'uploads/products/sandwich-ga.jpg', 1, 180, 72, NOW()),
(7, 'Bánh Mì Phô Mai', 2, 'Bánh mì mềm với nhân phô mai béo ngậy', 35000, 60, 'uploads/products/banh-mi-pho-mai.jpg', 1, 165, 68, NOW()),
(8, 'Bánh Mì Que', 2, 'Bánh mì que giòn rụm, thích hợp ăn kèm', 25000, 80, 'uploads/products/banh-mi-que.jpg', 1, 145, 95, NOW()),

-- Cookies
(9, 'Cookies Bơ', 3, 'Bánh quy bơ truyền thống Đan Mạch thơm ngon', 120000, 30, 'uploads/products/cookies-bo.jpg', 1, 110, 42, NOW()),
(10, 'Cookies Chocolate Chip', 3, 'Cookies với chocolate chips Bỉ cao cấp', 140000, 25, 'uploads/products/cookies-chocolate.jpg', 1, 135, 48, NOW()),
(11, 'Cookies Yến Mạch', 3, 'Cookies yến mạch healthy với hạt dinh dưỡng', 130000, 28, 'uploads/products/cookies-yen-mach.jpg', 1, 98, 35, NOW()),

-- Bánh Bông Lan
(12, 'Bánh Bông Lan Trứng Muối', 4, 'Bánh bông lan mềm xốp với nhân trứng muối béo ngậy', 280000, 20, 'uploads/products/bong-lan-trung-muoi.jpg', 1, 175, 55, NOW()),
(13, 'Bánh Bông Lan Phô Mai', 4, 'Bánh bông lan với phô mai Hokkaido thơm béo', 320000, 15, 'uploads/products/bong-lan-pho-mai.jpg', 1, 160, 48, NOW()),
(14, 'Bánh Bông Lan Vanilla', 4, 'Bánh bông lan vanilla truyền thống mềm mịn', 250000, 25, 'uploads/products/bong-lan-vanilla.jpg', 1, 125, 38, NOW()),

-- Bánh Ngọt Pháp
(15, 'Macaron Pháp', 5, 'Set 12 chiếc macaron nhiều vị: dâu, socola, chanh leo, pistachio', 280000, 18, 'uploads/products/macaron.jpg', 1, 190, 52, NOW()),
(16, 'Eclair Socola', 5, 'Eclair truyền thống với kem patisserie và socola', 65000, 35, 'uploads/products/eclair.jpg', 1, 140, 45, NOW()),
(17, 'Croissant Bơ', 5, 'Croissant giòn tan với nhiều lớp bơ thơm', 45000, 45, 'uploads/products/croissant.jpg', 1, 220, 88, NOW()),
(18, 'Pain au Chocolat', 5, 'Bánh ngọt Pháp với thanh chocolate bên trong', 50000, 40, 'uploads/products/pain-au-chocolat.jpg', 1, 155, 62, NOW()),

-- Bánh Truyền Thống
(19, 'Bánh Trung Thu Thập Cẩm', 6, 'Bánh Trung Thu truyền thống với nhân thập cẩm đầy đặn', 180000, 50, 'uploads/products/banh-trung-thu.jpg', 1, 85, 125, NOW()),
(20, 'Bánh Pía Sóc Trăng', 6, 'Bánh pía Sóc Trăng với nhân đậu xanh thơm ngon', 120000, 40, 'uploads/products/banh-pia.jpg', 1, 92, 78, NOW()),
(21, 'Bánh Dẻo Đậu Xanh', 6, 'Bánh dẻo mềm mịn với nhân đậu xanh ngọt dịu', 150000, 35, 'uploads/products/banh-deo.jpg', 1, 78, 65, NOW()),
(22, 'Bánh Cốm Xanh', 6, 'Bánh cốm xanh truyền thống Hà Nội thơm dẻo', 160000, 30, 'uploads/products/banh-com.jpg', 1, 105, 72, NOW());

-- ============================================================
-- 4. ORDERS DATA
-- ============================================================



INSERT INTO orders (order_id, user_id, order_code, order_date, total_amount, payment_method, payment_status, order_status, shipping_name, shipping_phone, shipping_address, customer_note) VALUES
(1, 2, 'DH20240209001', '2024-02-09 10:30:00', 730000, 'COD', 'pending', 'completed', 'Nguyễn Văn A', '0912345678', '456 Đường XYZ, Quận 2, TP.HCM', 'Giao giờ hành chính'),
(2, 3, 'DH20240210001', '2024-02-10 14:15:00', 500000, 'VNPAY', 'paid', 'shipping', 'Trần Thị B', '0923456789', '789 Đường DEF, Quận 3, TP.HCM', NULL),
(3, 4, 'DH20240211001', '2024-02-11 09:20:00', 420000, 'COD', 'pending', 'processing', 'Phạm Văn C', '0934567890', '321 Đường GHI, Quận 4, TP.HCM', 'Gọi trước khi giao'),
(4, 5, 'DH20240212001', '2024-02-12 16:45:00', 565000, 'COD', 'pending', 'pending', 'Lê Hoàng D', '0945678901', '654 Đường JKL, Quận 5, TP.HCM', NULL);

-- ============================================================
-- 5. ORDER DETAILS DATA
-- ============================================================

DELETE FROM order_details;

INSERT INTO order_details (detail_id, order_id, product_id, product_name, quantity, unit_price) VALUES
-- Order 1
(1, 1, 1, 'Bánh Kem Sinh Nhật Dâu Tây', 2, 350000),
(2, 1, 5, 'Bánh Mì Bơ Tỏi', 2, 45000),

-- Order 2
(3, 2, 3, 'Bánh Kem Tiramisu', 1, 420000),
(4, 2, 9, 'Cookies Bơ', 1, 120000),

-- Order 3
(5, 3, 15, 'Macaron Pháp', 1, 280000),
(6, 3, 16, 'Eclair Socola', 2, 65000),

-- Order 4
(7, 4, 12, 'Bánh Bông Lan Trứng Muối', 2, 280000),
(8, 4, 17, 'Croissant Bơ', 1, 45000);

-- ============================================================
-- 6. CART DATA (Giỏ hàng mẫu)
-- ============================================================

DELETE FROM cart;

INSERT INTO cart (cart_id, user_id, product_id, quantity, added_at) VALUES
(1, 2, 4, 1, NOW()),
(2, 3, 10, 2, NOW());

-- ============================================================
-- RESET AUTO_INCREMENT
-- ============================================================

ALTER TABLE users AUTO_INCREMENT = 6;
ALTER TABLE categories AUTO_INCREMENT = 7;
ALTER TABLE products AUTO_INCREMENT = 23;
ALTER TABLE orders AUTO_INCREMENT = 5;
ALTER TABLE order_details AUTO_INCREMENT = 9;
ALTER TABLE cart AUTO_INCREMENT = 3;

-- ============================================================
-- VERIFICATION
-- ============================================================

SELECT 'Data imported successfully!' as Status;
SELECT 'Users count:', COUNT(*) FROM users;
SELECT 'Categories count:', COUNT(*) FROM categories;
SELECT 'Products count:', COUNT(*) FROM products;
SELECT 'Orders count:', COUNT(*) FROM orders;

-- ============================================================
-- TEST ACCOUNTS
-- ============================================================
-- Admin:    admin / 123456
-- Customer: nguyenvana / 123456
-- Customer: tranthib / 123456
-- Customer: phamvanc / 123456
-- Customer: lehoangd / 123456
-- ============================================================
