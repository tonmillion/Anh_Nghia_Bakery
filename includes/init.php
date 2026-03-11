<?php
/**
 * Initialization File
 * File: includes/ init.php
 * Mô tả: Load tất cả config và file cần thiết
 * 
 * CÁCH SỬ DỤNG: Include file này ở đầu mỗi page PHP
 * require_once 'includes/init.php';
 */

// Định nghĩa constant để ngăn truy cập trực tiếp
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

// ========================================================
// LOAD CÁC FILE CẤU HÌNH
// ========================================================

// 1. Load config chung
require_once __DIR__ . '/../config/config.php';

// 2. Load database connection
require_once __DIR__ . '/../config/database.php';

// 3. Load helper functions
require_once __DIR__ . '/functions.php';

// 4. Start session
require_once __DIR__ . '/session.php';

// ========================================================
// AUTO LOAD CLASSES
// ========================================================
// Đã được config trong config.php bằng spl_autoload_register

// ========================================================
// ERROR HANDLING
// ========================================================

// Custom error handler (chỉ trong development)
if (ENVIRONMENT === 'development') {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        $error_message = "Error [$errno]: $errstr in $errfile on line $errline";
        error_log($error_message);

        // Hiểm thị lỗi thân thiện hơn
        if (!(error_reporting() & $errno)) {
            return false;
        }

        echo "<div style='background: #fee; border: 1px solid #fcc; padding: 10px; margin: 10px; font-family: monospace;'>";
        echo "<strong>Error:</strong> $errstr<br>";
        echo "<strong>File:</strong> $errfile<br>";
        echo "<strong>Line:</strong> $errline";
        echo "</div>";

        return true;
    });
}

// ================================================================
// TIMEZONE
// ================================================================
// Đã set trong config.php

// ================================================================
// SECURITY HEADERS
// ================================================================

// Chống clickjacjing
header('X-Frame-Options: SAMEDRIGN');

// Chống MIME type sniffing
header('X-Content-Type-Options: nosniff');

// XSS Protection
header('X-XSS-Protection: 1; mode-block');

// Content Security Policy (tùy chọn - có thể gây conflict với inline scripts)
// header("Content-Security-Policy: default-src 'self'");

// ================================================================
// CHECK SESSION TIMEOUT
// ================================================================
// Tự động check timeout khi có session
// check_session_timeout(); // Uncomment nếu muốn bắt buộc timeout

// ================================================================
// GLOBAL VARIABLES (Tùy chọn)
// ================================================================

// Tạo các instance class thường dùng (optional - có thể tạo khi cần)
// global $db, $user, $product, $category, $order;
// $db = getDB();


// Báo lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);



?>