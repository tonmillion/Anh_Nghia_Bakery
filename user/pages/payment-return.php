<?php
/**
 * VNPay return hander
 * File: user/pages/payment-return.php
 */

require_once '../../includes/init.php';

$page_title = 'Kết quả thanh toán . ' . SITE_NAME;

$vnpay = new VNPay();
$order = new Order();

// Xác thực callback từ VNPay
$result = $vnpay->verifyCallback($_GET);

// Lấy thông tin đơn hàng
$order_code = $result['data']['order_code'] ?? '';
$order_info = null;

if ($order_code) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_code = ?");
    $stmt->execute([$order_code]);
    $order_info = $stmt->fetch();
}

// Xử lý kết quả thanh toán
if ($result['success']) {
    // Thanh toán thành công
    if ($order_info) {
        // Cập nhật trạng thái thanh toán
        $order->updateOrderStatus($order_info['order_id'], 'paid');

        // Lưu thông tin giao dịch VNPay
        $transaction_data = [
            'order_id' => $order_info['order_id'],
            'transaction_no' => $result['data']['transaction_no'] ?? '',
            'bank_code' => $result['data']['bank_code'] ?? '',
            'card_type' => $result['data']['card_type'] ?? '',
            'amount' => $result['data']['amount'] ?? 0,
            'pay_date' => $result['data']['pay_date'] ?? date('YmdHis'),
        ];

        // Lưu vào bảng payment_transactions (nếu có)
        // savePaymentTransaction($transaction_data);
    }

    $success = true;
    $message = $result['message'];
} else {
    // Thanh toán thất bại
    if ($order_info) {
        // Cập nhật trạng thái thanh toán thất bại
        $order->updatePaymentStatus($order_info['order_id'], 'failed');
    }

    $success = false;
    $message = $result['message'];
}

// Include heaer
include '../../includes/layouts/header.php';
?>

<style>
    .payment-result {
        padding: 50px 0;
        min-height: 60vh;
    }
    
    .result-icon {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .result-icon i {
        font-size: 100px;
        animation: scaleIn 0.5s ease-in-out;
    }
    
    .result-icon.success i {
        color: #28a745;
    }
    
    .result-icon.failed i {
        color: #dc3545;
    }
    
    @keyframes scaleIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .result-box {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 700px;
        margin: 0 auto;
    }
    
    .result-box h2 {
        margin-bottom: 20px;
        font-size: 28px;
    }
    
    .result-box.success h2 {
        color: #28a745;
    }
    
    .result-box.failed h2 {
        color: #dc3545;
    }
    
    .transaction-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
</style>

<div class="payment-result">
    <div class="container">
        <!-- Icon -->
        <div class="result-icon <?= $success ? 'success' : 'failed' ?>">
            <i class="fas fa-<?= $success ? 'check-circle' : 'times-circle' ?>"></i>
        </div>
        
        <!-- Result Box -->
        <div class="result-box <?= $success ? 'success' : 'failed' ?>">
            <h2 class="text-center">
                <?= $success ? 'Thanh Toán Thành Công!' : 'Thanh Toán Thất Bại!' ?>
            </h2>
            
            <p class="text-center mb-4">
                <?= htmlspecialchars($message) ?>
            </p>
            
            <?php if ($order_info): ?>
                <div class="transaction-info">
                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Thông tin giao dịch</h5>
                    
                    <div class="info-row">
                        <span>Mã đơn hàng:</span>
                        <strong><?= htmlspecialchars($order_info['order_code']) ?></strong>
                    </div>
                    
                    <?php if ($success && isset($result['data']['transaction_no'])): ?>
                        <div class="info-row">
                            <span>Mã giao dịch VNPay:</span>
                            <strong><?= htmlspecialchars($result['data']['transaction_no']) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-row">
                        <span>Số tiền:</span>
                        <strong class="text-danger"><?= format_currency($order_info['total_amount']) ?></strong>
                    </div>
                    
                    <?php if ($success && isset($result['data']['bank_code'])): ?>
                        <div class="info-row">
                            <span>Ngân hàng:</span>
                            <strong><?= htmlspecialchars($result['data']['bank_code']) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success && isset($result['data']['card_type'])): ?>
                        <div class="info-row">
                            <span>Loại thẻ:</span>
                            <strong><?= htmlspecialchars($result['data']['card_type']) ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-row">
                        <span>Thời gian:</span>
                        <strong><?= format_date($order_info['order_date']) ?></strong>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <?php if ($success): ?>
                    <a href="<?= url('user/pages/order-success.php?order=' . $order_code) ?>" 
                       class="btn btn-success btn-lg">
                        <i class="fas fa-file-invoice"></i> Xem chi tiết đơn hàng
                    </a>
                    <a href="<?= url('user/pages/products.php') ?>" 
                       class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag"></i> Tiếp tục mua hàng
                    </a>
                <?php else: ?>
                    <a href="<?= url('user/pages/checkout.php') ?>" 
                       class="btn btn-warning btn-lg">
                        <i class="fas fa-redo"></i> Thử lại
                    </a>
                    <a href="<?= url('user/pages/cart.php') ?>" 
                       class="btn btn-secondary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Về giỏ hàng
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (!$success): ?>
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Lưu ý:</strong> Đơn hàng của bạn vẫn được lưu. 
                    Bạn có thể thử thanh toán lại hoặc chọn phương thức thanh toán khác (COD).
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/layouts/footer.php'; ?>