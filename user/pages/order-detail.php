<?php
/**
 * Order detail page (customer)
 * File: user/pages/order-detail.php
 */

require_once '../../includes/init.php';

// Bắt buộc đăng nhập
require_login();

// Lấy order code từ URL
$order_code = isset($_GET['order']) ? sanitize($_GET['order']) : '';

if (empty($order_code)) {
    set_flash('error', 'Đơn hàng không tồn tại');
    redirect(url('user/pages/orders.php'));
}

$order = new Order();
$db = getDB();

// Lấy thông tin đơn hàng
$stmt = $db->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->execute([$order_code]);
$order_info = $stmt->fetch();

// Kiểm tra đơn hàng có tồn tại và thuộc về user này không
if (!$order_info || $order_info['user_id'] != get_user_id()) {
    set_flash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập');
    redirect(url('user/pages/orders.php'));
}

// Lấy chi tiết sản phẩm
$order_details = $order->getOrderDetails($order_info['order_id']);

$page_title = 'Chi tiết đơn hàng #' . $order_code . ' - ' . SITE_NAME;

// Include header
include '../../includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('user/css/order-detail.css') ?>?v=<?= time() ?>">
 
<div class="order-detail-page">
    <div class="container">
        <!-- Order Header -->
        <div class="order-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><i class="fas fa-receipt"></i> Đơn hàng #<?= htmlspecialchars($order_info['order_code']) ?></h2>
                    <div class="order-date">
                        <i class="fas fa-clock"></i> Đặt lúc: <?= format_date($order_info['order_date'], 'd/m/Y H:i') ?>
                    </div>
                    <?php
                    $status_class = $order_info['order_status'];
                    $status_text = ORDER_STATUS[$order_info['order_status']] ?? $order_info['order_status'];
                    ?>
                    <div class="status-badge <?= $status_class ?>">
                        <?= $status_text ?>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= url('user/pages/orders.php') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Products List -->
                <div class="info-card">
                    <h4><i class="fas fa-box"></i> Sản phẩm đã đặt</h4>
                    
                    <?php foreach ($order_details as $item): ?>
                    <div class="product-item">
                        <img src="<?= upload($item['image_url'] ?? 'products/default.jpg') ?>" 
                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'">
                        
                        <div class="product-info">
                            <div class="product-name">
                                <a href="<?= url('user/pages/product-detail.php?id=' . $item['product_id']) ?>">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </a>
                            </div>
                            <div class="product-meta">
                                Số lượng: <strong><?= $item['quantity'] ?></strong>
                            </div>
                        </div>
                        
                        <div class="product-price">
                            <div class="unit-price">
                                <?= format_currency($item['unit_price']) ?> x <?= $item['quantity'] ?>
                            </div>
                            <div class="subtotal">
                                <?= format_currency($item['subtotal']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Order Summary -->
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Tổng tiền hàng:</span>
                            <span><?= format_currency($order_info['total_amount']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Tổng thanh toán:</span>
                            <span><?= format_currency($order_info['total_amount']) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="info-card">
                    <h4><i class="fas fa-shipping-fast"></i> Thông tin giao hàng</h4>
                    
                    <div class="info-row">
                        <div class="info-label">Người nhận:</div>
                        <div class="info-value"><?= htmlspecialchars($order_info['shipping_name']) ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Số điện thoại:</div>
                        <div class="info-value"><?= htmlspecialchars($order_info['shipping_phone']) ?></div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Địa chỉ:</div>
                        <div class="info-value"><?= htmlspecialchars($order_info['shipping_address']) ?></div>
                    </div>
                    
                    <?php if (!empty($order_info['customer_note'])): ?>
                    <div class="info-row">
                        <div class="info-label">Ghi chú:</div>
                        <div class="info-value"><?= htmlspecialchars($order_info['customer_note']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Payment Info -->
                <div class="info-card">
                    <h4><i class="fas fa-credit-card"></i> Thanh toán</h4>
                    
                    <div class="info-row">
                        <div class="info-label">Phương thức:</div>
                        <div class="info-value">
                            <?= PAYMENT_METHODS[$order_info['payment_method']] ?? $order_info['payment_method'] ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Trạng thái:</div>
                        <div class="info-value">
                            <?php
                            $payment_badge = $order_info['payment_status'] === 'paid' ? 'success' : 'warning';
                            $payment_text = PAYMENT_STATUS[$order_info['payment_status']] ?? $order_info['payment_status'];
                            ?>
                            <span class="badge bg-<?= $payment_badge ?>">
                                <?= $payment_text ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Tổng tiền:</div>
                        <div class="info-value text-danger">
                            <strong><?= format_currency($order_info['total_amount']) ?></strong>
                        </div>
                    </div>
                </div>
                
                <!-- Order Timeline -->
                <div class="info-card">
                    <h4><i class="fas fa-history"></i> Trạng thái đơn hàng</h4>
                    
                    <div class="timeline">
                        <?php
                        $statuses = [
                            'pending' => ['icon' => 'fa-clock', 'label' => 'Chờ xác nhận'],
                            'processing' => ['icon' => 'fa-cog', 'label' => 'Đang làm bánh'],
                            'shipping' => ['icon' => 'fa-truck', 'label' => 'Đang giao hàng'],
                            'completed' => ['icon' => 'fa-check-circle', 'label' => 'Hoàn thành']
                        ];
                        
                        $current_status = $order_info['order_status'];
                        $status_keys = array_keys($statuses);
                        $current_index = array_search($current_status, $status_keys);
                        
                        if ($current_status === 'cancelled') {
                            echo '<div class="timeline-item">';
                            echo '<div class="timeline-icon active"><i class="fas fa-times-circle"></i></div>';
                            echo '<div class="timeline-content">';
                            echo '<h5>Đã hủy</h5>';
                            echo '<p>Đơn hàng đã được hủy</p>';
                            echo '</div></div>';
                        } else {
                            foreach ($statuses as $key => $status):
                                $index = array_search($key, $status_keys);
                                $is_active = $index <= $current_index;
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-icon <?= $is_active ? 'active' : 'inactive' ?>">
                                <i class="fas <?= $status['icon'] ?>"></i>
                            </div>
                            <div class="timeline-content">
                                <h5><?= $status['label'] ?></h5>
                                <?php if ($is_active): ?>
                                    <p><?= $key === $current_status ? 'Hiện tại' : 'Đã hoàn thành' ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="action-buttons">
                    <?php if ($order_info['order_status'] === 'completed'): ?>
                        <button onclick="reorder()" class="btn btn-reorder flex-fill">
                            <i class="fas fa-redo"></i> Mua lại
                        </button>
                    <?php endif; ?>
                    
                    <?php if (in_array($order_info['order_status'], ['pending', 'processing'])): ?>
                        <a href="<?= url('user/pages/order-cancel.php?id=' . $order_info['order_id']) ?>" 
                           class="btn btn-danger flex-fill" style="border-radius: 30px; font-weight: 600;">
                            <i class="fas fa-times"></i> Hủy đơn
                        </a>
                    <?php endif; ?>
                    
                    <button onclick="window.print()" class="btn btn-secondary flex-fill" style="border-radius: 30px; font-weight: 600;">
                        <i class="fas fa-print"></i> In đơn
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
 
<script>
function reorder() {
    if (confirm('Bạn muốn đặt lại đơn hàng này?')) {
        // Add all products to cart
        <?php foreach ($order_details as $item): ?>
        fetch('<?= url('user/pages/cart-add.php') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=<?= $item['product_id'] ?>&quantity=<?= $item['quantity'] ?>'
        });
        <?php endforeach; ?>
        
        // Redirect to cart after a short delay
        setTimeout(() => {
            window.location.href = '<?= url('user/pages/cart.php') ?>';
        }, 500);
    }
}
</script>
 
<style>
@media print {
    .btn, .action-buttons, .order-header a {
        display: none !important;
    }
    
    .info-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
 
<?php include '../../includes/layouts/footer.php'; ?>