<?php
/**
 * Order History page
 * File: user/pages/orders.php
 */

require_once '../../includes/init.php';

require_login();

$page_title = 'Lịch sử đơn hàng - ' . SITE_NAME;

// Pagination
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($current_page - 1) * $items_per_page;

// Lấy đơn hàng
$order = new Order();
$orders = $order->getOrdersByUser(get_user_id(), $items_per_page, $offset);

// Đếm tổng
$total = $order->countOrders(['user_id' => get_user_id()]);
$total_pages = ceil($total / $items_per_page);

// Include header
include '../../includes/layouts/header.php';
?>

<style>
    .orders-page {
        padding: 30px 0;
        min-height: 60vh;
    }
    
    .orders-header {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .order-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }
    
    .order-header {
        background: #f8f9fa;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .order-code {
        font-weight: 600;
        color: #667eea;
    }
    
    .order-date {
        color: #666;
        font-size: 14px;
    }
    
    .order-status {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    
    .order-status.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .order-status.processing {
        background: #cfe2ff;
        color: #084298;
    }
    
    .order-status.shipping {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .order-status.completed {
        background: #d4edda;
        color: #155724;
    }
    
    .order-status.cancelled {
        background: #f8d7da;
        color: #721c24;
    }
    
    .order-body {
        padding: 20px;
    }
    
    .order-items {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .order-item-info {
        flex: 1;
    }
    
    .order-item-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .order-item-quantity {
        font-size: 13px;
        color: #666;
    }
    
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #e1e8ed;
    }
    
    .order-total {
        font-size: 18px;
        font-weight: bold;
        color: #ff6b6b;
    }
    
    .order-actions {
        display: flex;
        gap: 10px;
    }
    
    .empty-orders {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .empty-orders i {
        font-size: 100px;
        color: #ccc;
        margin-bottom: 20px;
    }
</style>

<div class="orders-page">
    <div class="container">
        <!-- Header -->
        <div class="orders-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-shopping-bag"></i> Lịch Sử Đơn Hàng</h2>
                    <p class="text-muted mb-0">Quản lý tất cả đơn hàng của bạn</p>
                </div>
                <div>
                    <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Đặt hàng mới
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Orders List -->
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h3>Bạn chưa có đơn hàng nào</h3>
                <p class="text-muted">Hãy bắt đầu mua sắm ngay hôm nay!</p>
                <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $o): 
                // Lấy chi tiết đơn hàng (chỉ lấy 3 item đầu để hiển thị)
                $details = $order->getOrderDetails($o['order_id']);
                $show_details = array_slice($details, 0, 3);
                $remaining = count($details) - count($show_details);
            ?>
            <div class="order-card">
                <!-- Order Header -->
                <div class="order-header">
                    <div>
                        <div class="order-code">
                            <i class="fas fa-barcode"></i> <?= htmlspecialchars($o['order_code']) ?>
                        </div>
                        <div class="order-date">
                            <i class="far fa-calendar"></i> <?= format_date($o['order_date']) ?>
                        </div>
                    </div>
                    
                    <div>
                        <span class="order-status <?= $o['order_status'] ?>">
                            <?= ORDER_STATUS[$o['order_status']] ?? $o['order_status'] ?>
                        </span>
                    </div>
                </div>
                
                <!-- Order Body -->
                <div class="order-body">
                    <div class="order-items">
                        <?php foreach ($show_details as $item): ?>
                        <div class="order-item">
                            <img src="<?= upload($item['image_url'] ?? 'products/default.jpg') ?>" 
                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                 class="order-item-image"
                                 onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                            
                            <div class="order-item-info">
                                <div class="order-item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                <div class="order-item-quantity">
                                    Số lượng: <?= $item['quantity'] ?> × <?= format_currency($item['unit_price']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if ($remaining > 0): ?>
                            <div class="text-muted" style="font-size: 13px;">
                                <i class="fas fa-plus-circle"></i> Và <?= $remaining ?> sản phẩm khác...
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="order-footer">
                        <div>
                            <div class="text-muted" style="font-size: 14px;">Tổng tiền:</div>
                            <div class="order-total"><?= format_currency($o['total_amount']) ?></div>
                        </div>
                        
                        <div class="order-actions">
                            <a href="<?= url('user/pages/order-detail.php?id=' . $o['order_id']) ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            
                            <?php if ($o['order_status'] === 'pending'): ?>
                                <button class="btn btn-outline-danger btn-sm" 
                                        onclick="cancelOrder(<?= $o['order_id'] ?>)">
                                    <i class="fas fa-times"></i> Hủy đơn
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($o['order_status'] === 'completed'): ?>
                                <a href="<?= url('user/pages/products.php') ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-redo"></i> Mua lại
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || abs($i - $current_page) <= 2): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php elseif (abs($i - $current_page) == 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    if (confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
        // TODO: Implement cancel order
        window.location.href = '<?= url('user/pages/order-cancel.php') ?>?id=' + orderId;
    }
}
</script>

<?php include '../../includes/layouts/footer.php'; ?>