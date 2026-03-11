<?php
/**
 * Edit Product page
 * File: admin/product-edit.php
 */

require_once '../includes/init.php';

$page_title = 'Sửa sản phẩm';

$product = new Product();
$category = new Category();

// Lấy product_id từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    set_flash('error', 'Sản phẩm không tồn tạii');
    redirect(url('admin/products.php'));
}

// Lấy thông tin sản phẩm
$product_info = $product->getProductById($product_id);

if (!$product_info) {
    set_flash('error', 'Sản phẩm không tồn tại');
    redirect(url('admin/products.php'));
}

$errors = [];

// Xử lý form submit
if (is_method('POST')) {
    $data = [
        'product_name' => sanitize($_POST['product_name'] ?? ''),
        'category_id' => (int)($_POST['category_id'] ?? 0),
        'description' => sanitize($_POST['description'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    // Valiadte
    if (empty($data['product_name'])) {
        $errors['product_name'] = 'Tên sản phẩm không được để trống';
    }

    if ($data['category_id'] <= 0) {
        $errors['category_id'] = 'Vui lòng chọn danh mục';
    }

    if ($data['price'] < 0) {
        $errors['price'] = 'Giá sản phẩm phải lớn hơn hoặc bằng 0';
    }

    //Xử lý upload ảnh mới nếu có
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_image($_FILES['image'], 'products', 'product');
        if ($uploaded) {
            // Xóa ảnh cũ
            if ($product_info['image_url'] && $product_info['image_url'] != 'product/default.jpg') {
                delete_file($product_info['image_url']);
            }
            $data['image_url'] = $uploaded;
        }
    } else {
        // Giữ nguyên ảnh cũ
        $data['image_url'] = $product_info['image_url'];
    }

    // Nếu không có lỗi thì cập nhật
    if (empty($errors)) {
        if ($product->updateProduct($product_id, $data)) {
            set_flash('success', 'Đã cập nhật sản phẩm thành công');
            redirect(url('admin/products.php'));
        } else {
            $errors['general'] = 'Có lỗi xảy ra khi cập nhật sản phẩm';
        }
    }
}

// Lấy danh sách danh mục
$categories = $category->getAllCategories();

// Include header
include 'includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-edit"></i> Sửa sản phẩm</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/products.php') ?>">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Sửa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger">
        <?= $errors['general'] ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="row">
        <!-- Form chính -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Thông tin sản phẩm
                </div>
                <div class="card-body">
                    <!-- Tên sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= isset($errors['product_name']) ? 'is-invalid' : '' ?>" 
                               name="product_name" 
                               value="<?= htmlspecialchars($_POST['product_name'] ?? $product_info['product_name']) ?>"
                               required>
                        <?php if (isset($errors['product_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['product_name'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Danh mục -->
                    <div class="mb-3">
                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" 
                                name="category_id" 
                                required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" 
                                        <?= (isset($_POST['category_id']) ? $_POST['category_id'] : $product_info['category_id']) == $cat['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="invalid-feedback"><?= $errors['category_id'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mô tả -->
                    <div class="mb-3">
                        <label class="form-label">Mô tả sản phẩm</label>
                        <textarea class="form-control" 
                                  name="description" 
                                  rows="5"><?= htmlspecialchars($_POST['description'] ?? $product_info['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <!-- Giá -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                   name="price" 
                                   value="<?= $_POST['price'] ?? $product_info['price'] ?>"
                                   min="0"
                                   step="1000"
                                   required>
                            <?php if (isset($errors['price'])): ?>
                                <div class="invalid-feedback"><?= $errors['price'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tồn kho -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số lượng tồn kho</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="stock_quantity" 
                                   value="<?= $_POST['stock_quantity'] ?? $product_info['stock_quantity'] ?>"
                                   min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Hình ảnh -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-image"></i> Hình ảnh sản phẩm
                </div>
                <div class="card-body">
                    <!-- Ảnh hiện tại -->
                    <div class="mb-3 text-center">
                        <img src="<?= upload($product_info['image_url']) ?>" 
                             id="currentImage"
                             style="max-width: 100%; border-radius: 5px;"
                             onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thay đổi hình ảnh</label>
                        <input type="file" 
                               class="form-control" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(event)">
                        <small class="text-muted">Để trống nếu không muốn thay đổi</small>
                    </div>
                </div>
            </div>
            
            <!-- Trạng thái -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-toggle-on"></i> Trạng thái
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_active" 
                               id="is_active"
                               <?= (isset($_POST['is_active']) ? $_POST['is_active'] : $product_info['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">
                            Hiển thị sản phẩm
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save"></i> Cập nhật sản phẩm
                    </button>
                    <a href="<?= url('admin/products.php') ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(event) {
    const preview = document.getElementById('currentImage');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php include 'includes/footer.php'; ?>