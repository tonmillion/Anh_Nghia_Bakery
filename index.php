<?php
/**
 * Homapage
 * File: index.php
 */

require_once 'includes/init.php';

$page_title = 'Trang chủ - ' . SITE_NAME;

// Lấy sản phẩm mới nhất
$product = new Product();
$latest_products = $product->getLatestProducts(6);

// Lấy sản phẩm bán chạy
$best_selling = $product->getBestSellingProducts(6);

// Lấy danh mục
$category = new Category();
$categories = $category->getCategoriesWithCount();

// Include header
include 'includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('assets/css/index.css') ?>?v=<?= time() ?>">

<!-- Hero Section -->
<section class="hero-wrapper">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 d-none d-lg-block position-relative">
                <img src="<?= asset('images/hero-image.jpg') ?>" alt="Hero Image" class="img-fluid hero-image rounded-4 shadow-lg">
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 text-center text-lg-start">
                <div class="hero-content ps-lg-5">
                    <h1>Bánh ngon<br>Trao trọn yêu thương</h1>
                    <p>Hương vị truyền thống - Chất lượng hàng đầu</p>
                    <br>
                    <a href="<?= url('user/pages/products.php') ?>" class="btn-cta">
                        ĐẶT HÀNG NGAY <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Categories Slider Section -->
<section class="container mt-5 pt-3">
    <div class="section-title">
        <h2>DANH MỤC SẢN PHẨM</h2>
    </div>
    <div class="category-slider-wrapper">
        <button class="slider-btn prev" onclick="slideCategories(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="category-slider-mask">
            <div class="category-slider" id="categorySlider">
                <?php 
                $category_icons = [
                    'Bánh Kem' => 'fa-cake-candles',
                    'Bánh Mì' => 'fa-bread-slice',
                    'Cookies' => 'fa-cookie',
                    'Bánh Bông Lan' => 'fa-layer-group',
                    'Bánh Ngọt Pháp' => 'fa-stroopwafel',
                    'Bánh ngọt Pháp' => 'fa-stroopwafel',
                    'Bánh Truyền Thống' => 'fa-gifts'
                ];

                foreach ($categories as $cat): 
                    $icon = $category_icons[$cat['category_name']] ?? 'fa-cake-candles';
                ?>
                <a href="<?= url('user/pages/products.php?category=' . $cat['category_id']) ?>" class="category-slide-card text-decoration-none">
                    <i class="fas <?= $icon ?>"></i>
                    <h5><?= htmlspecialchars($cat['category_name']) ?></h5>
                    <p><?= $cat['product_count'] ?? 0 ?> sản phẩm</p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <button class="slider-btn next" onclick="slideCategories(1)">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</section>

<script src="<?= url('assets/js/index.js') ?>?v=<?= time() ?>"></script>

<!-- Latest Products Section -->
<section class="product-list-wrapper">
    <div class="container">
        <div class="section-title">
            <h2>SẢN PHẨM MỚI NHẤT</h2>
        </div>
        
        <div class="row g-4">
            <?php foreach ($latest_products as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card">
                    <div class="position-relative">
                        <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>">
                            <img src="<?= upload($p['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($p['product_name']) ?>" 
                                 class="product-image"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </a>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-name">
                            <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" class="text-decoration-none text-dark">
                                <?= mb_strtoupper(htmlspecialchars($p['product_name']), 'UTF-8') ?>
                            </a>
                        </div>
                        
                        <div class="product-price">
                            <?= format_currency($p['price']) ?>
                        </div>
                        
                        <form method="POST" action="<?= url('user/pages/cart-add.php') ?>">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-shopping me-2"></i> THÊM VÀO GIỎ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Best Selling Products Section -->
<section class="product-list-wrapper">
    <div class="container">
        <div class="section-title">
            <h2>SẢN PHẨM BÁN CHẠY</h2>
        </div>
        
        <div class="row g-4">
            <?php foreach ($best_selling as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card">
                    <div class="position-relative">
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2" style="z-index: 10;">
                            <i class="fas fa-fire"></i> Hot
                        </span>
                        <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>">
                            <img src="<?= upload($p['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($p['product_name']) ?>" 
                                 class="product-image"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        </a>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-name">
                            <a href="<?= url('user/pages/product-detail.php?id=' . $p['product_id']) ?>" class="text-decoration-none text-dark">
                                <?= mb_strtoupper(htmlspecialchars($p['product_name']), 'UTF-8') ?>
                            </a>
                        </div>
                        
                        <div class="product-price">
                            <?= format_currency($p['price']) ?>
                        </div>
                        
                        <form method="POST" action="<?= url('user/pages/cart-add.php') ?>">
                            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn-add-cart">
                                <i class="fas fa-cart-shopping me-2"></i> THÊM VÀO GIỎ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="container">
    <div class="features-section">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="feature-card">
                    <i class="fas fa-truck"></i>
                    <h5>GIAO HÀNG NHANH</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="feature-card">
                    <i class="fas fa-medal"></i>
                    <h5>CHẤT LƯỢNG CAO</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="feature-card">
                    <i class="fas fa-leaf"></i>
                    <h5>NGUYÊN LIỆU SẠCH</h5>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="feature-card">
                    <i class="fas fa-undo"></i>
                    <h5>DỄ ĐỔI TRẢ</h5>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Banner / Countdown Section -->
<section class="sale-ribbon-section">
    <div class="container">
        <div class="sale-ribbon-content">
            <h3>CƠ HỘI DUY NHẤT: GIẢM 20% CHO ĐƠN HÀNG MỚI!</h3>
            <p class="text-white mt-3" style="font-size: 18px; font-weight: bold;">[ FREESHIP - KHÔNG GIỚI HẠN ]</p>
        </div>
    </div>
</section>

<!-- Customer Reviews Section -->
<section class="reviews-section">
    <div class="container">
        <div class="section-title">
            <h2>ĐÁNH GIÁ KHÁCH HÀNG</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="review-card">
                    <div class="review-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="review-text">
                        "Bánh rất ngon và mềm, vừa miệng không bị quá ngọt. 
                        Decor bánh cũng rất đẹp mắt, các bạn nhân viên tư vấn nhiệt tình."
                    </div>
                    <div class="review-author">CHỊ MAI</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="review-card">
                    <div class="review-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="review-text">
                        "Mình đã đặt bánh ở đây cho ngày sinh nhật con trai và gia đình rất hài lòng. 
                        Giá cả hợp lý, giao hàng đúng giờ."
                    </div>
                    <div class="review-author">ANH HOÀNG</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="review-card">
                    <div class="review-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="review-text">
                        "Nguyên liệu rất tơi mát và an tâm. Đặc biệt bánh sừng bò ở đây làm rất chuẩn vị. 
                        Sẽ tiếp tục ủng hộ tiệm vào lần sau."
                    </div>
                    <div class="review-author">BẠN LÊ</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/layouts/footer.php';
?>