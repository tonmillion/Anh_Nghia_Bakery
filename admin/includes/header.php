<?php
/**
 * Admin header
 * File: admin/includes/header.php
 */

 // Bắt buộc phải là admin
 require_admin();

 // Lấy thông tin admin
 $current_user = get_logged_in_user();
 ?>

 <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?> - <?= SITE_NAME ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS (Optional) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <link rel="stylesheet" href="<?= url('admin/css/header.css') ?>?v=<?= time() ?>">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-cake-candles"></i> Admin Panel</h4>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-section">Main</li>
        <li>
            <a href="<?= url('admin/index.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="menu-section">Quản lý</li>
        <li>
            <a href="<?= url('admin/pages/products.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i>
                <span>Sản phẩm</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/pages/categories.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i>
                <span>Danh mục</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/pages/orders.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Đơn hàng</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/pages/users.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Người dùng</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/pages/contacts.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i>
                <span>Tin nhắn</span>
                <?php
                // Hiển thị badge số tin nhắn mới
                $db = getDB();
                $stmt = $db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'");
                $new_count = (int)$stmt->fetchColumn();
                if ($new_count > 0):
                ?>
                <span class="badge bg-danger rounded-pill ms-auto"><?= $new_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li class="menu-section">Khác</li>
        <li>
            <a href="<?= url('index.php') ?>" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Xem website</span>
            </a>
        </li>
        <li>
            <a href="<?= url('user/logout.php') ?>">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="top-bar-right">
            <div class="current-time" id="currentTime" style="margin-right: 20px; font-size: 14px; color: #7f8c8d;">
                <i class="fas fa-clock"></i> <span id="timeDisplay"></span>
            </div>
            
            <div class="admin-info">
                <div class="admin-avatar">
                    <?= strtoupper(substr($current_user['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">
                        <?= htmlspecialchars($current_user['full_name']) ?>
                    </div>
                    <div style="font-size: 12px; color: #7f8c8d;">
                        Administrator
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Content Area -->
    <div class="content-area">
        <?php 
        $flash = get_flash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
                <?= $flash['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
