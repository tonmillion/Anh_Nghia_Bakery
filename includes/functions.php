<?php
/**
 * Helper Functions
 * File: includes/functions.php
 * Mô tả: Chứa các hàm tiện ích dùng chung
 */

// ===========================================================
// SERCURITY FUNCTIONS
// ===========================================================

/**
 * Sanitize input - Làm sạch dư liệu đầu vào
 * @param string $data
 * @return mixed
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    } 
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate number (Việt Nam)
 * @param string $phone
 * @return bool
 */
function is_valid_phone($phone) {
    // Số điện thoại VN: 10 - 11 số, bắt đầu bằng 0
    return preg_match('/^0[0-9]{9,10}$/', $phone);
}

/**
 * Hash password
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 *Generate CSRF token
 * @return string
 */
function generate_csrf_token() {
    if (!isset($_SESSION['CSRF_TOKEN_NAME'])) {
        $_SESSION['CSRF_TOKEN_NAME'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['CSRF_TOKEN_NAME'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    return isset($_SESSION['CSRF_TOKEN_NAME']) && 
           hash_equals($_SESSION['CSRF_TOKEN_NAME'], $token);
}

/**
 * Generate ramdon string
 * @param int $length
 * @return string
 */
function generate_random_string($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// ============================================================
// SESSION FUNCITONS
// ============================================================

/**
 * Set flash message
 * @param string $type ('success', 'error', 'info')
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login - Redirect nếu chưa đăng nhập
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect(url('user/login.php'));
    }
}

/**
 * Require admin - Redirect nếu không phải admin
 */
function require_admin() {
    if (!is_admin()) {
        set_flash('error', 'Bạn không có quyền truy cập trang này.');
        redirect(url('index.php'));
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user info
 * @return array|null
 */
function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }

    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT user_id, username, full_name, email, phone, role
                              FROM users WHERE user_id = ?");
        $stmt->execute([get_user_id()]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Lỗi lấy thông tin user hiện tại: " . $e->getMessage());
        return null;
    }
}

// ============================================================
// VALIDATION FUNCTIONS
// ============================================================

/**
 * Validate required fields
 * @param array $fields
 * @return array Mảng lỗi
 */
function validate_required_fields($fields) {
    $errors = [];
    
    foreach ($fields as $name => $label) {
        if (empty($_POST[$name])) {
            $errors[$name] = $label . ' không được để trống';
        }
    }

    return $errors;
}

/**
 * Validate password strength
 * @param string $password
 * @return string|null Error message hoặc null nếu OK
 */
function validate_password($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return 'Mật khẩu phải có ít nhất ' . PASSWORD_MIN_LENGTH . ' ký tự.';
    }

    // Kiểm tra có cả chữ và số
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        return 'Mật khẩu phải bao gồm cả chữ và số.';
    }

    return null;
}

/**
 * Validate file upload
 * @param array $file $_FILES['file']
 * @param array $allowed_types
 * @param int $max_size
 * @return string|null Error message hoặc null nếu OK
 */
function validate_file_upload($file, $allowed_types = ALLOWED_IMAGE_TYPES, $max_size = MAX_UPLOAD_SIZE) {
    // Kiểm tra có lỗi upload không
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Lỗi khi tải lên file.';
    }
    
    // Kiểm tra kích thước
    if ($file['size'] > $max_size) {
        $max_mb = $max_size / 1024 / 1024;
        return 'Kích thước file không được vượt quá giới hạn ' . $max_mb . ' MB.';
    }

    // Kiểm tra định dạng
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        return 'Định dạng file không hợp lệ.';
    }

    return null;
}

// ============================================================
// FILE UPLOAD FUNCTIONS
// ============================================================

/**
 * Upload image
 * @param array $file $_FILES['file']
 * @param string $folder Thư mục đích (trong uploads)
 * @param string $prefix Tiền tố tên file
 * @return string|false Tên file mới hoặc false nếu lỗi
 */
function upload_image($file, $folder = 'products', $prefix = 'img_') {
    // Validate
    $error = validate_file_upload($file);
    if ($error) {
        set_flash('error', $error);
        return false;
    }

    // Tạo tên file mới (unique)
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;

    // Đường dẫn đầy đủ
    $upload_path = UPLOADS_PATH . $folder . '/';

    // Tạo thư mục nếu chưa có
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    $destination = $upload_path . $new_filename;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Resize nếu ảnh quá lớn
        resize_image($destination, 800, 800);

        return $folder . '/' . $new_filename;
    } 
    
    return false;
}

