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

<style>
    .success-page {
        padding: 50px 0;
        min-height: 60vh;
    }
    
    .success-icon {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .success-icon i {
        font-size: 100px;
        color: #28a745;
        animation: scaleIn 0.5s ease-in-out;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .success-box {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 700px;
        margin: 0 auto 30px;
    }
    
    .success-box h2 {
        color: #28a745;
        font-size: 32px;
        margin-bottom: 15px;
    }
    
    .success-box p {
        color: #666;
        font-size: 16px;
        margin-bottom: 20px;
    }
    
    .order-code {
        font-size: 24px;
        font-weight: bold;
        color: #667eea;
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .order-info-box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e1e8ed;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #666;
        font-weight: 500;
    }
    
    .info-value {
        color: #333;
        font-weight: 600;
    }
    
    .order-items {
        margin-top: 20px;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    .order-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .order-item-info {
        flex: 1;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
</style>

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
                        <span class="info-value text-danger" style="font-size: 20px;">
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
                        <strong class="text-danger"><?= format_currency($item['subtotal']) ?></strong>
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