<?php
/**
 * Products list page
 * File: user/pages/products.php
 */

require_once '../../includes/init.php';

$page_title = 'Sản phẩm - ' . SITE_NAME;

// Lấy parameters từ URL
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Chuẩn bị filters
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;

$filters = [];
if ($category_id) {
    $filters['category_id'] = $category_id;
}
if ($search) {
    $filters['search'] = $search;
}
if ($min_price !== null && $min_price !== '') {
    $filters['min_price'] = $min_price;
}
if ($max_price !== null && $max_price !== '') {
    $filters['max_price'] = $max_price;
}

// Xác định sort order
$order_by = match($sort) {
    'price_asc' => 'price ASC',
    'price_desc' => 'price DESC',
    'name' => 'product_name ASC',
    'popular' => 'sold_count DESC',
    default => 'created_at DESC'
};

// Lấy sản phẩm
$product = new Product();
$items_per_page = 9; // Display 9 products per page for a 3x3 grid
$offset = ($current_page - 1) * $items_per_page;
$products = $product->getProducts($items_per_page, $offset, $filters, $order_by);

// Đếm tổng số để tạo pagination
$total = $product->countProducts($filters);
$total_pages = ceil($total / $items_per_page);

// Lấy thông tin category nếu có
$category = new Category();
$current_category = null;
if ($category_id) {
    $current_category = $category->getCategoryById($category_id);
}

// Lấy tất cả categories cho sidebar
$categories = $category->getCategoriesWithCount($category_id);

// Include header
include '../../includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('user/css/products.css') ?>?v=<?= time() ?>">

<div class="products-page">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <h5><i class="fas fa-filter"></i> Danh Mục</h5>
                    <ul class="category-list">
                        <li>
                            <a href="<?= url('user/pages/products.php') ?>" 
                               class="<?= !$category_id ? 'active' : '' ?>">
                                <span><i class="fas fa-th"></i> Tất cả sản phẩm</span>
                                <span class="badge bg-secondary"><?= $total ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="<?= url('user/pages/products.php?category=' . $cat['category_id']) ?>" 
                               class="<?= $category_id == $cat['category_id'] ? 'active' : '' ?>">
                                <span><?= htmlspecialchars($cat['category_name']) ?></span>
                                <span class="badge bg-secondary"><?= $cat['product_count'] ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Price Filter (Optional) -->
                <div class="sidebar">
                    <h5><i class="fas fa-dollar-sign"></i> Khoảng Giá</h5>
                    <form method="GET" action="">
                        <?php if ($category_id): ?>
                            <input type="hidden" name="category" value="<?= $category_id ?>">
                        <?php endif; ?>
                        
                        <div class="mb-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" 
                                   placeholder="Giá thấp nhất" value="<?= $_GET['min_price'] ?? '' ?>">
                        </div>
                        <div class="mb-2">
                            <input type="number" name="max_price" class="form-control form-control-sm" 
                                   placeholder="Giá cao nhất" value="<?= $_GET['max_price'] ?? '' ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Header -->
                <div class="products-header">
                    <?php if ($current_category): ?>
                        <h3><i class="fas fa-cake-candles"></i> <?= htmlspecialchars($current_category['category_name']) ?></h3>
                        <p class="text-muted mb-0"><?= htmlspecialchars($current_category['description'] ?? '') ?></p>
                    <?php elseif ($search): ?>
                        <h3><i class="fas fa-search"></i> Kết quả tìm kiếm: "<?= htmlspecialchars($search) ?>"</h3>
                    <?php else: ?>
                        <h3><i class="fas fa-shopping-bag"></i> Tất Cả Sản Phẩm</h3>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="products-toolbar">
                        <div class="result-count">
                            Hiển thị <strong><?= count($products) ?></strong> trên <strong><?= $total ?></strong> sản phẩm
                        </div>
                        
                        <div class="sort-dropdown custom-sort-dropdown dropdown">
                            <button class="btn w-100 d-flex justify-content-between align-items-center form-select-custom" type="button" id="sortDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                    $sortLabels = [
                                        'newest' => 'Mới nhất',
                                        'popular' => 'Bán chạy',
                                        'price_asc' => 'Giá: Thấp → Cao',
                                        'price_desc' => 'Giá: Cao → Thấp',
                                        'name' => 'Tên: A → Z'
                                    ];
                                    echo isset($sortLabels[$sort]) ? $sortLabels[$sort] : 'Mới nhất';
                                ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end w-100 shadow-sm" aria-labelledby="sortDropdownMenu">
                                <li><a class="dropdown-item <?= $sort === 'newest' ? 'active' : '' ?>" href="#" onclick="event.preventDefault(); sortProducts('newest')">Mới nhất</a></li>
                                <li><a class="dropdown-item <?= $sort === 'popular' ? 'active' : '' ?>" href="#" onclick="event.preventDefault(); sortProducts('popular')">Bán chạy</a></li>
                                <li><a class="dropdown-item <?= $sort === 'price_asc' ? 'active' : '' ?>" href="#" onclick="event.preventDefault(); sortProducts('price_asc')">Giá: Thấp → Cao</a></li>
                                <li><a class="dropdown-item <?= $sort === 'price_desc' ? 'active' : '' ?>" href="#" onclick="event.preventDefault(); sortProducts('price_desc')">Giá: Cao → Thấp</a></li>
                                <li><a class="dropdown-item <?= $sort === 'name' ? 'active' : '' ?>" href="#" onclick="event.preventDefault(); sortProducts('name')">Tên: A → Z</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h4>Không tìm thấy sản phẩm nào</h4>
                        <p class="text-muted">Vui lòng thử lại với bộ lọc khác</p>
                        <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary">
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>">
                                <img src="<?= upload($p['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($p['product_name']) ?>" 
                                     class="product-image"
                                     onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            </a>
                            
                            <div class="product-info">
                                <div class="product-name">
                                    <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= mb_strtoupper(htmlspecialchars($p['product_name']), 'UTF-8') ?>
                                    </a>
                                </div>
                                
                                <div class="product-price">
                                    <?= format_currency($p['price']) ?>
                                </div>
                                
                                <div class="product-meta">
                                    <span><i class="fas fa-eye"></i> <?= $p['view_count'] ?></span>
                                    <span><i class="fas fa-shopping-cart"></i> <?= $p['sold_count'] ?></span>
                                </div>
                                
                                <?php if ($p['stock_quantity'] > 0): ?>
                                    <form method="POST" action="<?= url('user/pages/cart-add.php') ?>">
                                        <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-add-cart" disabled>
                                        <i class="fas fa-times"></i> Hết hàng
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <!-- Previous -->
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $category_id ? '&category=' . $category_id : '' ?><?= $sort !== 'newest' ? '&sort=' . $sort : '' ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Pages -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == 1 || $i == $total_pages || abs($i - $current_page) <= 2): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $category_id ? '&category=' . $category_id : '' ?><?= $sort !== 'newest' ? '&sort=' . $sort : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php elseif (abs($i - $current_page) == 3): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $category_id ? '&category=' . $category_id : '' ?><?= $sort !== 'newest' ? '&sort=' . $sort : '' ?>">
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
    </div>
</div>

<script>
function sortProducts(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    url.searchParams.delete('page'); // Reset về trang 1
    window.location.href = url.toString();
}
</script>

<?php include '../../includes/layouts/footer.php'; ?>