/**
 * Resize image (giữ nguyên tỉ lệ)
 * @param string $file_path
 * @param int $max_width
 * @param int $max_height
 * @return bool
 */
function resize_image($file_path, $max_width = 800, $max_height = 800) {
    // Kiểm tra extension GD
    if (!extension_loaded('gd')) {
        return false;
    }

    list($width, $height, $type) = getimagesize($file_path);

    // Không cần resize nếu ảnh nhỏ hơn max
    if ($width <= $max_width && $height <= $max_height) {
        return true;
    }

    // Tính tỉ lệ
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = (int)($width * $ratio);
    $new_height = (int)($height * $ratio);

    // Tạo image từ file
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($file_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($file_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($file_path);
            break;
        default:
            return false;
    }

    // Tạo ảnh mới
    $destination = imagecreatetruecolor($new_width, $new_height);

    // Giữ transparency cho PNG và GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($destination, imagecolorallocatealpha($destination, 0, 0, 0, 127));
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
    }

    // Resize
    imagecopyresampled ($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Lưu lại file
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($destination, $file_path, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($destination, $file_path, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($destination, $file_path);
            break;
    }

    imagedestroy($source);
    imagedestroy($destination);

    return true;
}

/**
 * Delete file
 * @param string $file_path Đường dẫn tương đối từ thư mục uploads/ 
 * @return bool
 */
function delete_file($file_path) {
    $full_path = UPLOADS_PATH . $file_path;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return false;
}

// ============================================================
// PAGINATION FUNCTIONS
// ============================================================

/**
 * Tạo pagination
 * @param int $total_items
 * @param int $current_page
 * @param int $items_per_page
 * @param string $base_url
 * @return array
 */
function paginate($total_items, $items_per_page = ITEMS_PER_PAGE, $current_page = 1, $base_url = '') {
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = max(1, min($current_page, $total_pages));

    $offset = ($current_page - 1) * $items_per_page;

    // Tính range hiển thị
    $range = 2; // Số trang hiển thị trước và sau trang hiện tại
    $start_page = max(1, $current_page - $range);
    $end_page = min($total_pages, $current_page + $range);

    return [
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'items_per_page' => $items_per_page,
        'offset' => $offset,
        'start_page' => $start_page,
        'end_page' => $end_page,
        'base_url' => $base_url,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

// ============================================================
// STRING FUNCTIONS
// ============================================================

/**
 * Cắt chuỗi và thêm ...
 * @param string $text
 * @param int $length
 * @return string
 */
function excerpt($text, $length = 100) {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Highlight keyword trong text
 * @param string $text
 * @param string $keyword
 * @return string
 */
function highlight_keyword($text, $keyword) {
    if (empty($keyword)) {
        return $text;
    }
    return preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark>$1</mark>', $text);
}

// ============================================================
// ARRAY FUNCTIONS
// ============================================================

/**
 * Get value from array by key with default
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function array_get($array, $key, $default = null) {
    return $array[$key] ?? $default;
}

/**
 * Pluck values from array of arrays
 * @param array $array
 * @param string $key
 * @return array
 */
function array_pluck($array, $key) {
    return array_map(function($item) use ($key) {
        return is_array($item) ? $item[$key] : $item -> $key;
    }, $array);
}

// ============================================================
// DEBUG FUNCTIONS
// ============================================================

/**
 * Pretty print (chỉ dùng khi development)
 * @param mixed $data
 */
function pr($data) {
    if (ENVIRONMENT === 'development') {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Log to file
 * @param mixed $data
 * @param string $filename
 */
function log_data($data, $filename = 'debug.log') {
    $log_path = ROOT_PATH . '/logs/';
    if (!is_dir($log_path)) {
        mkdir($log_path, 0755, true);
    }

    $message = date('Y-m-d H:i:s') . ' - ' . print_r($data, true) . PHP_EOL;
    file_put_contents($log_path . $filename, $message, FILE_APPEND);
}

?>