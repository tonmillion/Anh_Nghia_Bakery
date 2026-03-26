<?php
/**
 * User Registration Page
 * File: user/register.php
 */

require_once '../includes/init.php';

// Nếu đã đăng nhập thì redirect về trang chủ
if (is_logged_in()) {
    redirect(url('index.php'));
}

$errors = [];
$old_data = [];

// Xử lý form đăng ký
if (is_method('POST')) {
    $is_ajax = isset($_POST['ajax']) ? true : false;
    // Lấy dữ liệu từ form
    $data = [
        'username' => sanitize($_POST['username'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'full_name' => sanitize($_POST['full_name'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'address' => sanitize($_POST['address'] ?? '')
    ];

    // Lưu lại dữ liệu để fill form nếu có lỗi
    $old_data = $data;

    // Validate
    if (empty($data['username'])) {
        $errors['username'] = 'Tên đăng nhập không được để trống';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Mật khẩu không được để trống';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
    }

    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Họ tên không được để trống';
    }

    if (empty($data['email'])) {
        $errors['email'] = 'Email không được để trống';
    } elseif (!is_valid_email($data['email'])) {
        $errors['email'] = 'Email không hợp lệ';
    }

    if (!empty($data['phone']) && !is_valid_phone($data['phone'])) {
        $errors['phone'] = 'Số điện thoại không hợp lệ';
    }

    // Nếu không có lỗi thì đăng ký
    if (empty($errors)) {
        $user = new User();
        $result = $user->register($data);

        if ($result['success']) {
            if ($is_ajax) {
                if (ob_get_level()) ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
            set_flash('success', 'Đăng ký thành công! Vui lòng đăng nhập');
            redirect(url('user/login.php'));
        } else {
            $errors['general'] = $result['message'];
        }
    }

    if ($is_ajax) {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
}

$page_title = 'Đăng ký tài khoản';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('user/css/register.css') ?>?v=<?= time() ?>">
</head>
<body>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>🍰 Đăng Ký Tài Khoản</h1>
                <p class="text-muted">Tạo tài khoản để mua hàng dễ dàng hơn</p>
            </div>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= $errors['general'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" novalidate>
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">
                        Tên đăng nhập <span class="required">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                           id="username" 
                           name="username" 
                           value="<?= $old_data['username'] ?? '' ?>"
                           placeholder="Nhập tên đăng nhập"
                           required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="error-message"><?= $errors['username'] ?></div>
                    <?php endif; ?>
                    <small class="text-muted">Chỉ chứa chữ, số và dấu gạch dưới, ít nhất 4 ký tự</small>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        Mật khẩu <span class="required">*</span>
                    </label>
                    <input type="password" 
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                           id="password" 
                           name="password"
                           placeholder="Nhập mật khẩu"
                           required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                    <small class="text-muted">Ít nhất <?= PASSWORD_MIN_LENGTH ?> ký tự, có chữ và số</small>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">
                        Xác nhận mật khẩu <span class="required">*</span>
                    </label>
                    <input type="password" 
                           class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                           id="confirm_password" 
                           name="confirm_password"
                           placeholder="Nhập lại mật khẩu"
                           required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error-message"><?= $errors['confirm_password'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Full Name -->
                <div class="mb-3">
                    <label for="full_name" class="form-label">
                        Họ và tên <span class="required">*</span>
                    </label>
                    <input type="text" 
                           class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                           id="full_name" 
                           name="full_name"
                           value="<?= $old_data['full_name'] ?? '' ?>"
                           placeholder="Nhập họ và tên"
                           required>
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="error-message"><?= $errors['full_name'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" 
                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" 
                           name="email"
                           value="<?= $old_data['email'] ?? '' ?>"
                           placeholder="example@email.com"
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="tel" 
                           class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           id="phone" 
                           name="phone"
                           value="<?= $old_data['phone'] ?? '' ?>"
                           placeholder="0912345678">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?= $errors['phone'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" 
                              id="address" 
                              name="address" 
                              rows="2"
                              placeholder="Số nhà, tên đường, quận/huyện, tỉnh/thành phố"><?= $old_data['address'] ?? '' ?></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register">
                    Đăng Ký
                </button>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập ngay</a></p>
                <p><a href="<?= url('index.php') ?>">← Quay về trang chủ</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Client-side validation
document.querySelector('form').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Username validation
    const username = document.getElementById('username').value;
    if (username.length < 4) {
        alert('Tên đăng nhập phải có ít nhất 4 ký tự');
        isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length < <?= PASSWORD_MIN_LENGTH ?>) {
        alert('Mật khẩu phải có ít nhất <?= PASSWORD_MIN_LENGTH ?> ký tự');
        isValid = false;
    }
    
    if (password !== confirmPassword) {
        alert('Mật khẩu xác nhận không khớp');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>

</body>
</html>