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

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['ajax'])) {
        $cart_count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cart_count += $item['quantity'];
            }
        }
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message'],
            'cart_count' => $cart_count
        ]);
        exit;
    }

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