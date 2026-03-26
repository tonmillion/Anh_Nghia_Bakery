<?php
/**
 * Products mângement
 * File: admin/pages/products.php
 */

require_once '../../includes/init.php';

$page_title = 'Quản lý sản phẩm';

$product = new Product();
$category = new Category();

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    if ($product->deleteProduct($product_id)) {
        set_flash('success', 'Đã xóa sản phẩm thành công');
    } else {
        set_flash('error', 'Không thể xóa sản phẩm');
    }
    redirect(url('admin/pages/products.php'));
}

// Lấy danh sách sản phẩm
$products = $product->getProducts(1000, 0, []); // Lấy nhiều để hiển thị bảng
$category = $category->getAllCategories();

// Include header
include '../includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-box"></i> Quản lý sản phẩm</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sản phẩm</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('admin/pages/product-add.php') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Danh sách sản phẩm (<?= count($products) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Đã bán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= $p['product_id'] ?></td>
                        <td>
                            <img src="<?= upload($p['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($p['product_name']) ?>"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                 onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($p['product_name']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                        <td><?= format_currency($p['price']) ?></td>
                        <td>
                            <?php if ($p['stock_quantity'] < 10): ?>
                                <span class="badge rounded-pill bg-warning"><?= $p['stock_quantity'] ?></span>
                            <?php else: ?>
                                <?= $p['stock_quantity'] ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $p['sold_count'] ?></td>
                        <td>
                            <?php if ($p['is_active']): ?>
                                <span class="badge rounded-pill bg-success">Đang bán</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/pages/product-edit.php?id=' . $p['product_id']) ?>" 
                                   class="btn btn-info" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" 
                                   class="btn btn-secondary" 
                                   target="_blank" 
                                   title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?delete=<?= $p['product_id'] ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirmDelete('Bạn có chắc muốn xóa sản phẩm này?')"
                                   title="Xóa">
                                    <i class="fas fa-trash"></i>
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

<?php include '../includes/footer.php'; ?>