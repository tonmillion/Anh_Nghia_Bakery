<?php
/**
 * Order Success page
 * File: user/pages/order-success.php
 */

require_once '../../includes/init.php';

require_login();

$page_title = 'Đặt hàng thành công - ' . SITE_NAME;

// Lấy order code từ URL
$order_code = isset($_GET['order']) ? sanitize($_GET['order']) : '';

if (empty($order_code)) {
    redirect(url('index.php'));
}

// Lấy thông tin đơn hàng
$order = new Order();
$db = getDB();

$stmt = $db->prepare("SELECT * FROM orders WHERE order_code = ? AND user_id = ?");
$stmt->execute([$order_code, get_user_id()]);
$order_info = $stmt->fetch();

if (!$order_info) {
    set_flash('error', 'Không tìm thấy đơn hàng');
    redirect(url('index.php'));
}

// Lấy chi tiết đơn hàng
$order_details = $order->getOrderDetails($order_info['order_id']);

// Include header
include '../../includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('user/css/order-success.css') ?>?v=<?= time() ?>">

<div class="success-page">
    <div class="container">
        <!-- Success Message -->
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <div class="success-box">
            <h2>Đặt Hàng Thành Công!</h2>
            <p>Cảm ơn bạn đã đặt hàng tại <?= SITE_NAME ?>. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
            
            <div class="order-code">
                <i class="fas fa-barcode"></i> Mã đơn hàng: <?= htmlspecialchars($order_info['order_code']) ?>
            </div>
            
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle"></i> Vui lòng lưu lại mã đơn hàng để tra cứu
            </p>
        </div>
        
        <!-- Order Information -->
        <div class="row">
            <div class="col-lg-6">
                <div class="order-info-box">
                    <h4><i class="fas fa-info-circle"></i> Thông Tin Đơn Hàng</h4>
                    
                    <div class="info-row">
                        <span class="info-label">Mã đơn hàng:</span>
                        <span class="info-value"><?= htmlspecialchars($order_info['order_code']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Ngày đặt:</span>
                        <span class="info-value"><?= format_date($order_info['order_date']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Trạng thái:</span>
                        <span class="info-value">
                            <span class="badge bg-warning">
                                <?= ORDER_STATUS[$order_info['order_status']] ?? $order_info['order_status'] ?>
                            </span>
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Thanh toán:</span>
                        <span class="info-value"><?= PAYMENT_METHODS[$order_info['payment_method']] ?? $order_info['payment_method'] ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Tổng tiền:</span>
                        <span class="info-value text-theme-price" style="font-size: 20px;">
                            <?= format_currency($order_info['total_amount']) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="order-info-box">
                    <h4><i class="fas fa-shipping-fast"></i> Thông Tin Giao Hàng</h4>
                    
                    <div class="info-row">
                        <span class="info-label">Người nhận:</span>
                        <span class="info-value"><?= htmlspecialchars($order_info['shipping_name']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Số điện thoại:</span>
                        <span class="info-value"><?= htmlspecialchars($order_info['shipping_phone']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Địa chỉ:</span>
                        <span class="info-value"><?= htmlspecialchars($order_info['shipping_address']) ?></span>
                    </div>
                    
                    <?php if ($order_info['customer_note']): ?>
                    <div class="info-row">
                        <span class="info-label">Ghi chú:</span>
                        <span class="info-value"><?= htmlspecialchars($order_info['customer_note']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-info-box">
            <h4><i class="fas fa-list"></i> Chi Tiết Sản Phẩm</h4>
            
            <div class="order-items">
                <?php foreach ($order_details as $item): ?>
                <div class="order-item">
                    <img src="<?= upload($item['image_url'] ?? 'products/default.jpg') ?>" 
                         alt="<?= htmlspecialchars($item['product_name']) ?>"
                         class="order-item-image"
                         onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                    
                    <div class="order-item-info">
                        <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                        <p class="mb-0 text-muted">
                            Số lượng: <?= $item['quantity'] ?> × <?= format_currency($item['unit_price']) ?>
                        </p>
                    </div>
                    
                    <div class="text-end">
                        <strong class="text-theme-price"><?= format_currency($item['subtotal']) ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?= url('index.php') ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag"></i> Tiếp tục mua hàng
            </a>
            <a href="<?= url('user/pages/orders.php') ?>" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-list"></i> Xem đơn hàng
            </a>
        </div>
    </div>
</div>

<?php include '../../includes/layouts/footer.php'; ?>