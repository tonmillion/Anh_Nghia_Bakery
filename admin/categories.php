<?php
/**
 * Quản lý danh mục
 * File: admin/categories.php
 */

require_once '../includes/init.php';

$page_title = 'Quản lý danh mục';

$category = new Category();

// Xử lý thêm danh mục
if (isset($_POST['add_category'])) {
    $data = [
        'category_name' => sanitize($_POST['category_name']),
        'description' => sanitize($_POST['description']),
        'display_order' => (int)($_POST['display_order'] ?? 0)
    ];

    if ($category->addCategory($data)) {
        set_flash('success', 'Đã thêm danh mục thành công');
    } else {
        set_flash ('error', 'Không thể thêm danh mục');
    }
    redirect(url('admin/categories.php'));
}

// Xử lý cập nhật danh mục
if (isset($_POST['update_category'])) {
    $category_id = (int)$_POST['category_id'];
    $data = [
        'category_name' => sanitize($_POST['category_name']),
        'description' => sanitize($_POST['description']),
        'display_order' => (int)($_POST['display_order'] ?? 0)
    ];

    if ($category->updateCategory($category_id, $data)) {
        set_flash('success', 'Đã cập nhật danh mục thành công');
    } else {
        set_flash('error', 'Không thể cập nhật danh mục');
    }
    redirect(url('admin/categories.php'));
}

// Xử lý xóa danh mục
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];

    if ($category->deleteCategory($category_id)) {
        set_flash('success', 'Đã xóa danh mục thành công');
    } else {
        set_flash('error', 'Không thể xóa danh mục');
    }
    redirect(url('admin/categories.php'));
}

// Lấy danh sách danh mục
$categories = $category->getCategoriesWithCount();

// Lấy thông tin category nếu đang edit
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_category = $category->getCategoryById($edit_id);
}

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-tags"></i> Quản lý danh mục</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Danh mục</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form thêm/sửa -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-<?= $edit_category ? 'edit' : 'plus' ?>"></i> 
                <?= $edit_category ? 'Sửa danh mục' : 'Thêm danh mục mới' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?= $edit_category['category_id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="category_name" 
                               value="<?= htmlspecialchars($edit_category['category_name'] ?? '') ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" 
                                  name="description" 
                                  rows="3"><?= htmlspecialchars($edit_category['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thứ tự hiển thị</label>
                        <input type="number" 
                               class="form-control" 
                               name="display_order" 
                               value="<?= $edit_category['display_order'] ?? 0 ?>"
                               min="0">
                        <small class="text-muted">Số càng nhỏ càng hiển thị trước</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" 
                                name="<?= $edit_category ? 'update_category' : 'add_category' ?>" 
                                class="btn btn-primary flex-fill">
                            <i class="fas fa-save"></i> <?= $edit_category ? 'Cập nhật' : 'Thêm mới' ?>
                        </button>
                        
                        <?php if ($edit_category): ?>
                            <a href="<?= url('admin/categories.php') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Danh sách danh mục -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Danh sách danh mục (<?= count($categories) ?>)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th width="120">Số sản phẩm</th>
                                <th width="100">Thứ tự</th>
                                <th width="100">Trạng thái</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Chưa có danh mục nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $cat['category_id'] ?></td>
                                    <td><strong><?= htmlspecialchars($cat['category_name']) ?></strong></td>
                                    <td><?= htmlspecialchars(excerpt($cat['description'], 50)) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= $cat['product_count'] ?? 0 ?></span>
                                    </td>
                                    <td class="text-center"><?= $cat['display_order'] ?></td>
                                    <td>
                                        <?php if ($cat['is_active']): ?>
                                            <span class="badge bg-success">Hiển thị</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?edit=<?= $cat['category_id'] ?>" 
                                               class="btn btn-info" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= url('user/pages/products.php?category=' . $cat['category_id']) ?>" 
                                               class="btn btn-secondary" 
                                               target="_blank"
                                               title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($cat['product_count'] == 0): ?>
                                                <a href="?delete=<?= $cat['category_id'] ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirmDelete('Bạn có chắc muốn xóa danh mục này?')"
                                                   title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-danger" 
                                                        disabled 
                                                        title="Không thể xóa danh mục có sản phẩm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>