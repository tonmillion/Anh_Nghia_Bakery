<?php
/**
 * Product Detail Page
 * File: user/pages/product-detail.php
 */

require_once '../../includes/init.php';

// Lấy product_id từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    set_flash('error', 'Sản phẩm không tồn tại');
    redirect(url('user/pages/products.php'));
}

// Lấy thông tin sản phẩm
$product = new Product();
$detail = $product->getProductById($product_id);

if (!$detail) {
    set_flash('error', 'Sản phẩm không tồn tại');
    redirect(url('user/pages/products.php'));
}

// Lấy sản phẩm liên quan
$relate_products = $product->getRelatedProducts($detail['category_id'], $product_id, 4);

$page_title = $detail['product_name'] . ' - ' . SITE_NAME;

// Include header
include '../../includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= url('user/css/product-detail.css') ?>?v=<?= time() ?>">

<div class="product-detail">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= url('user/pages/products.php') ?>">Sản phẩm</a></li>
                <li class="breadcrumb-item"><a href="<?= url('user/pages/products.php?category=' . $detail['category_id']) ?>"><?= htmlspecialchars($detail['category_name']) ?></a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($detail['product_name']) ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-7">
                <div class="product-image-main">
                    <img src="<?= upload($detail['image_url']) ?>" 
                         alt="<?= htmlspecialchars($detail['product_name']) ?>"
                         onerror="this.src='https://via.placeholder.com/600x600?text=<?= urlencode($detail['product_name']) ?>'">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-5">
                <div class="product-info-box">
                    <span class="product-category">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($detail['category_name']) ?>
                    </span>
                    
                    <h1 class="product-title"><?= htmlspecialchars($detail['product_name']) ?></h1>
                    
                    <div class="product-price-box">
                        <div class="product-price"><?= format_currency($detail['price']) ?></div>
                    </div>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-eye"></i>
                            <span><?= $detail['view_count'] ?> lượt xem</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Đã bán <?= $detail['sold_count'] ?></span>
                        </div>
                    </div>
                    
                    <?php if ($detail['stock_quantity'] > 0): ?>
                        <div class="stock-status in-stock">
                            <i class="fas fa-check-circle"></i> Còn hàng (<?= $detail['stock_quantity'] ?> sản phẩm)
                        </div>
                        
                        <form method="POST" action="<?= url('user/pages/cart-add.php') ?>" id="addToCartForm">
                            <input type="hidden" name="product_id" value="<?= $detail['product_id'] ?>">
                            
                            <div class="quantity-selector">
                                <label><strong>Số lượng:</strong></label>
                                <div class="quantity-input">
                                    <button type="button" onclick="decreaseQuantity()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="<?= $detail['stock_quantity'] ?>"
                                           readonly>
                                    <button type="button" onclick="increaseQuantity()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn-add-cart-large">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                                <button type="button" class="btn-buy-now" onclick="buyNow()">
                                    <i class="fas fa-bolt"></i> Mua ngay
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="stock-status out-of-stock">
                            <i class="fas fa-times-circle"></i> Tạm hết hàng
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Description -->
        <div class="product-description">
            <h4><i class="fas fa-info-circle"></i> Mô Tả Sản Phẩm</h4>
            <div class="description-content">
                <?= nl2br(htmlspecialchars($detail['description'])) ?>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
        <div class="related-products">
            <div class="section-title">
                <h3><i class="fas fa-boxes"></i> Sản Phẩm Liên Quan</h3>
            </div>
            
            <div class="row g-4">
                <?php foreach ($related_products as $p): ?>
                <div class="col-lg-3 col-md-6">
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
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const maxQuantity = <?= $detail['stock_quantity'] ?>;

function increaseQuantity() {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value);
    if (value < maxQuantity) {
        input.value = value + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value);
    if (value > 1) {
        input.value = value - 1;
    }
}

function buyNow() {
    // Thêm vào giỏ và redirect sang checkout
    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);
    
    fetch('<?= url('user/pages/cart-add.php') ?>', {
        method: 'POST',
        body: formData
    })
    .then(() => {
        window.location.href = '<?= url('user/pages/checkout.php') ?>';
    });
}

// Validate quantity khi submit
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    const quantity = parseInt(document.getElementById('quantity').value);
    if (quantity < 1 || quantity > maxQuantity) {
        e.preventDefault();
        alert(`Số lượng phải từ 1 đến ${maxQuantity}`);
    }
});
</script>

<?php include '../../includes/layouts/footer.php'; ?>
