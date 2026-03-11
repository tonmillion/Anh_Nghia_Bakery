<?php
/**
 * User Login Page
 * File: user/login.php
 */

require_once '../includes/init.php';

// Nếu đã đăng nhập thì redirect
if (is_logged_in()) {
    redirect(url('index.php'));
}

$errors = [];
$username_old = '';

// Xử lý form đăng nhập
if (is_method('POST')) {
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

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $result['user']['full_name']);

            // Redirect vể trang trước đó hoặc trang chủ
            $redirect_to = $_GET['redirect'] ?? 'index.php';
            redirect(url($redirect_to));
        } else {
            $errors['general'] = $result['message'];
        }
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
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header .logo {
            font-size: 60px;
            margin-bottom: 15px;
        }
        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            border-radius: 8px;
            margin-top: 10px;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .form-check-label {
            color: #666;
        }
        .forgot-link {
            color: #667eea;
            text-decoration: none;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e1e8ed;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #999;
            font-size: 14px;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .back-home {
            text-align: center;
            margin-top: 15px;
        }
        .back-home a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
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
                Chưa có tài khoản? <a href="<?= url('user/register.php') ?>">Đăng ký ngay</a>
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