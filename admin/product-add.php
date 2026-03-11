<?php
/**
 * Add product page
 * File: admin/product-add.php
 */

require_once '../includes/init.php';

$page_title = 'Thêm sản phẩm mới';

$product = new Product();
$category = new Category();

$errors = [];
$success = false;

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

    // Validate
    if (empty($data['product_name'])) {
        $errors['product_name'] = 'Tên sản phẩm không được để trống.';
    }

    if ($data['category_id'] <= 0) {
        $errors['category_id'] = 'Vui lòng chọn danh mục.';
    }

    if ($data['price'] <= 0) {
        $errors['price'] = 'Giá sản phẩm phải lớn hơn 0.';
    }

    // Xử lý upload ảnh
    $image_path = 'products/default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_image($_FILES['image'], 'products', 'product');
        if ($uploaded) {
            $image_path = $uploaded;
        } else {
            $errors['image'] = 'Lỗi khi tải ảnh lên.';
        }
    }

    $data['image_url'] = $image_path;

    // Nếu không có lỗi thì thêm sản phẩm
    if (empty($errors)) {
        $product_id = $product->addProduct($data);

        if ($product_id) {
            set_flash('success', 'Thêm sản phẩm thành công.');
            redirect(url('admin/products.php'));
        } else {
            $errors['general'] = 'Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.';
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
            <h1><i class="fas fa-plus-circle"></i> Thêm sản phẩm mới</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/products.php') ?>">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Thêm mới</li>
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
                               value="<?= $_POST['product_name'] ?? '' ?>"
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
                                        <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
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
                                  rows="5"><?= $_POST['description'] ?? '' ?></textarea>
                    </div>
                    
                    <div class="row">
                        <!-- Giá -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                   name="price" 
                                   value="<?= $_POST['price'] ?? '' ?>"
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
                                   value="<?= $_POST['stock_quantity'] ?? 0 ?>"
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
                    <div class="mb-3">
                        <input type="file" 
                               class="form-control" 
                               name="image" 
                               accept="image/*"
                               onchange="previewImage(event)">
                        <small class="text-muted">Chấp nhận: JPG, PNG, GIF. Tối đa 5MB</small>
                        <?php if (isset($errors['image'])): ?>
                            <div class="text-danger mt-2"><?= $errors['image'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Preview -->
                    <div id="imagePreview" class="text-center" style="display: none;">
                        <img id="preview" src="" style="max-width: 100%; border-radius: 5px;">
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
                               checked>
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
                        <i class="fas fa-save"></i> Lưu sản phẩm
                    </button>
                    <a href="<?= url('admin/products.php') ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-times"></i> Hủy bỏ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php include 'includes/footer.php'; ?>


