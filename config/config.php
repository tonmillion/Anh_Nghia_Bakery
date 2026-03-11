<?php
/**
 * Genetal configuration
 * File: config/config.php
 * Mô tả: Cấu hình chung cho toàn bộ hệ thống
 */

//Ngăn chặn truy cập trực tiếp
if (!defined('INCLUDE')) {
    define('INCLUDE', true);
}

// ============================================================
// THÔNG TIN WEBSITE
// ============================================================

define('SITE_NAME', 'Anh Nghĩa Bakery');
define('SITE_TITLE', 'Anh Nghĩa Bakery - Bánh Kem - Bánh sinh nhật Bằng Ca');
define('SITE_DESCRIPTION', 'Chuyên cung cấp các loại bánh ngọt');
define('SITE_KEYWORDS', 'bánh kem, bánh sinh nhật, bánh ngọt, Anh Nghĩa Bakery, cookies');

// ============================================================
// URL VÀ PATH
// ============================================================

// URL gốc của website
define('BASE_URL', 'http://AN_Bakery.local/');

// Path tuyệt đối đến thư mục gốc
define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');

// Path đến thư mục quan trọng
define('INCLUDE_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

//URL các thư mục
define('UPLOADS_URL', BASE_URL . 'uploads/');
define('ASSETS_URL', BASE_URL . 'assets/');

// ============================================================
// MÔI TRƯỜNG
// ============================================================

define('ENVIRONMENT', 'development'); //'production hoặc development'

if (ENVIRONMENT === 'development') {
    // Hiển thị tất cả lỗi trong môi trường development
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Tắt hiển thị lỗi trong môi trường production
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);

    //Log lỗi vào file
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . 'logs/error.log');
}

// ============================================================
// SESSION
// ============================================================

define('SESSION_LIFETIME', 86400); //24 giờ (tính bằng giây)
define('SESSION_NAME', 'anb_session');

// ============================================================
// UPLOAD
// ============================================================

define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); //5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ============================================================
// PAGINATION
// ============================================================

define('ITEMS_PER_PAGE', 12);         //Số sản phẩm mỗi trang
define('ADMIN_ITEMS_PER_PAGE', 20);   //Số item mỗi trang trong admin

// ============================================================
// SERCURITY
// ============================================================

define('PASSWORD_MIN_LENGTH', 6);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPS', 5);     //Số lần đăng nhập sai tối đa
define('LOGIN_TIMEOUT', 900);       //15 phút (tính bằng giây)

// ============================================================
// EMAIL
// ============================================================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'anhnghiabakery@gmail.com');   //thay bằng email thật
define('SMTP_PASSWORD', 'bdwvqatbyqzdfslc');          //thay bằng mật khẩu thật
define('SMTP_FROM_EMAIL', 'anhnghiabakery@gmail.com');
define('SMTP_FROM_NAME', 'Anh Nghĩa Bakery');

// ============================================================
// VNPAY
// ============================================================
// Môi trường SANDBOX test
define('VNPAY_TMN_CODE', 'LG95A20I');
define('VNPAY_HASH_SECRET', 'WXT41SF8YON27S1OIXYEAK1H2KOM0R36');
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
define('VNPAY_RETURN_URL', BASE_URL . 'user/pages/payment-return.php');
// Môi trường PRODUCTION thật
// define('VNPAY_TMN_CODE', 'YOUR_REAL_TMN_CODE');
// define('VNPAY_HASH_SECRET', 'YOUR_REAL_HASH_SECRET');
// define('VNPAY_URL', 'https://vnpayment.vn/paymentv2/vpcpay.html');
// define('VNPAY_RETURN_URL', BASE_URL . 'user/pages/payment-return.php');

// ============================================================
// TIMEZONE
// ============================================================

date_default_timezone_set('Asia/Ho_Chi_Minh');

// ============================================================
// CONSTANTS - TRẠNG THÁI ĐƠN HÀNG
// ============================================================

define('ORDER_STATUS', [
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang làm bánh',
    'shipping' => 'Đang giao hàng',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
]);

define('PAYMENT_STATUS', [
    'pending' => 'Chờ thanh toán',
    'paid' => 'Đã thanh toán',
    'failed' => 'Thanh toán thất bại',
]);

define('PAYMENT_METHODS', [
    'COD' => 'Thanh toán khi nhận hàng',
    'VNPAY' => 'Thanh toán qua VNPay',
]);

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Hàm lấy URL đầy đủ
 * @param string $path
 * @return string
 */
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Lấy URL assets
 * @param string $path
 * @return string
 */
function asset($path = '') {
    return ASSETS_URL . ltrim($path, '/');
}

/**
 * Lấy URL uploads
 * @param string $path
 * @return string
 */
function upload($path = '') {
    return UPLOADS_URL . ltrim($path, '/');
}

/**
 * Redirect đến URL
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Kiểm tra request method
 * @param string $method
 * @return bool
 */
function is_method($method) {
    return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
}

/**
 * Lấy giá trị từ POST
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function post($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Lấy giá trị từ GET
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Debug - var_dump và dừng chương trình
 * @param mixed $data
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Format tiền tệ VND
 * @param float $amount
 * @return string
 */
function format_currency($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

/**
 * Format ngày tháng
 * @param string $datetime
 * @param string $format
 * @return string
 */
function format_date($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

/**
 * Tạo slug từ chuỗi tiếng Việt
 * @param string $string
 * @return string
 */
function create_slug($str) {
     $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
    $str = preg_replace("/(đ)/", "d", $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    $str = preg_replace("/(Đ)/", "D", $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}

// ============================================================
// AUTO LOAD
// ============================================================

/**
 * Tự động load file khi cần
 */
spl_autoload_register(function ($className) {
    $file = INCLUDE_PATH . 'classes/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

?>