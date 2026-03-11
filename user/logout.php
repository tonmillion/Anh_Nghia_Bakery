<?php
/**
 * User logout
 * File: user/logout.php
 */

require_once '../includes/init.php';

// Xóa tất cả session và logout
logout_user();

// Redirect về trang chủ với thông báo
set_flash('success', 'Đã đăng xuất thành công. Hẹn gặp lại!');
redirect(url('index.php'));
?>