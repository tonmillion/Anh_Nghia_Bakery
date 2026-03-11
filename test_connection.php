<?php
/**
 * Test file
 * File: test_connection.php
 * Mô tả: Test kết nối database và các class
 * Truy cập: http://localhost/AN_Bakery/test_connection.php
 */

require_once 'includes/init.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connection - Bakery Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        h2 {
            color: #666;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .info {
            background: #e7f3ff;
            padding: 10px;
            border-left: 4px solid #2196F3;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <h1>🧪 BAKERY SHOP - TEST CONNECTION</h1>

    <!-- TEST 1: Database Connection -->
    <div class="test-box">
        <h2>1️⃣ Test Kết Nối Database</h2>
        <?php
        try {
            $db = getDB();
            echo '<p class="success">✅ Kết nối database thành công!</p>';
            echo '<div class="info">';
            echo '<strong>Database:</strong> ' . DB_NAME . '<br>';
            echo '<strong>Host:</strong> ' . DB_HOST . '<br>';
            echo '<strong>Charset:</strong> ' . DB_CHARSET;
            echo '</div>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi kết nối: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 2: Tables -->
    <div class="test-box">
        <h2>2️⃣ Kiểm Tra Bảng Database</h2>
        <?php
        try {
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($tables) > 0) {
                echo '<p class="success">✅ Tìm thấy ' . count($tables) . ' bảng</p>';
                echo '<table>';
                echo '<tr><th>Tên Bảng</th><th>Số Dòng</th></tr>';
                
                foreach ($tables as $table) {
                    $count_stmt = $db->query("SELECT COUNT(*) FROM $table");
                    $count = $count_stmt->fetchColumn();
                    echo '<tr>';
                    echo '<td>' . $table . '</td>';
                    echo '<td>' . $count . ' rows</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">❌ Không tìm thấy bảng nào. Hãy import file bakery_db.sql</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 3: User Class -->
    <div class="test-box">
        <h2>3️⃣ Test Class User</h2>
        <?php
        try {
            $userClass = new User();
            echo '<p class="success">✅ Class User load thành công</p>';
            
            // Lấy danh sách users
            $users = $userClass->getAllUsers(5, 0);
            if (!empty($users)) {
                echo '<p>Danh sách users (5 người đầu):</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Tên</th><th>Email</th><th>Role</th></tr>';
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . $user['user_id'] . '</td>';
                    echo '<td>' . $user['username'] . '</td>';
                    echo '<td>' . $user['full_name'] . '</td>';
                    echo '<td>' . $user['email'] . '</td>';
                    echo '<td><span class="badge badge-' . ($user['role'] == 'admin' ? 'warning' : 'success') . '">' . $user['role'] . '</span></td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 4: Category Class -->
    <div class="test-box">
        <h2>4️⃣ Test Class Category</h2>
        <?php
        try {
            $categoryClass = new Category();
            echo '<p class="success">✅ Class Category load thành công</p>';
            
            $categories = $categoryClass->getCategoriesWithCount();
            if (!empty($categories)) {
                echo '<p>Danh sách danh mục:</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Tên Danh Mục</th><th>Số Sản Phẩm</th></tr>';
                foreach ($categories as $cat) {
                    echo '<tr>';
                    echo '<td>' . $cat['category_id'] . '</td>';
                    echo '<td>' . $cat['category_name'] . '</td>';
                    echo '<td>' . $cat['product_count'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 5: Product Class -->
    <div class="test-box">
        <h2>5️⃣ Test Class Product</h2>
        <?php
        try {
            $productClass = new Product();
            echo '<p class="success">✅ Class Product load thành công</p>';
            
            $products = $productClass->getLatestProducts(5);
            if (!empty($products)) {
                echo '<p>5 sản phẩm mới nhất:</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Tên Sản Phẩm</th><th>Danh Mục</th><th>Giá</th><th>Tồn Kho</th></tr>';
                foreach ($products as $product) {
                    echo '<tr>';
                    echo '<td>' . $product['product_id'] . '</td>';
                    echo '<td>' . $product['product_name'] . '</td>';
                    echo '<td>' . $product['category_name'] . '</td>';
                    echo '<td>' . format_currency($product['price']) . '</td>';
                    echo '<td>' . $product['stock_quantity'] . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 6: Order Class -->
    <div class="test-box">
        <h2>6️⃣ Test Class Order</h2>
        <?php
        try {
            $orderClass = new Order();
            echo '<p class="success">✅ Class Order load thành công</p>';
            
            $orders = $orderClass->getAllOrders([], 5, 0);
            if (!empty($orders)) {
                echo '<p>5 đơn hàng gần nhất:</p>';
                echo '<table>';
                echo '<tr><th>Mã ĐH</th><th>Khách Hàng</th><th>Tổng Tiền</th><th>Trạng Thái</th><th>Ngày Đặt</th></tr>';
                foreach ($orders as $order) {
                    echo '<tr>';
                    echo '<td>' . $order['order_code'] . '</td>';
                    echo '<td>' . $order['full_name'] . '</td>';
                    echo '<td>' . format_currency($order['total_amount']) . '</td>';
                    echo '<td>' . ORDER_STATUS[$order['order_status']] . '</td>';
                    echo '<td>' . format_date($order['order_date']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 7: Cart Class -->
    <div class="test-box">
        <h2>7️⃣ Test Class Cart</h2>
        <?php
        try {
            $cartClass = new Cart();
            echo '<p class="success">✅ Class Cart load thành công</p>';
            
            // Test các method
            echo '<table>';
            echo '<tr><th>Method</th><th>Result</th></tr>';
            echo '<tr><td>getCartCount()</td><td>' . $cartClass->getCartCount() . '</td></tr>';
            echo '<tr><td>getCartTotal()</td><td>' . format_currency($cartClass->getCartTotal()) . '</td></tr>';
            echo '</table>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Lỗi: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <!-- TEST 8: Helper Functions -->
    <div class="test-box">
        <h2>8️⃣ Test Helper Functions</h2>
        <?php
        echo '<p class="success">✅ Test các hàm tiện ích</p>';
        echo '<table>';
        
        echo '<tr><td>format_currency(350000)</td><td>' . format_currency(350000) . '</td></tr>';
        echo '<tr><td>format_date(NOW)</td><td>' . format_date(date('Y-m-d H:i:s')) . '</td></tr>';
        echo '<tr><td>excerpt("Đây là test...")</td><td>' . excerpt('Đây là một đoạn text rất dài để test hàm excerpt...', 30) . '</td></tr>';
        echo '<tr><td>create_slug("Bánh Kem")</td><td>' . create_slug('Bánh Kem Dâu Tây') . '</td></tr>';
        echo '<tr><td>url("user/login.php")</td><td>' . url('user/login.php') . '</td></tr>';
        echo '<tr><td>is_valid_email("test@gmail.com")</td><td>' . (is_valid_email('test@gmail.com') ? '✅ Valid' : '❌ Invalid') . '</td></tr>';
        echo '<tr><td>is_valid_phone("0912345678")</td><td>' . (is_valid_phone('0912345678') ? '✅ Valid' : '❌ Invalid') . '</td></tr>';
        
        echo '</table>';
        ?>
    </div>

    <!-- TEST 9: Configuration -->
    <div class="test-box">
        <h2>9️⃣ Thông Tin Cấu Hình</h2>
        <table>
            <tr><th>Cấu Hình</th><th>Giá Trị</th></tr>
            <tr><td>SITE_NAME</td><td><?= SITE_NAME ?></td></tr>
            <tr><td>BASE_URL</td><td><?= BASE_URL ?></td></tr>
            <tr><td>ENVIRONMENT</td><td><span class="badge badge-<?= ENVIRONMENT === 'development' ? 'warning' : 'success' ?>"><?= ENVIRONMENT ?></span></td></tr>
            <tr><td>ITEMS_PER_PAGE</td><td><?= ITEMS_PER_PAGE ?></td></tr>
            <tr><td>SESSION_LIFETIME</td><td><?= SESSION_LIFETIME ?> giây (<?= SESSION_LIFETIME / 3600 ?> giờ)</td></tr>
            <tr><td>MAX_UPLOAD_SIZE</td><td><?= MAX_UPLOAD_SIZE / 1024 / 1024 ?> MB</td></tr>
            <tr><td>PHP Version</td><td><?= phpversion() ?></td></tr>
            <tr><td>Server</td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td></tr>
        </table>
    </div>

    <div class="test-box" style="text-align: center; background: #28a745; color: white;">
        <h2>✅ TẤT CẢ TEST ĐÃ HOÀN THÀNH!</h2>
        <p>Hệ thống đã sẵn sàng để phát triển tiếp.</p>
        <p><strong>Tiếp theo:</strong> Bắt đầu xây dựng giao diện User và Admin</p>
    </div>

</body>
</html>