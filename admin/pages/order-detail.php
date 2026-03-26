<?php
/**
 * Order detail page
 * File: admin/pages/order-detail.php
 */

require_once '../../includes/init.php';

$page_title = 'Chi tiết đơn hàng';

$order = new Order();

// Lấy order id
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    set_flash('error', 'Đơn hàng không tồn tại');
    redirect(url('admin/pages/orders.php'));
}

// Lấy thông tin đơn hàng
$order_info = $order->getOrderById($order_id);
$order_details = $order->getOrderDetails($order_id);

if (!$order_info) {
    set_flash('error', 'Đơn hàng không tồn tại');
    redirect(url('admin/pages/orders.php'));
}

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status'])) {
    $status = sanitize($_POST['update_status']);

    if ($order->updateOrderStatus($order_id, $status)) {
        set_flash('success', 'Đã cập nhật trạng thái đơn hàng');
        redirect(url('admin/pages/order-detail.php?id=' . $order_id));
    } else {
        set_flash('error', 'Không thể cập nhật trạng thái');
    }
}

// Xử lý cập nhật thanh toán
if (isset($_POST['update_payment'])) {
    $payment_status = sanitize($_POST['payment_status']);

    if ($order->updatePaymentStatus($order_id, $payment_status)) {
        set_flash('success', 'Đã cập nhật trạng thái thanh toán');
        redirect(url('admin/pages/order-detail.php?id=' . $order_id));
    }
}

// Include header
include '../includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-file-invoice"></i> Chi tiết đơn hàng #<?= $order_info['order_code'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/pages/orders.php') ?>">Đơn hàng</a></li>
                    <li class="breadcrumb-item active">#<?= $order_info['order_code'] ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('admin/pages/orders.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> In đơn hàng
            </button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Thông tin đơn hàng -->
    <div class="col-lg-8">
        <!-- Thông tin chung -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Thông tin đơn hàng
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Mã đơn hàng:</th>
                                <td><strong><?= htmlspecialchars($order_info['order_code']) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Ngày đặt:</th>
                                <td><?= format_date($order_info['order_date']) ?></td>
                            </tr>
                            <tr>
                                <th>Khách hàng:</th>
                                <td><?= htmlspecialchars($order_info['username'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= htmlspecialchars($order_info['email'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Người nhận:</th>
                                <td><?= htmlspecialchars($order_info['shipping_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td><?= htmlspecialchars($order_info['shipping_phone']) ?></td>
                            </tr>
                            <tr>
                                <th>Địa chỉ:</th>
                                <td><?= htmlspecialchars($order_info['shipping_address']) ?></td>
                            </tr>
                            <tr>
                                <th>Ghi chú:</th>
                                <td><?= htmlspecialchars($order_info['customer_note'] ?? 'Không có') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chi tiết sản phẩm -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list"></i> Chi tiết sản phẩm
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th width="120">Đơn giá</th>
                                <th width="100">Số lượng</th>
                                <th width="150">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_details as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= upload($item['image_url'] ?? 'products/default.jpg') ?>" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                             onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td><?= format_currency($item['unit_price']) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td><strong><?= format_currency($item['subtotal']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Tổng cộng:</th>
                                <th class="text-danger"><?= format_currency($order_info['total_amount']) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Trạng thái đơn hàng -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-tasks"></i> Trạng thái đơn hàng
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái:</label>
                        <select name="status" class="form-select">
                            <?php foreach (ORDER_STATUS as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $order_info['order_status'] === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cập nhật trạng thái
                    </button>
                </form>
                
                <hr>
                
                <div class="timeline mt-3">
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> 
                        Cập nhật lần cuối: <?= format_date($order_info['order_date']) ?>
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Thanh toán -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-credit-card"></i> Thanh toán
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Phương thức:</th>
                        <td><?= PAYMENT_METHODS[$order_info['payment_method']] ?? $order_info['payment_method'] ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái:</th>
                        <td>
                            <span class="badge rounded-pill bg-<?= $order_info['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                <?= PAYMENT_STATUS[$order_info['payment_status']] ?? $order_info['payment_status'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tổng tiền:</th>
                        <td><strong class="text-danger"><?= format_currency($order_info['total_amount']) ?></strong></td>
                    </tr>
                </table>
                
                <?php if ($order_info['payment_status'] !== 'paid'): ?>
                <form method="POST">
                    <input type="hidden" name="payment_status" value="paid">
                    <button type="submit" name="update_payment" class="btn btn-success w-100">
                        <i class="fas fa-check"></i> Đánh dấu đã thanh toán
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Ghi chú admin -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-sticky-note"></i> Ghi chú nội bộ
            </div>
            <div class="card-body">
                <textarea class="form-control" rows="3" placeholder="Ghi chú của admin..."><?= htmlspecialchars($order_info['admin_note'] ?? '') ?></textarea>
                <button class="btn btn-sm btn-secondary mt-2 w-100">
                    <i class="fas fa-save"></i> Lưu ghi chú
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= url('admin/css/order-detail.css') ?>?v=<?= time() ?>">

<?php include '../includes/footer.php'; ?>

