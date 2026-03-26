<?php
/**
 * Header Layout
 * File: includes/layouts/header.php
 */

// Lấy thông tin giỏ hàng
$cart = new Cart();
$cart_count = $cart->getCartCount();

// Lấy thông tin user nếu đã đăng nhập
$current_user = is_logged_in() ? get_logged_in_user() : null;

// Lấy danh mục để hiển thị menu
$category = new Category();
//$categories = $category->getAllCategories();
$categories = $category->getCategoriesWithCount();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=  SITE_DESCRIPTION ?>">
    <meta name="keywords" content="<?=  SITE_KEYWORDS ?>">
    <title><?= $page_title ?? SITE_TITLE ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

     
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Bakery Theme Header Styles */
        :root {
            --primary-orange: #F7B75E;
            --dark-orange: #E67E22;
            --light-beige: #FDF6ED;
            --dark-brown: #5E3A21;
            --soft-pink: #F0B8B4;
            --dark-pink: #E59A96;
            --wave-beige: #F2E2D2;
        }

        body {
            background-color: var(--light-beige);
            color: var(--dark-brown);
            font-family: 'Quicksand', sans-serif;
        }

        .top-bar {
            background: var(--dark-orange);
            color: white;
            padding: 8px 0;
            font-size: 14px;
        }
        
        .top-bar a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        
        .top-bar a:hover {
            text-decoration: underline;
        }
        
        .navbar {
            background: var(--primary-orange);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: bold;
            color: var(--dark-brown) !important;
        }
        
        .navbar-brand i {
            color: var(--dark-brown);
        }
        
        .search-box {
            width: 100%;
            max-width: 650px;
            position: relative;
        }
        
        .search-box input {
            border-radius: 25px;
            padding: 10px 45px 10px 20px;
            border: 2px solid rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.9);
            color: var(--dark-brown);
        }
        
        .search-box input:focus {
            border-color: var(--dark-brown);
            box-shadow: none;
            outline: none;
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: var(--dark-brown);
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }
        
        .cart-icon {
            position: relative;
            color: var(--dark-brown);
            font-size: 24px;
            margin: 0 15px;
        }

        .cart-icon:hover {
            color: white;
        }
        
        .cart-icon .badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: var(--dark-brown);
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 11px;
        }
        
        .user-menu .dropdown-toggle {
            background: none;
            border: none;
            color: var(--dark-brown);
            font-size: 18px; /* Tăng cỡ chữ username */
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 10px;
        }
        
        .user-menu .dropdown-toggle i {
            font-size: 26px; /* Tăng icon người dùng to ngang giỏ hàng */
            vertical-align: middle;
        }
        
        .user-menu .dropdown-toggle:hover {
            color: white;
        }
        
        .user-menu .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(94, 58, 33, 0.15);
            padding: 15px 0;
            background: var(--light-beige);
            min-width: 200px;
        }
        
        .user-menu .dropdown-item {
            color: var(--dark-brown);
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .user-menu .dropdown-item i {
            margin-right: 10px;
            color: var(--dark-orange);
            width: 20px;
            text-align: center;
        }
        
        .user-menu .dropdown-item:hover {
            background: var(--wave-beige);
            color: var(--dark-orange);
            padding-left: 25px;
        }
        
        .user-menu .dropdown-divider {
            border-top: 1px dashed rgba(94, 58, 33, 0.2);
            margin: 10px 0;
        }
        
        .user-menu .dropdown-item.text-danger:hover {
            color: #dc3545 !important;
            background: #fff5f5;
        }
        
        .main-nav {
            background: var(--primary-orange);
            padding: 0 0 12px 0;
        }
        
        .main-nav .nav-link {
            color: var(--dark-brown);
            font-weight: bold;
            padding: 8px 20px;
            transition: all 0.3s;
            text-transform: uppercase;
        }
        
        .main-nav .nav-link:hover, .main-nav .nav-link.active {
            color: white;
            background: var(--dark-brown);
            border-radius: 20px;
        }

        .btn-outline-primary {
            color: var(--dark-brown);
            border-color: var(--dark-brown);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--dark-brown);
            color: white;
            border-color: var(--dark-brown);
        }
        
        /* Login Modal Styles */
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 40px rgba(94, 58, 33, 0.15);
            border: 1px solid rgba(94, 58, 33, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header .logo {
            font-size: 60px;
            margin-bottom: 10px;
            display: inline-block;
            background: var(--wave-beige);
            width: 100px;
            height: 100px;
            line-height: 100px;
            border-radius: 50%;
            box-shadow: inset 0 0 15px rgba(94, 58, 33, 0.05);
        }
        .login-header h1 {
            color: var(--dark-brown);
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .login-header p {
            color: var(--dark-brown);
            font-size: 15px;
            opacity: 0.8;
        }
        .login-card .form-label {
            font-weight: bold;
            color: var(--dark-brown);
            margin-bottom: 8px;
        }
        .login-card .form-control {
            padding: 12px 15px;
            border: 2px solid rgba(94, 58, 33, 0.1);
            border-radius: 0 15px 15px 0;
            font-size: 15px;
            font-weight: 500;
            color: var(--dark-brown);
        }
        .login-card .form-control:focus {
            border-color: var(--dark-orange);
            box-shadow: none;
        }
        .login-card .input-group:focus-within .input-group-text,
        .login-card .input-group:focus-within .form-control {
            border-color: var(--dark-orange);
            box-shadow: 0 0 0 0.25rem rgba(230, 126, 34, 0.25);
        }
        .login-card .input-group-text {
            background: white;
            border: 2px solid rgba(94, 58, 33, 0.1);
            border-right: none;
            border-radius: 15px 0 0 15px;
            color: var(--dark-orange);
        }
        .login-card .input-group .form-control {
            border-left: none;
        }
        .btn-login {
            background: var(--dark-orange);
            border: none;
            color: white;
            padding: 14px;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            border-radius: 30px;
            margin-top: 15px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(94, 58, 33, 0.3);
            background: var(--dark-brown);
            color: white;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        .form-check-label {
            color: var(--dark-brown);
            cursor: pointer;
        }
        .forgot-link {
            color: var(--dark-orange);
            text-decoration: none;
            font-weight: bold;
        }
        .forgot-link:hover {
            color: var(--dark-brown);
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
            background: rgba(94, 58, 33, 0.1);
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: var(--dark-brown);
            font-size: 14px;
            font-weight: bold;
            opacity: 0.6;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-brown);
            font-size: 15px;
            font-weight: 500;
        }
        .signup-link a {
            color: var(--dark-orange);
            text-decoration: none;
            font-weight: bold;
        }
        .signup-link a:hover {
            color: var(--dark-brown);
        }
        .error-message {
            color: var(--dark-pink);
            font-size: 14px;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-phone"></i> Hotline: 1900 1234
                <i class="fas fa-envelope ms-3"></i> info@bakeryshop.vn
            </div>
            <div>
                <?php if ($current_user): ?>
                    <span><i class="fas fa-user"></i> Xin chào, <?= htmlspecialchars($current_user['full_name']) ?></span>
                <?php else: ?>
                    <span><i class="fas fa-user"></i> Xin chào quý khách!</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand logo-link" href="<?= url('index.php') ?>">
            <img src="<?= asset('images/logo.png') ?>?v=<?= time() ?>" alt="<?= SITE_NAME ?>" style="height: 100px; display: inline-block;">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Search Box -->
            <div class="flex-grow-1 d-flex justify-content-center my-3 my-lg-0 px-lg-4">
                <div class="search-box">
                    <form action="<?= url('user/pages/search.php') ?>" method="GET" class="w-100">
                        <input type="text" 
                               class="form-control w-100" 
                               name="q" 
                               placeholder="Tìm kiếm sản phẩm..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Menu -->
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Cart -->
                <li class="nav-item">
                    <a href="<?= url('user/pages/cart.php') ?>" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- User Menu -->
                <li class="nav-item dropdown user-menu">
                    <?php if ($current_user): ?>
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($current_user['username']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('user/pages/account.php') ?>">
                                <i class="fas fa-user"></i> Tài khoản
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url('user/pages/orders.php') ?>">
                                <i class="fas fa-shopping-bag"></i> Đơn hàng
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (is_admin()): ?>
                                <li><a class="dropdown-item" href="<?= url('admin/index.php') ?>">
                                    <i class="fas fa-cog"></i> Quản trị
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger" href="<?= url('user/logout.php') ?>">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a></li>
                        </ul>
                    <?php else: ?>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="btn btn-login-nav btn-sm">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Custom Login Button for Navbar */
.btn-login-nav {
    background-color: var(--dark-brown);
    color: white !important;
    border-radius: 30px;
    padding: 6px 20px;
    font-weight: 700;
    border: 2px solid var(--dark-brown);
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(94, 58, 33, 0.2);
}

.btn-login-nav:hover {
    background-color: white;
    color: var(--dark-brown) !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(94, 58, 33, 0.3);
}

.btn-login-nav i {
    margin-right: 5px;
}
</style>

<!-- Navigation Menu -->
<div class="main-nav">
    <div class="container">
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link" href="<?= url('index.php') ?>">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('user/pages/products.php') ?>">
                    <i class="fas fa-layer-group"></i> Tất cả sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('about.php') ?>">
                    <i class="fas fa-info-circle"></i> Giới thiệu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('contact.php') ?>">
                    <i class="fas fa-phone"></i> Liên hệ
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Flash Messages -->
<?php 
$flash = get_flash();
if ($flash): 
?>
<div class="container mt-3">
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
        <?= $flash['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 25px; border: none; background: transparent;">
        <div class="login-card w-100 m-0">
            <div class="login-header">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 25px; right: 25px;"></button>
                <div class="logo">🍰</div>
                <h1>Đăng Nhập</h1>
                <p>Chào mừng bạn quay trở lại!</p>
            </div>

            <div id="modal-login-errors"></div>

            <form id="modalLoginForm">
                <!-- Username -->
                <div class="mb-3">
                    <label for="modal-username" class="form-label">
                        <i class="fas fa-user"></i> Tên đăng nhập
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <input type="text" class="form-control" id="modal-username" name="username" placeholder="Nhập tên đăng nhập" required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="modal-password" class="form-label">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" class="form-control" id="modal-password" name="password" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="remember-forgot">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="modal-remember" name="remember">
                        <label class="form-check-label" for="modal-remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <a href="#" class="forgot-link">Quên mật khẩu?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login" id="modal-login-btn">
                    <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                </button>
            </form>

            <div class="divider">
                <span>hoặc</span>
            </div>

            <div class="signup-link">
                Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Đăng ký ngay</a>
            </div>
            
            <?php if (ENVIRONMENT === 'development'): ?>
            <div class="alert alert-info mt-3 mb-0" style="font-size: 13px;">
                <strong>🔧 Test Accounts:</strong><br>
                Admin: <code>admin / password</code><br>
                User: <code>nguyenvana / password</code>
            </div>
            <?php endif; ?>
        </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('modalLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('modal-login-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;
            
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            fetch('<?= url("user/login.php") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('modal-login-errors');
                if (data.success) {
                    errorDiv.innerHTML = '<div class="alert alert-success">Đăng nhập thành công! Đang tải lại trang...</div>';
                    window.location.reload();
                } else {
                    btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng Nhập';
                    btn.disabled = false;
                    
                    let errorHtml = '';
                    if (data.errors) {
                        for (const [key, msg] of Object.entries(data.errors)) {
                            errorHtml += `<div class="error-message"><i class="fas fa-exclamation-triangle"></i> ${msg}</div>`;
                        }
                    } else if (data.message) {
                        errorHtml = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message}</div>`;
                    } else if (data.general) {
                        errorHtml = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.general}</div>`;
                    }
                    errorDiv.innerHTML = errorHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.getElementById('modal-login-errors');
                errorDiv.innerHTML = '<div class="alert alert-danger">Đã có lỗi kết nối xảy ra. Vui lòng thử lại.</div>';
                btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng Nhập';
                btn.disabled = false;
            });
        });
    }
});
</script>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 25px; border: none; background: transparent;">
        <div class="login-card w-100 m-0">
            <div class="login-header">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 25px; right: 25px;"></button>
                <div class="logo">🎂</div>
                <h1>Đăng Ký Tài Khoản</h1>
                <p>Tạo tài khoản để nhận nhiều ưu đãi hơn!</p>
            </div>

            <div id="modal-register-errors"></div>

            <form id="modalRegisterForm">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Username -->
                        <div class="mb-3">
                            <label for="reg-username" class="form-label">
                                <i class="fas fa-user"></i> Tên đăng nhập <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                <input type="text" class="form-control" id="reg-username" name="username" placeholder="Ít nhất 4 ký tự" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="reg-password" class="form-label">
                                <i class="fas fa-lock"></i> Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="reg-password" name="password" placeholder="Mật khẩu" required onscreen-password>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="reg-confirm" class="form-label">
                                <i class="fas fa-check-double"></i> Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                <input type="password" class="form-control" id="reg-confirm" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="reg-name" class="form-label">
                                <i class="fas fa-id-card"></i> Họ và tên <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <input type="text" class="form-control" id="reg-name" name="full_name" placeholder="Họ và tên của bạn" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="reg-email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-at"></i></span>
                                <input type="email" class="form-control" id="reg-email" name="email" placeholder="example@email.com" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="reg-phone" class="form-label">
                                <i class="fas fa-phone"></i> Số điện thoại
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                                <input type="tel" class="form-control" id="reg-phone" name="phone" placeholder="0xxx xxx xxx">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="reg-address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-home"></i></span>
                                <textarea class="form-control" id="reg-address" name="address" rows="3" placeholder="Địa chỉ giao hàng"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-login" id="modal-register-btn">
                    <i class="fas fa-user-plus"></i> Đăng Ký Ngay
                </button>
            </form>

            <div class="divider">
                <span>hoặc</span>
            </div>

            <div class="signup-link">
                Đã có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logic cho Modal Đăng ký
    const registerForm = document.getElementById('modalRegisterForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('modal-register-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;
            
            const formData = new FormData(this);
            formData.append('ajax', '1');
            
            fetch('<?= url("user/register.php") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorDiv = document.getElementById('modal-register-errors');
                if (data.success) {
                    errorDiv.innerHTML = '<div class="alert alert-success">Đăng ký thành công! Đang chuyển sang đăng nhập...</div>';
                    setTimeout(() => {
                        const regModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                        regModal.hide();
                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                        loginModal.show();
                        // Reset form đăng ký
                        registerForm.reset();
                        errorDiv.innerHTML = '';
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 2000);
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    
                    let errorHtml = '<div class="alert alert-danger"><ul>';
                    if (data.errors) {
                        for (const [key, msg] of Object.entries(data.errors)) {
                            errorHtml += `<li>${msg}</li>`;
                        }
                    } else if (data.message) {
                        errorHtml += `<li>${data.message}</li>`;
                    }
                    errorHtml += '</ul></div>';
                    errorDiv.innerHTML = errorHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.getElementById('modal-register-errors');
                errorDiv.innerHTML = '<div class="alert alert-danger">Đã có lỗi kết nối xảy ra. Vui lòng thử lại.</div>';
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});
</script>

<!-- Main Content Wrapper -->
<main class="content-wrapper">