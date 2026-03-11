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
$items_per_page = ITEMS_PER_PAGE;
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

<style>
    .products-page {
        padding: 30px 0;
    }
    
    .sidebar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .sidebar h5 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .category-list {
        list-style: none;
        padding: 0;
    }
    
    .category-list li {
        margin-bottom: 10px;
    }
    
    .category-list a {
        color: #333;
        text-decoration: none;
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .category-list a:hover,
    .category-list a.active {
        background: #667eea;
        color: white;
    }
    
    .products-header {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .products-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .result-count {
        font-size: 16px;
        color: #666;
    }
    
    .sort-dropdown select {
        padding: 8px 15px;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        outline: none;
    }
    
    .sort-dropdown select:focus {
        border-color: #667eea;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .no-products {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
    }
    
    .no-products i {
        font-size: 80px;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    /* Pagination */
    .pagination {
        justify-content: center;
        margin-top: 30px;
    }
    
    .page-link {
        color: #667eea;
    }
    
    .page-link:hover {
        background: #667eea;
        color: white;
    }
    
    .page-item.active .page-link {
        background: #667eea;
        border-color: #667eea;
    }






    .product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: #f8f9fa;
}

.product-info {
    padding: 15px;
}







</style>

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
                        
                        <div class="sort-dropdown">
                            <select id="sortSelect" onchange="sortProducts(this.value)">
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Bán chạy</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
                                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Tên: A → Z</option>
                            </select>
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
                                <h6 class="product-name">
                                    <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($p['product_name']) ?>
                                    </a>
                                </h6>
                                
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
