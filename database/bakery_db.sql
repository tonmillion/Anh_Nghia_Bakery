-- ============================================================
-- BAKERY SHOP DATABASE SCHEMA
-- Phiên bản: 1.0
-- Ngày tạo: 2024-02-09
-- Mô tả: Database cho website bán bánh ngọt
-- ============================================================

-- Xóa database nếu đã tồn tại (cẩn thận khi dùng trong production!)
DROP DATABASE IF EXISTS bakery_db;

-- Tạo database mới
CREATE DATABASE bakery_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Sử dụng database
USE bakery_db;

-- ============================================================
-- BẢNG 1: USERS (Người dùng)
-- Lưu thông tin tài khoản khách hàng và admin
-- ============================================================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bằng password_hash()',
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    address TEXT COMMENT 'Địa chỉ giao hàng mặc định',
    role ENUM('customer', 'admin') DEFAULT 'customer' COMMENT 'Phân quyền: customer hoặc admin',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = banned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG 2: CATEGORIES (Danh mục sản phẩm)
-- Phân loại bánh: bánh kem, bánh mì, cookies...
-- ============================================================

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    display_order INT DEFAULT 0 COMMENT 'Thứ tự hiển thị (số càng nhỏ càng ưu tiên)',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = hiển thị, 0 = ẩn',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_display_order (display_order),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG 3: PRODUCTS (Sản phẩm)
-- Thông tin chi tiết các loại bánh
-- ============================================================

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(200) NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL COMMENT 'Giá bán (VNĐ)',
    stock_quantity INT DEFAULT 0 COMMENT 'Số lượng tồn kho',
    image_url VARCHAR(255) COMMENT 'Đường dẫn ảnh sản phẩm',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = đang bán, 0 = ngưng bán',
    view_count INT DEFAULT 0 COMMENT 'Số lượt xem',
    sold_count INT DEFAULT 0 COMMENT 'Số lượng đã bán',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_category (category_id),
    INDEX idx_price (price),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (product_name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG 4: ORDERS (Đơn hàng)
-- Thông tin đơn đặt hàng
-- ============================================================

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'Mã đơn hàng (DH20240209001)',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL COMMENT 'Tổng tiền đơn hàng',
    
    -- Phương thức thanh toán
    payment_method ENUM('COD', 'VNPAY') DEFAULT 'COD' COMMENT 'COD = tiền mặt, VNPAY = chuyển khoản',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
    payment_date TIMESTAMP NULL COMMENT 'Ngày thanh toán',
    
    -- Trạng thái đơn hàng
    order_status ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') 
        DEFAULT 'pending' 
        COMMENT 'pending=chờ xác nhận, processing=đang làm, shipping=đang giao, completed=hoàn thành, cancelled=đã hủy',
    
    -- Thông tin giao hàng
    shipping_name VARCHAR(100) NOT NULL COMMENT 'Tên người nhận',
    shipping_phone VARCHAR(15) NOT NULL COMMENT 'SĐT người nhận',
    shipping_address TEXT NOT NULL COMMENT 'Địa chỉ giao hàng',
    
    -- Ghi chú
    customer_note TEXT COMMENT 'Ghi chú từ khách hàng',
    admin_note TEXT COMMENT 'Ghi chú nội bộ của admin',
    
    -- Thông tin VNPay (nếu thanh toán online)
    vnpay_transaction_id VARCHAR(50) COMMENT 'Mã giao dịch VNPay',
    vnpay_response TEXT COMMENT 'Response từ VNPay (JSON)',
    
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_order_code (order_code),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG 5: ORDER_DETAILS (Chi tiết đơn hàng)
-- Lưu các sản phẩm trong mỗi đơn hàng
-- ============================================================

CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL COMMENT 'Lưu lại tên sản phẩm tại thời điểm đặt hàng',
    quantity INT NOT NULL COMMENT 'Số lượng đặt mua',
    unit_price DECIMAL(10, 2) NOT NULL COMMENT 'Giá tại thời điểm đặt hàng',
    subtotal DECIMAL(10, 2) NOT NULL COMMENT 'Thành tiền = quantity * unit_price',
    
    FOREIGN KEY (order_id) REFERENCES orders(order_id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG 6: CART (Giỏ hàng - Tùy chọn)
-- Lưu giỏ hàng vào DB thay vì session (cho user đã đăng nhập)
-- ============================================================

CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(user_id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TRIGGER: Tự động tính subtotal khi insert order_details
-- ============================================================

DELIMITER //

CREATE TRIGGER before_insert_order_details
BEFORE INSERT ON order_details
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

CREATE TRIGGER before_update_order_details
BEFORE UPDATE ON order_details
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

DELIMITER ;

-- ============================================================
-- VIEW: Thống kê sản phẩm bán chạy
-- ============================================================

CREATE VIEW view_best_selling_products AS
SELECT 
    p.product_id,
    p.product_name,
    c.category_name,
    p.price,
    p.sold_count,
    p.stock_quantity,
    p.image_url
FROM products p
JOIN categories c ON p.category_id = c.category_id
WHERE p.is_active = 1
ORDER BY p.sold_count DESC;

-- ============================================================
-- VIEW: Thống kê đơn hàng theo trạng thái
-- ============================================================

CREATE VIEW view_order_statistics AS
SELECT 
    order_status,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue
FROM orders
GROUP BY order_status;

-- ============================================================
-- STORED PROCEDURE: Lấy doanh thu theo khoảng thời gian
-- ============================================================

DELIMITER //

CREATE PROCEDURE sp_get_revenue_by_date(
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    SELECT 
        DATE(order_date) as date,
        COUNT(*) as total_orders,
        SUM(total_amount) as revenue,
        AVG(total_amount) as avg_order_value
    FROM orders
    WHERE order_status = 'completed'
        AND DATE(order_date) BETWEEN start_date AND end_date
    GROUP BY DATE(order_date)
    ORDER BY date DESC;
END//

DELIMITER ;

-- ============================================================
-- STORED PROCEDURE: Cập nhật số lượng bán khi hoàn thành đơn
-- ============================================================

DELIMITER //

CREATE PROCEDURE sp_complete_order(
    IN p_order_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: Transaction rolled back' AS message;
    END;
    
    START TRANSACTION;
    
    -- Cập nhật trạng thái đơn hàng
    UPDATE orders 
    SET order_status = 'completed',
        payment_status = 'paid',
        payment_date = NOW()
    WHERE order_id = p_order_id;
    
    -- Cập nhật số lượng bán của sản phẩm
    UPDATE products p
    JOIN order_details od ON p.product_id = od.product_id
    SET p.sold_count = p.sold_count + od.quantity,
        p.stock_quantity = p.stock_quantity - od.quantity
    WHERE od.order_id = p_order_id;
    
    COMMIT;
    SELECT 'Order completed successfully' AS message;
END//

DELIMITER ;

-- ============================================================
-- FUNCTION: Tạo mã đơn hàng tự động
-- ============================================================

DELIMITER //

CREATE FUNCTION fn_generate_order_code()
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE order_count INT;
    DECLARE new_code VARCHAR(20);
    
    SELECT COUNT(*) INTO order_count FROM orders 
    WHERE DATE(order_date) = CURDATE();
    
    SET new_code = CONCAT('DH', DATE_FORMAT(NOW(), '%Y%m%d'), 
                         LPAD(order_count + 1, 3, '0'));
    
    RETURN new_code;
END//

DELIMITER ;

-- ============================================================
-- KẾT THÚC SCHEMA
-- ============================================================

-- Hiển thị danh sách bảng đã tạo
SHOW TABLES;

-- Hiển thị cấu trúc các bảng
SELECT 'Database bakery_db created successfully!' AS message;
SELECT CONCAT('Total tables: ', COUNT(*)) AS info 
FROM information_schema.tables 
WHERE table_schema = 'bakery_db';


-- ============================================================
-- BẢNG 6: CONTACTS (Liên hệ)
-- Lưu thông tin liên hệ từ khách hàng
-- ============================================================

CREATE TABLE IF NOT EXISTS contacts (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    admin_note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- Thêm một số contacts mẫu
INSERT INTO contacts (name, email, phone, subject, message, status) VALUES
('Nguyễn Văn A', 'nguyenvana@gmail.com', '0123456789', 'Hỏi về sản phẩm', 'Tôi muốn biết thêm thông tin về bánh kem sinh nhật', 'new'),
('Trần Thị B', 'tranthib@gmail.com', '0987654321', 'Đặt bánh theo yêu cầu', 'Tôi muốn đặt bánh cưới cho ngày 20/12', 'read'),
('Lê Văn C', 'levanc@gmail.com', '0912345678', 'Góp ý dịch vụ', 'Dịch vụ giao hàng rất tốt, cảm ơn shop!', 'replied');
 