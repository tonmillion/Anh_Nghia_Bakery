<?php
/**
 * User Login Page
 * File: user/login.php
 */

require_once '../includes/init.php';

// Nếu đã đăng nhập thì redirect
if (is_logged_in()) {
    if (isset($_POST['ajax'])) {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    redirect(url('index.php'));
}

$errors = [];
$username_old = '';

// Xử lý form đăng nhập
if (is_method('POST')) {
    $is_ajax = isset($_POST['ajax']) ? true : false;
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $username_old = $username; // Lưu lại để fill form

    // Validate
    if (empty($username)) {
        $errors['username'] = 'Vui lòng nhập tên đăng nhập';
    }

    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    }

    // Nếu không có lỗi validate thì xử lý đăng nhập
    if (empty($errors)) {
        $user = new User();
        $result = $user->login($username, $password);

        if ($result['success']) {
            // Lưu thông tin vào session
            login_user($result['user']);

            // Đồng bộ giỏ hàng tử session sang database
            $cart = new Cart();
            $cart ->syncCartToDatabase($result['user']['user_id']);

            // Remember me (optional - lưu cookie)
            if ($remember) {
                // Tạo token và lưu cookie (có thể làm thêm nếu cần)
                setcookie('remember_token', bin2hex(random_bytes(32)), time() + 30*24*3600, '/');
            }

            if ($is_ajax) {
                if (ob_get_level()) ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $result['user']['full_name']);

            // Redirect vể trang trước đó hoặc trang chủ
            $redirect_to = $_GET['redirect'] ?? 'index.php';
            redirect(url($redirect_to));
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

$page_title = 'Đăng nhập';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('user/css/login.css') ?>?v=<?= time() ?>">
</head>
<body>

<div class="container">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">🍰</div>
                <h1>Đăng Nhập</h1>
                <p>Chào mừng bạn quay trở lại!</p>
            </div>

            <?php 
            // Hiển thị flash message (từ register hoặc logout)
            $flash = get_flash();
            if ($flash): 
            ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $errors['general'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Tên đăng nhập
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <input type="text" 
                               class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                               id="username" 
                               name="username" 
                               value="<?= $username_old ?>"
                               placeholder="Nhập tên đăng nhập"
                               autofocus
                               required>
                    </div>
                    <?php if (isset($errors['username'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i> <?= $errors['username'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" 
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password"
                               placeholder="Nhập mật khẩu"
                               required>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i> <?= $errors['password'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Remember & Forgot -->
                <div class="remember-forgot">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                </button>
            </form>

            <div class="divider">
                <span>hoặc</span>
            </div>

            <div class="signup-link">
                Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký ngay</a>
            </div>

            <div class="back-home">
                <a href="<?= url('index.php') ?>">
                    <i class="fas fa-home"></i> Quay về trang chủ
                </a>
            </div>
        </div>

        <!-- Quick Login Info (Development only) -->
        <?php if (ENVIRONMENT === 'development'): ?>
        <div class="alert alert-info mt-3" style="font-size: 13px;">
            <strong>🔧 Test Accounts:</strong><br>
            Admin: <code>admin / password</code><br>
            User: <code>nguyenvana / password</code>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>