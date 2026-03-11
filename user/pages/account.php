<?php
/**
 * User Account Page
 * File: user/pages/account.php
 */

require_once '../../includes/init.php';

// Bắt buộc phải đăng nhập
require_login();

$user = new User();
$user_id = get_user_id();
$user_info = $user->getUserById($user_id);

// Debug kiểm tra dữ liệu
if (!$user_info) {
    set_flash('error', 'Không thể lấy thông tin tài khoản, vui lòng đăng nhập lại');
    redirect(url('user/login.php'));
    exit;
}
          
$errors = [];
$succcess = false;

// Xử lý cập nhật thông tin
if (is_method('POST') && isset($_POST['update_profile'])) {
    $data = [
        'full_name' => sanitize($_POST['full_name'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'address' => sanitize($_POST['address'] ?? '')
    ];

    // Validate
    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Họ tên không được để trống';
    }

    if (empty($data['email']) || !is_valid_email($data['email'])) {
        $errors['email'] = 'Email không hợp lệ';
    }

    if (empty($data['phone']) || !is_valid_phone($data['phone'])) {
        $errors['phone'] = 'Số điện thoại không hợp lệ';
    }

    // Nếu không có lỗi thì cập nhật
    if (empty($errors)) {
        if ($user->updateProfile($user_id, $data)) {
            set_flash('success', 'Cập nhật thông tin thành công!');
            $success = true;
            // Reload lại thông tin
            $user_info = $user->getUserById($user_id);
        } else {
            $errors['general'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }
    }
}

// Xử lý đổi mật khẩu
if (is_method('POST') && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($old_password)) {
        $errors['old_password'] = 'Vui lòng nhập mật khẩu cũ';
    }

    if (empty($new_password)) {
        $errors['new_password'] = 'Vui lòng nhập mật khẩu mới';
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
    }

    if (empty($errors)) {
        $result = $user->changePassword($user_id, $old_password, $new_password);

        if ($result['success']) {
            set_flash('success', $result['message']);
            $success = true;
        } else {
            $errors['password_general'] = $result['message'];
        }
    }
}

$page_title = 'Thông tin tài khoản'
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
            background: #f8f9fa;
        }
        .account-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .account-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 15px 50px;
        }
        .account-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .account-card h4 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .nav-pills .nav-link {
            color: #666;
        }
        .nav-pills .nav-link.active {
            background: #667eea;
        }
        .badge-role {
            background: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-edit {
            background: #667eea;
            border: none;
            color: white;
        }
        .btn-edit:hover {
            background: #5568d3;
            color: white;
        }
    </style>
</head>
<body>

<div class="account-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-user-circle"></i> Thông Tin Tài Khoản</h2>
                <?php if ($user_info): ?>
                    <p class="mb-0">Xin chào, <strong><?= htmlspecialchars($user_info['full_name']) ?></strong>!</p>
                <?php endif; ?>
            </div>
            <div>
                <a href="<?= url('index.php') ?>" class="btn btn-light">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

<div class="account-container">
    
    <?php 
    $flash = get_flash();
    if ($flash): 
    ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
            <?= $flash['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-3" id="accountTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button">
                <i class="fas fa-info-circle"></i> Thông tin cá nhân
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="password-tab" data-bs-toggle="pill" data-bs-target="#password" type="button">
                <i class="fas fa-key"></i> Đổi mật khẩu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button">
                <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="accountTabContent">
        
        <!-- Thông tin cá nhân -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="account-card">
                <h4><i class="fas fa-user"></i> Thông Tin Cá Nhân</h4>
                
                <form method="POST" action="">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger"><?= $errors['general'] ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_info['username']) ?>" disabled>
                            <small class="text-muted">Không thể thay đổi</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai trò</label>
                            <div>
                                <span class="badge-role">
                                    <?= $user_info['role'] === 'admin' ? 'Quản trị viên' : 'Khách hàng' ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên *</label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                   name="full_name" 
                                   value="<?= htmlspecialchars($user_info['full_name']) ?>"
                                   required>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user_info['email']) ?>"
                                   required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" 
                                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                   name="phone" 
                                   value="<?= htmlspecialchars($user_info['phone']) ?? '' ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày đăng ký</label>
                            <input type="text" class="form-control" value="<?= format_date($user_info['created_at']) ?>" disabled>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="2"><?= htmlspecialchars($user_info['address']) ?? '' ?></textarea>
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-edit">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                </form>
            </div>
        </div>

        <!-- Đổi mật khẩu -->
        <div class="tab-pane fade" id="password" role="tabpanel">
            <div class="account-card">
                <h4><i class="fas fa-lock"></i> Đổi Mật Khẩu</h4>
                
                <form method="POST" action="">
                    <?php if (isset($errors['password_general'])): ?>
                        <div class="alert alert-danger"><?= $errors['password_general'] ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Mật khẩu hiện tại *</label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['old_password']) ? 'is-invalid' : '' ?>" 
                                   name="old_password"
                                   required>
                            <?php if (isset($errors['old_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['old_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu mới *</label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                                   name="new_password"
                                   required>
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Ít nhất <?= PASSWORD_MIN_LENGTH ?> ký tự</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới *</label>
                            <input type="password" 
                                   class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                   name="confirm_password"
                                   required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-edit">
                        <i class="fas fa-key"></i> Đổi mật khẩu
                    </button>
                </form>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="tab-pane fade" id="orders" role="tabpanel">
            <div class="account-card">
                <h4><i class="fas fa-shopping-bag"></i> Đơn Hàng Của Tôi</h4>
                
                <?php
                // Lấy đơn hàng của user
                $order = new Order();
                $orders = $order->getOrdersByUser($user_id, 10, 0);
                
                if (empty($orders)):
                ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Bạn chưa có đơn hàng nào.
                        <a href="<?= url('user/pages/products.php') ?>">Mua sắm ngay</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><strong><?= $o['order_code'] ?></strong></td>
                                    <td><?= format_date($o['order_date']) ?></td>
                                    <td><?= format_currency($o['total_amount']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $o['order_status'] === 'completed' ? 'success' : 'warning' ?>">
                                            <?= ORDER_STATUS[$o['order_status']] ?? $o['order_status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $o['payment_status'] === 'paid' ? 'success' : 'secondary' ?>">
                                            <?= PAYMENT_STATUS[$o['payment_status']] ?? $o['payment_status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= url('user/pages/order-detail.php?id=' . $o['order_id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Logout Button -->
    <div class="text-center mt-4">
        <a href="<?= url('user/logout.php') ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>