<?php
/**
 * Admin dashboard
 * File: admin/index.php
 */

require_once '../includes/init.php';

$page_title = 'Dashboard';

// Lấy thống kê
$db = getDB();

// Tổng số đơn hàng
$stmt = $db->query("SELECT COUNT(*) AS total FROM orders");
$total_orders = $stmt->fetch()['total'];

// Tổng doanh thu
$stmt = $db -> query("SELECT SUM(total_amount) AS revenue FROM orders WHERE order_status = 'completed'");
$total_revenue = $stmt->fetch()['revenue'] ?? 0;

// Tổng sản phẩm
$stmt = $db -> query("SELECT COUNT(*) AS total FROM products WHERE is_active = 1");
$total_products = $stmt->fetch()['total'];

// Tổng khách hàng
$stmt = $db -> query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
$total_customers = $stmt->fetch()['total'];

// Đơn hàng mới (pending)
$stmt = $db -> query("SELECT COUNT(*) AS total FROM orders WHERE order_status = 'pending'");
$pending_orders = $stmt->fetch()['total'];

// Đơn hàng gần đây
$order = new Order();
$recent_orders = $order->getAllOrders([], 10, 0);

// Sản phẩm bán chạy
$stmt = $db -> query("SELECT p.*, COUNT(od.product_id) AS order_count
                    FROM products p
                    LEFT JOIN order_details od ON p.product_id = od.product_id
                    GROUP BY p.product_id
                    ORDER BY order_count DESC
                    LIMIT 5");
$top_products = $stmt->fetchAll();

// Doanh thu theo ngày (7 ngày gần nhất)
$stmt = $db -> query("SELECT DATE(order_date) AS date, SUM(total_amount) AS revenue
                    FROM orders
                    WHERE order_status = 'completed' 
                    AND order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY DATE(order_date)
                    ORDER BY date ASC");
$revenue_by_date = $stmt->fetchAll();

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card blue">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-value"><?= number_format($total_orders) ?></div>
            <div class="stat-label">Tổng đơn hàng</div>
            <?php if ($pending_orders > 0): ?>
                <small class="text-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $pending_orders ?> đơn chờ xử lý
                </small>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card green">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-value"><?= number_format($total_revenue / 1000000, 1) ?>M</div>
            <div class="stat-label">Doanh thu</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card orange">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-value"><?= number_format($total_products) ?></div>
            <div class="stat-label">Sản phẩm</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stat-card red">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><?= number_format($total_customers) ?></div>
            <div class="stat-label">Khách hàng</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Doanh thu 7 ngày gần đây
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Order Status -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Trạng thái đơn hàng
            </div>
            <div class="card-body">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</span>
                <a href="<?= url('admin/orders.php') ?>" class="btn btn-sm btn-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $o): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('admin/order-detail.php?id=' . $o['order_id']) ?>">
                                        <?= htmlspecialchars($o['order_code']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($o['full_name'] ?? 'N/A') ?></td>
                                <td><?= format_currency($o['total_amount']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $o['order_status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= ORDER_STATUS[$o['order_status']] ?? $o['order_status'] ?>
                                    </span>
                                </td>
                                <td><?= format_date($o['order_date'], 'd/m/Y') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-fire"></i> Sản phẩm bán chạy
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($top_products as $p): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= upload($p['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($p['product_name']) ?>"
                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;"
                                 onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                            <div>
                                <div style="font-weight: 600; font-size: 14px;">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </div>
                                <small class="text-muted">
                                    Đã bán: <?= $p['sold_count'] ?>
                                </small>
                            </div>
                        </div>
                        <span class="badge bg-primary rounded-pill">
                            <?= $p['order_count'] ?? 0 ?> đơn
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($revenue_by_date, 'date')) ?>,
        datasets: [{
            label: 'Doanh thu',
            data: <?= json_encode(array_column($revenue_by_date, 'revenue')) ?>,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('vi-VN') + ' đ';
                    }
                }
            }
        }
    }
});

// Order Status Chart
<?php
$stmt = $db->query("SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status");
$status_data = $stmt->fetchAll();
?>

const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(function($s) { return ORDER_STATUS[$s['order_status']] ?? $s['order_status']; }, $status_data)) ?>,
        datasets: [{
            data: <?= json_encode(array_column($status_data, 'count')) ?>,
            backgroundColor: [
                '#ffc107',
                '#2196F3',
                '#00bcd4',
                '#4CAF50',
                '#f44336'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>