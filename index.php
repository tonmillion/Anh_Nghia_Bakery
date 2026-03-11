<?php
/**
 * Homapage
 * File: index.php
 */

require_once 'includes/init.php';

$page_title = 'Trang chủ - ' . SITE_NAME;

// Lấy sản phẩm mới nhất
$product = new Product();
$latest_products = $product->getLatestProducts(8);

// Lấy sản phẩm bán chạy
$best_selling = $product->getBestSellingProducts(8);

// Lấy danh mục
$category = new Category();
$categories = $category->getCategoriesWithCount();

// Include header
include 'includes/layouts/header.php';
?>

<style>
    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
        margin-bottom: 50px;
    }
    
    .hero-content h1 {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    
    .hero-content p {
        font-size: 20px;
        margin-bottom: 30px;
    }
    
    .hero-image {
        text-align: center;
        font-size: 200px;
    }
    
    /* Section Titles */
    .section-title {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .section-title h2 {
        font-size: 36px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    
    .section-title p {
        color: #666;
        font-size: 16px;
    }
    
    /* Category Cards */
    .category-card {
        background: white;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
        height: 100%;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .category-card i {
        font-size: 50px;
        color: #667eea;
        margin-bottom: 15px;
    }
    
    .category-card h5 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .category-card p {
        color: #666;
        font-size: 14px;
        margin-bottom: 0;
    }
    
    /* Product Cards */
    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
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
    
    .product-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .product-price {
        font-size: 20px;
        color: #ff6b6b;
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .product-meta {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .btn-add-cart {
        width: 100%;
        background: #667eea;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .btn-add-cart:hover {
        background: #5568d3;
        color: white;
    }
    
    /* Features Section */
    .features {
        background: #f8f9fa;
        padding: 50px 0;
        margin: 50px 0;
    }
    
    .feature-item {
        text-align: center;
        padding: 20px;
    }
    
    .feature-item i {
        font-size: 50px;
        color: #667eea;
        margin-bottom: 15px;
    }
    
    .feature-item h5 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .feature-item p {
        color: #666;
        font-size: 14px;
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1>🍰 Bánh Ngọt Tươi Ngon</h1>
                    <p>Chất lượng cao cấp - Giao hàng tận nơi - Giá cả hợp lý</p>
                    <a href="<?= url('user/pages/products.php') ?>" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                    </a>
                    <a href="<?= url('user/pages/about.php') ?>" class="btn btn-outline-light btn-lg ms-2">
                        <i class="fas fa-info-circle"></i> Tìm hiểu thêm
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    🎂
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section mb-5">
    <div class="container">
        <div class="section-title">
            <h2>Danh Mục Sản Phẩm</h2>
            <p>Khám phá các loại bánh ngọt đa dạng</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $category_icons = [
                'Bánh Kem' => 'fa-cake-candles',
                'Bánh Mì' => 'fa-bread-slice',
                'Cookies' => 'fa-cookie',
                'Bánh Bông Lan' => 'fa-layer-group',
                'Bánh Ngọt Pháp' => 'fa-croissant',
                'Bánh Truyền Thống' => 'fa-gifts'
            ];

            foreach ($categories as $cat): 
                $icon = $category_icons[$cat['category_name']] ?? 'fa-cake-candles';
            ?>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <a href="<?= url('user/pages/products.php?category=' . $cat['category_id']) ?>" class="text-decoration-none">
                    <div class="category-card">
                        <i class="fas <?= $icon ?>"></i>
                        <h5><?= htmlspecialchars($cat['category_name']) ?></h5>
                        <p><?= $cat['product_count'] ?? 0 ?> sản phẩm</p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="latest-products mb-5">
    <div class="container">
        <div class="section-title">
            <h2>Sản Phẩm Mới Nhất</h2>
            <p>Những sản phẩm vừa được ra mắt</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($latest_products as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    <img src="<?= upload($p['image_url']) ?>" 
                         alt="<?= htmlspecialchars($p['product_name']) ?>" 
                         class="product-image"
                         onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                    
                    <div class="product-info">
                        <h6 class="product-name">
                            <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </a>
                        </h6>
                        
                        <div class="product-price">
                            <?= format_currency($p['price']) ?>
                        </div>
                        
                        <div class="product-meta">
                            <span><i class="fas fa-eye"></i> <?= $p['view_count'] ?></span>
                            <span><i class="fas fa-shopping-cart"></i> Đã bán <?= $p['sold_count'] ?></span>
                        </div>
                        
                        <form method="POST" action="<?= url('user/pages/cart-add.php') ?>">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary btn-lg">
                Xem tất cả sản phẩm <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <i class="fas fa-truck"></i>
                    <h5>Giao hàng nhanh</h5>
                    <p>Giao hàng trong 1 giờ với đơn dưới 10km</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h5>An toàn vệ sinh</h5>
                    <p>Đảm bảo tiêu chuẩn ATTP cao nhất</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <i class="fas fa-undo"></i>
                    <h5>Đổi trả dễ dàng</h5>
                    <p>Đổi trả trong 6h nếu không hài lòng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h5>Hỗ trợ 24/7</h5>
                    <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Best Selling Products Section -->
<section class="best-selling mb-5">
    <div class="container">
        <div class="section-title">
            <h2>Sản Phẩm Bán Chạy</h2>
            <p>Được khách hàng yêu thích nhất</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($best_selling as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    <div class="position-relative">
                        <img src="<?= upload($p['image_url']) ?>" 
                             alt="<?= htmlspecialchars($p['product_name']) ?>" 
                             class="product-image"
                             onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                            <i class="fas fa-fire"></i> Hot
                        </span>
                    </div>
                    
                    <div class="product-info">
                        <h6 class="product-name">
                            <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </a>
                        </h6>
                        
                        <div class="product-price">
                            <?= format_currency($p['price']) ?>
                        </div>
                        
                        <div class="product-meta">
                            <span><i class="fas fa-eye"></i> <?= $p['view_count'] ?></span>
                            <span><i class="fas fa-shopping-cart"></i> Đã bán <?= $p['sold_count'] ?></span>
                        </div>
                        
                        <form method="POST" action="<?= url('user/pages/cart-add.php') ?>">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/layouts/footer.php';
?>