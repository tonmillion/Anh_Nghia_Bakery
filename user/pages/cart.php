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

<link rel="stylesheet" href="<?= url('user/css/cart.css') ?>?v=<?= time() ?>">

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
                <a href="<?= url('user/pages/products.php') ?>" class="btn btn-primary btn-lg mt-3" style="text-decoration: none; display: inline-block;">
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