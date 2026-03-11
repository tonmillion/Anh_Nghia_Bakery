<?php
/**
 * Add to cart Handler
 * *File: user/pages/add-cart.php
 */

require_once '../../includes/init.php';

if (is_method('POST')) {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    // Validate
    if ($product_id <= 0 || $quantity <= 0) {
        set_flash('error', 'Dữ liệu không hợp lệ');
        redirect(url('index.php'));
    }

    // Thêm vào giỏ hàng
    $cart = new Cart();
    $result = $cart->addToCart($product_id, $quantity);

    if ($result['success']) {
        set_flash('success', $result['message']);
    } else {
        set_flash('error', $result['message']);
    }

    // Redirect về trang trước đó hoặc trang chủ
    $redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? url('index.php');
    redirect($redirect);
} else {
    redirect(url('index.php'));
}
?>