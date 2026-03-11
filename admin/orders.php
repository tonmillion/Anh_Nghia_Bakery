<?php
/**
 * Orders management
 * File: admin/orders.php
 */

require_once '../includes/init.php';

$page_title = 'Quản lý đơn hàng';

$order = new Order();

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);

    if ($order->updateOrderStatus($order_id, $status)) {
        set_flash('success', 'Cập nhật trạng thái đơn hàng thành công');
    } else {
        set_flash('error', 'Không thể cập nhật trạng thái đơn hàng');
    }
    redirect(url('admin/orders.php'));
}

// Filter
$filter_status = isset($_GET['status']) ? $_GET['status'] : null;
$filters = [];
if ($filter_status) {
    $filters['status'] = $filter_status;
}

// Lấy danh sách đơn hàng
$orders = $order->getAllOrders($filters, 100, 0);

// Đếm theo trạng thái
$db = getDB();
$stmt = $db->query("SELECT order_status, COUNT(*) AS count FROM orders GROUP BY order_status");
$status_counts = [];
while ($row = $stmt->fetch()) {
    $status_counts[$row['order_status']] = $row['count'];
}

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Đơn hàng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Status Filter -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="<?= url('admin/orders.php') ?>" 
               class="btn btn-outline-primary <?= !$filter_status ? 'active' : '' ?>">
                Tất cả (<?= count($orders) ?>)
            </a>
            <?php foreach (ORDER_STATUS as $key => $label): ?>
                <a href="<?= url('admin/orders.php?status=' . $key) ?>" 
                   class="btn btn-outline-primary <?= $filter_status === $key ? 'active' : '' ?>">
                    <?= $label ?> (<?= $status_counts[$key] ?? 0 ?>)
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Danh sách đơn hàng
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>
                            <a href="<?= url('admin/order-detail.php?id=' . $o['order_id']) ?>">
                                <strong><?= htmlspecialchars($o['order_code']) ?></strong>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($o['full_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($o['shipping_phone']) ?></td>
                        <td><strong><?= format_currency($o['total_amount']) ?></strong></td>
                        <td>
                            <span class="badge bg-<?= $o['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                <?= PAYMENT_STATUS[$o['payment_status']] ?? $o['payment_status'] ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                                <select name="status" 
                                        class="form-select form-select-sm" 
                                        onchange="this.form.submit()"
                                        style="width: 150px;">
                                    <?php foreach (ORDER_STATUS as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $o['order_status'] === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                        <td><?= format_date($o['order_date']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/order-detail.php?id=' . $o['order_id']) ?>" 
                                   class="btn btn-info" 
                                   title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>