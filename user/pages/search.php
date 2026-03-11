<?php
/**
 * Search page
 * File: user/pages/search.php
 */

require_once '../../includes/init.php';

$keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$page_title = 'Tìm kiếm: ' . ($keyword ?: 'Tất cả sản phẩm') . ' - ' . SITE_NAME;

$current_page = isset ($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $items_per_page;

// Tìm kiếm sản phẩm
$product = new Product();

if (empty($keyword)) {
    // Nếu không có từ khóa, hiển thị tất cả sản phẩm
    $products = $product->getProducts($items_per_page, $offset);
    $total = $product->countProducts();
} else {
    // Tìm kiếm theo từ khóa
    $products = $product->searchProducts($keyword, $items_per_page + $offset);
    // Slice để phân trang
    $all_result = $products;
    $total = count($products);
    $products = array_slice($products, $offset, $items_per_page);
}

$total_pages = ceil($total / $items_per_page);

// Include header
include '../../includes/layouts/header.php';
?>

<style>
    .search-page {
        padding: 30px 0;
        min-height: 60vh;
    }
    
    .search-header {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .search-box-large {
        max-width: 600px;
        margin: 20px auto;
        position: relative;
    }
    
    .search-box-large input {
        padding: 15px 60px 15px 20px;
        border: 2px solid #e1e8ed;
        border-radius: 30px;
        font-size: 16px;
        width: 100%;
    }
    
    .search-box-large input:focus {
        border-color: #667eea;
        outline: none;
    }
    
    .search-box-large button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        font-size: 18px;
        transition: all 0.3s;
    }
    
    .search-box-large button:hover {
        background: #5568d3;
        transform: translateY(-50%) scale(1.05);
    }
    
    .search-results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .keyword-highlight {
        color: #667eea;
        font-weight: bold;
    }
</style>

<div class="search-page">
    <div class="container">
        <!-- Search Header -->
        <div class="search-header">
            <h2 class="text-center mb-3">
                <i class="fas fa-search"></i> Tìm Kiếm Sản Phẩm
            </h2>
            
            <div class="search-box-large">
                <form action="" method="GET">
                    <input type="text" 
                           name="q" 
                           placeholder="Nhập tên sản phẩm cần tìm..." 
                           value="<?= htmlspecialchars($keyword) ?>"
                           autofocus>
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <?php if ($keyword): ?>
                <p class="text-center text-muted mt-3 mb-0">
                    Kết quả tìm kiếm cho: <span class="keyword-highlight">"<?= htmlspecialchars($keyword) ?>"</span>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Results Header -->
        <div class="search-results-header">
            <div>
                <strong><?= $total ?></strong> sản phẩm
                <?php if ($keyword): ?>
                    phù hợp với từ khóa <span class="keyword-highlight">"<?= htmlspecialchars($keyword) ?>"</span>
                <?php endif; ?>
            </div>
            
            <?php if ($keyword): ?>
                <a href="<?= url('user/pages/search.php') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="text-center py-5" style="background: white; border-radius: 10px;">
                <i class="fas fa-search" style="font-size: 80px; color: #ccc; margin-bottom: 20px;"></i>
                <h4>Không tìm thấy sản phẩm nào</h4>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác</p>
                <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary mt-3">
                    Xem tất cả sản phẩm
                </a>
            </div>
        <?php else: ?>
            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
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
                                <?php
                                // Highlight từ khóa trong tên sản phẩm
                                if ($keyword) {
                                    echo highlight_keyword($p['product_name'], $keyword);
                                } else {
                                    echo htmlspecialchars($p['product_name']);
                                }
                                ?>
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
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $current_page - 1 ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == 1 || $i == $total_pages || abs($i - $current_page) <= 2): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php elseif (abs($i - $current_page) == 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&page=<?= $current_page + 1 ?>">
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

<?php include '../../includes/layouts/footer.php'; ?>