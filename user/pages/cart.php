<?php
/**
 * Shopping cart page
 * File: user/pages/cart.php
 */

require_once '../../includes/init.php';

$page_title = 'Giỏ hàng - ' . SITE_NAME;

// Xử lý cập nhật số lượng
if (is_method('POST') && isset($_POST['update_cart'])) {
    $cart = new Cart();

    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $cart->updateQuantity($product_id, (int)$quantity);
    }

    set_flash('success', 'Đã cập nhật giỏ hàng.');
    redirect(url('user/pages/cart.php'));
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove'])) {
    $cart = new Cart();
    $product_id = (int)$_GET['remove'];
    $result = $cart->removeFromCart($product_id);

    set_flash($result['success'] ? 'success' : 'error', $result['message']);
    redirect(url('user/pages/cart.php'));
}

// Lấy giỏ hàng
$cart = new Cart();
$cart_items = $cart->getCart();
$total = $cart->getCartTotal();
$count = $cart->getCartCount();

// Include header
include '../../includes/layouts/header.php';
?>

<style>
    .cart-page {
        padding: 30px 0;
        min-height: 60vh;
    }
    
    .cart-header {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .cart-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .cart-table table {
        margin: 0;
    }
    
    .cart-table th {
        background: #667eea;
        color: white;
        padding: 15px;
        font-weight: 600;
    }
    
    .cart-table td {
        padding: 15px;
        vertical-align: middle;
    }
    
    .product-image-small {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .product-info h6 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .product-info p {
        margin: 5px 0 0;
        color: #666;
        font-size: 14px;
    }
    
    .quantity-input-cart {
        display: flex;
        align-items: center;
        border: 2px solid #e1e8ed;
        border-radius: 5px;
        overflow: hidden;
        width: fit-content;
    }
    
    .quantity-input-cart button {
        background: #f8f9fa;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .quantity-input-cart button:hover {
        background: #667eea;
        color: white;
    }
    
    .quantity-input-cart input {
        width: 50px;
        text-align: center;
        border: none;
        padding: 8px;
        font-weight: 600;
    }
    
    .price-column {
        font-size: 18px;
        font-weight: 600;
        color: #ff6b6b;
    }
    
    .subtotal-column {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }
    
    .btn-remove {
        color: #dc3545;
        text-decoration: none;
        font-size: 20px;
        transition: all 0.3s;
    }
    
    .btn-remove:hover {
        color: #a71d2a;
        transform: scale(1.2);
    }
    
    .cart-summary {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    
    .cart-summary h4 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e1e8ed;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 16px;
    }
    
    .summary-row.total {
        font-size: 24px;
        font-weight: bold;
        color: #ff6b6b;
        padding-top: 15px;
        border-top: 2px solid #e1e8ed;
        margin-top: 10px;
    }
    
    .btn-checkout {
        width: 100%;
        padding: 15px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-checkout:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-continue {
        width: 100%;
        padding: 12px;
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        margin-top: 10px;
        transition: all 0.3s;
    }
    
    .btn-continue:hover {
        background: #667eea;
        color: white;
    }
    
    .empty-cart {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .empty-cart i {
        font-size: 100px;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-cart h3 {
        margin-bottom: 15px;
        color: #666;
    }
</style>

<div class="cart-page">
    <div class="container">
        <!-- Header -->
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Giỏ Hàng Của Bạn</h2>
            <p class="text-muted mb-0">Có <?= $count ?> sản phẩm trong giỏ hàng</p>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Cart Table -->
                <div class="col-lg-8">
                    <form method="POST" action="">
                        <div class="cart-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= upload($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                     class="product-image-small"
                                                     onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                                                <div class="product-info">
                                                    <h6><?= htmlspecialchars($item['product_name']) ?></h6>
                                                    <p>Còn lại: <?= $item['stock_quantity'] ?> sản phẩm</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="price-column">
                                            <?= format_currency($item['price']) ?>
                                        </td>
                                        <td>
                                            <div class="quantity-input-cart">
                                                <button type="button" onclick="updateQuantity(<?= $item['product_id'] ?>, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       name="quantity[<?= $item['product_id'] ?>]" 
                                                       id="qty_<?= $item['product_id'] ?>"
                                                       value="<?= $item['quantity'] ?>" 
                                                       min="1" 
                                                       max="<?= $item['stock_quantity'] ?>"
                                                       readonly>
                                                <button type="button" onclick="updateQuantity(<?= $item['product_id'] ?>, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="subtotal-column">
                                            <?= format_currency($item['subtotal']) ?>
                                        </td>
                                        <td>
                                            <a href="?remove=<?= $item['product_id'] ?>" 
                                               class="btn-remove"
                                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= url('user/pages/products.php') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                            </a>
                            <button type="submit" name="update_cart" class="btn btn-primary">
                                <i class="fas fa-sync"></i> Cập nhật giỏ hàng
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4><i class="fas fa-calculator"></i> Tổng Đơn Hàng</h4>
                        
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <strong><?= format_currency($total) ?></strong>
                        </div>
                        
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <strong>Miễn phí</strong>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <strong><?= format_currency($total) ?></strong>
                        </div>
                        
                        <a href="<?= url('user/pages/checkout.php') ?>" class="btn-checkout">
                            <i class="fas fa-credit-card"></i> Tiến hành thanh toán
                        </a>
                        
                        <a href="<?= url('user/pages/products.php') ?>" class="btn-continue">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua hàng
                        </a>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> Thanh toán an toàn & bảo mật
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(productId, change) {
    const Input = document.getElementById('qty_' + productId);
    let value = parseInt(Input.value);
    const max = parseInt(Input.max);
    
    value += change;

    if (value < 1) value = 1;
    if (value > max) value = max;
    
    Input.value = value;
}
</script>

<?php include '../../includes/layouts/footer.php'; ?>