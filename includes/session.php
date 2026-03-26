<?php
/**
 * Session Management
 * File: includes/session.php
 * Mô tả: Khởi tạo và cấu hình session
 */

// Ngăn chặn truy cập trực tiếp
if (!defined('INCLUDE')) {
    define('INCLUDE', true);
}

// ===========================================================
// CẤU HÌNH SESSION
// ===========================================================

// Chỉ start session nếu chưa có session nào
if (session_status() === PHP_SESSION_NONE) {
    
    // Cấu hình session
    ini_set('session.cookie_httponly', 1);   // Chống tấn công XSS
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);

    // Sử dụng HTTPS (uncomment khi deploy production với SSL)
    // ini_set('session.cookie_secure', 1);

    // Session name
    session_name('SESSION_NAME');

    // Khởi tạo session
    session_start();

    // Regenerate session ID định kỳ để tránh session hijacking
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        // Regenerate mỗi 30 phút
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// ===========================================================
// SESSION HELPER FUNCTIONS
// ===========================================================

/**
 * Set session value
 * @param string $key
 * @param mixed $value
 */
function set_session($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Get session value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function session_get($key, $default = null) {
    return $_SESSION[$key] ?? $default;
}

/**
 * Check if session key exists
 * @param string $key
 * @return bool
 */
function session_has($key) {
    return isset($_SESSION[$key]);
}

/**
 * Delete session key
 * @param string $key
 */
function session_delete($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Destroy all session data
 */
function session_destroy_all() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Login user - Lưu thông tin vào session
 * @param array $user
 */
function login_user($user) {
    // Regenerate session ID khi login (bảo mật)
    session_regenerate_id(true);

    // Lưu thông tin user vào session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in_at'] = time();

    // Update last login trong DB
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET updated_at = NOW() WHERE user_id = ?");
        $stmt -> execute([$user['user_id']]);
    } catch (PDOException $e) {
        error_log("Error updating last login: " . $e->getMessage());
    }
}

/**
 * Logout user - Xoá session
 */
function logout_user() {
    session_destroy_all();
    set_flash('success', 'Bạn đã đăng xuất thành công.');
    redirect(url('index.php'));
}

/**
 * Check session timeout
 * @return bool
 */
function check_session_timeout() {
    if (is_logged_in()) {
        $logged_in_at = session_get('logged_in_at', 0);
        $current_time = time();

        // Nếu quá LOGIN_TIMEOUT thì logout
        if ($current_time - $logged_in_at > LOGIN_TIMEOUT) {
            logout_user();
            return true;
        }

        // Update logged_in_at nếu có hoạt động
        $_SESSION['logged_in_at'] = $current_time;
    }

    return false;
}

// ===========================================================
// CART SESSION FUNCTIONS
// ===========================================================

/**
 * Get cart from session
 * @return array
 */
function get_cart() {
    return $_SESSION['cart'] ?? [];
}

/**
 * Add item to cart
 * @param int $product_id
 * @param int $quantity
 */
function add_to_cart($product_id, $quantity = 1) {
    $cart = get_cart();

    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity'] += $quantity;
    } else {
        $cart[$product_id] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
    }

    $_SESSION['cart'] = $cart;
}

/**
 * Update cart item quantity
 * @param int $product_id
 * @param int $quantity
 */
function update_cart($product_id, $quantity) {
    $cart = get_cart();

    if ($quantity <= 0) {
        unset($cart[$product_id]);
    } else {
        if (isset($cart[$product_id])) {
            $cart[$product_id]['quantity'] = $quantity;
        }
    }

    $_SESSION['cart'] = $cart;
}

/**
 * Remove item from cart
 * @param int $product_id
 */
function remove_from_cart($product_id) {
    $cart = get_cart();
    unset($cart[$product_id]);
    $_SESSION['cart'] = $cart;
}

/**
 * Clear cart
 */
function clear_cart() {
    unset($_SESSION['cart']);
}

/** 
 * Get cart count
 * @return int
 */
function get_cart_count() {
    $cart = get_cart();
    $count = 0;

    foreach ($cart as $item) {
        $count += $item['quantity'];
    }

    return $count;
}

/**
 * Get cart total (cần query database để lấy giá)
 * @return float
 */
function get_cart_total() {
    $cart = get_cart();

    if (empty($cart)) {
        return 0;
    }

    try {
        $db = getDB();
        $product_ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        
        $stmt = $db->prepare("SELECT product_id, price FROM products WHERE product_id IN ($placeholders)");
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = 0;
        foreach ($products as $product) {
            $quantity = $cart[$product['product_id']]['quantity'];
            $total += $product['price'] * $quantity;
        }

        return $total;

    } catch (PDOException $e) {
        error_log("Error calculating cart total: " . $e->getMessage());
        return 0;
    }
}

?>