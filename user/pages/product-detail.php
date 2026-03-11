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

<style>
    .product-detail {
        padding: 30px 0;
    }
    
    .product-image-main {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        margin-bottom: 20px;
    }
    
    .product-image-main img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }
    
    .product-info-box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .product-title {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }
    
    .product-category {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .product-price-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
    }
    
    .product-price {
        font-size: 36px;
        color: #ff6b6b;
        font-weight: bold;
    }
    
    .product-meta {
        display: flex;
        gap: 20px;
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        color: #666;
    }
    
    .meta-item i {
        color: #667eea;
    }
    
    .stock-status {
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: 500;
        display: inline-block;
        margin: 15px 0;
    }
    
    .stock-status.in-stock {
        background: #d4edda;
        color: #155724;
    }
    
    .stock-status.out-of-stock {
        background: #f8d7da;
        color: #721c24;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 20px 0;
    }
    
    .quantity-input {
        display: flex;
        align-items: center;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .quantity-input button {
        background: #f8f9fa;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        font-size: 18px;
        transition: all 0.3s;
    }
    
    .quantity-input button:hover {
        background: #667eea;
        color: white;
    }
    
    .quantity-input input {
        width: 60px;
        text-align: center;
        border: none;
        padding: 10px;
        font-size: 16px;
        font-weight: 600;
    }
    
    .btn-add-cart-large {
        padding: 15px 40px;
        font-size: 18px;
        font-weight: 600;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-add-cart-large:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-buy-now {
        padding: 15px 40px;
        font-size: 18px;
        font-weight: 600;
        background: #ff6b6b;
        color: white;
        border: none;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-buy-now:hover {
        background: #ff5252;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }
    
    .product-description {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    
    .product-description h4 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .related-products {
        margin-top: 50px;
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .section-title h3 {
        font-size: 28px;
        font-weight: bold;
        color: #333;
    }
</style>

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
            <div class="col-lg-5">
                <div class="product-image-main">
                    <img src="<?= upload($detail['image_url']) ?>" 
                         alt="<?= htmlspecialchars($detail['product_name']) ?>"
                         onerror="this.src='https://via.placeholder.com/500x500?text=<?= urlencode($detail['product_name']) ?>'">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="product-info-box">
                    <span class="product-category">
                        <i class="fas fa-tag"></i> <?= htmlspecialchars($detail['category_name']) ?>
                    </span>
                    
                    <h1 class="product-title"><?= htmlspecialchars($detail['product_name']) ?></h1>
                    
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
                    
                    <div class="product-price-box">
                        <div class="product-price"><?= format_currency($detail['price']) ?></div>
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
