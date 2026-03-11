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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #2c3e50;
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 20px;
            background: #1a252f;
            text-align: center;
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-size: 20px;
            transition: all 0.3s;
        }
        
        .sidebar.collapsed .sidebar-header h4 {
            font-size: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: #34495e;
            border-left: 4px solid #3498db;
            padding-left: 16px;
        }
        
        .sidebar-menu li a i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }
        
        .sidebar.collapsed .sidebar-menu li a span {
            display: none;
        }
        
        .sidebar-menu .menu-section {
            padding: 10px 20px;
            font-size: 12px;
            color: #95a5a6;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .sidebar.collapsed .menu-section {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #2c3e50;
        }
        
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .page-header .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }
        
        .stat-card.blue .stat-icon {
            background: #e3f2fd;
            color: #2196F3;
        }
        
        .stat-card.green .stat-icon {
            background: #e8f5e9;
            color: #4CAF50;
        }
        
        .stat-card.orange .stat-icon {
            background: #fff3e0;
            color: #FF9800;
        }
        
        .stat-card.red .stat-icon {
            background: #ffebee;
            color: #f44336;
        }
        
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Card */
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e1e8ed;
            padding: 20px;
            font-weight: 600;
            font-size: 18px;
        }
    </style>
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
            <a href="<?= url('admin/products.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i>
                <span>Sản phẩm</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/categories.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i>
                <span>Danh mục</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/orders.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Đơn hàng</span>
            </a>
        </li>
        <li>
            <a href="<?= url('admin/users.php') ?>" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span>Người dùng</span>
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
