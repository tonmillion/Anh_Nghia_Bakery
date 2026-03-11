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
    
    <style>
        /* Header Styles */
        .top-bar {
            background: #667eea;
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
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-size: 28px;
            font-weight: bold;
            color: #ff6b6b !important;
        }
        
        .navbar-brand i {
            color: #ff9f43;
        }
        
        .search-box {
            max-width: 500px;
            position: relative;
        }
        
        .search-box input {
            border-radius: 25px;
            padding: 10px 45px 10px 20px;
            border: 2px solid #e1e8ed;
        }
        
        .search-box input:focus {
            border-color: #667eea;
            box-shadow: none;
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }
        
        .cart-icon {
            position: relative;
            color: #333;
            font-size: 24px;
            margin: 0 15px;
        }
        
        .cart-icon .badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #ff6b6b;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 11px;
        }
        
        .user-menu .dropdown-toggle {
            background: none;
            border: none;
            color: #333;
            font-size: 16px;
        }
        
        .user-menu .dropdown-toggle:hover {
            color: #667eea;
        }
        
        .main-nav {
            background: #f8f9fa;
            padding: 12px 0;
        }
        
        .main-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 8px 15px;
            transition: all 0.3s;
        }
        
        .main-nav .nav-link:hover {
            color: #667eea;
            background: white;
            border-radius: 5px;
        }
        
        .main-nav .nav-link.active {
            color: #667eea;
            background: white;
            border-radius: 5px;
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
                    <a href="<?= url('user/login.php') ?>"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                    <a href="<?= url('user/register.php') ?>"><i class="fas fa-user-plus"></i> Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="<?= url('index.php') ?>">
            <i class="fas fa-cake-candles"></i> <?= SITE_NAME ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Search Box -->
            <div class="search-box mx-auto my-3 my-lg-0">
                <form action="<?= url('user/pages/search.php') ?>" method="GET">
                    <input type="text" 
                           class="form-control" 
                           name="q" 
                           placeholder="Tìm kiếm sản phẩm..." 
                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
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
                        <a href="<?= url('user/login.php') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Navigation Menu -->
<div class="main-nav">
    <div class="container">
        <ul class="nav justify-content-center">
            <li class="nav-item">
                <a class="nav-link <?= !isset($_GET['category']) ? 'active' : '' ?>" 
                   href="<?= url('index.php') ?>">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            
            <?php foreach ($categories as $cat): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'active' : '' ?>" 
                       href="<?= url('user/pages/products.php?category=' . $cat['category_id']) ?>">
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <li class="nav-item">
                <a class="nav-link" href="<?= url('user/pages/about.php') ?>">
                    <i class="fas fa-info-circle"></i> Giới thiệu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= url('user/pages/contact.php') ?>">
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

<!-- Main Content Wrapper -->
<main class="content-wrapper">