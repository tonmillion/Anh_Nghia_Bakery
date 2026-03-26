<?php
/**
 * Edit User page
 * File: admin/pages/user-edit.php
 */

require_once '../../includes/init.php';

$page_title = 'Sửa thông tin người dùng';

$user = new User();

// Lấy user_id
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    set_flash('error', 'Người dùng không tồn tại');
    redirect(url('admin/pages/users.php'));
}

// Lấy thông tin user
$user_info = $user->getUserById($user_id);

if (!$user_info) {
    set_flash('error', 'Người dùng không tồn tại');
    redirect(url('admin/pages/users.php'));
}

$errors = [];

// Xử lý form submit
if (is_method('POST')) {
    if (isset($_POST['update_info'])) {
        // Cập nhật thông tin cơ bản
        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'role' => sanitize($_POST['role'] ?? 'user')
        ];

        // Validate
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Họ tên không được để trống';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email không được để trống';
        } elseif (!is_valid_email($data['email'])) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($data['phone']) && !is_valid_phone($data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        if (empty($errors)) {
            if ($user->updateUser($user_id, $data)) {
                set_flash ('success', 'Đã cập nhật thông tin người dùng');
                redirect(url('admin/pages/users.php'));
            } else {
                $errors['general'] = 'Có lỗi xảy ra';
            }
        }
    }
    elseif (isset($_POST['reset_password'])) {
        // Reset mật khẩu
        $new_password = sanitize($_POST['new_password'] ?? '');
        $confirm_password = sanitize($_POST['confirm_password'] ?? '');

        if (empty($new_password)) {
            $errors['new_password'] = 'Mật khẩu mới không được để trống';
        } elseif (strlen($new_password) < 6) {
            $errors['new_password'] = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        }

        if ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        if (empty($errors)) {
            if ($user->resetPassword($user_id, $new_password)) {
                set_flash ('success', 'Đã reset mật khẩu thành công');
                redirect(url('admin/pages/users.php?id=' . $user_id));
            } else {
                $errors['general'] = 'Không thể reset mật khẩu';
            }
        }
    }
}

// Include header
include '../includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-user-edit"></i> Sửa thông tin người dùng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/pages/users.php') ?>">Người dùng</a></li>
                    <li class="breadcrumb-item active">Sửa</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= url('admin/pages/users.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger">
        <?= $errors['general'] ?>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Thông tin cơ bản -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user"></i> Thông tin cơ bản
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($user_info['username']) ?>"
                                   disabled>
                            <small class="text-muted">Không thể thay đổi username</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" name="role">
                                <option value="customer" <?= $user_info['role'] === 'customer' ? 'selected' : '' ?>>
                                    Khách hàng
                                </option>
                                <option value="admin" <?= $user_info['role'] === 'admin' ? 'selected' : '' ?>>
                                    Quản trị viên
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                   name="full_name" 
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? $user_info['full_name']) ?>"
                                   required>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? $user_info['email']) ?>"
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
                                   value="<?= htmlspecialchars($_POST['phone'] ?? $user_info['phone']) ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= format_date($user_info['created_at']) ?>"
                                   disabled>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" 
                                      name="address" 
                                      rows="2"><?= htmlspecialchars($_POST['address'] ?? $user_info['address']) ?></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" name="update_info" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật thông tin
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Reset mật khẩu -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-key"></i> Reset mật khẩu
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                               name="new_password"
                               minlength="6">
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                               name="confirm_password"
                               minlength="6">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" 
                            name="reset_password" 
                            class="btn btn-warning w-100"
                            onclick="return confirm('Bạn có chắc muốn reset mật khẩu cho người dùng này?')">
                        <i class="fas fa-sync"></i> Reset mật khẩu
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Thống kê -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Thống kê
            </div>
            <div class="card-body">
                <?php
                $db = getDB();
                
                // Đếm số đơn hàng
                $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $order_count = $stmt->fetchColumn();
                
                // Tổng chi tiêu
                $stmt = $db->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND order_status = 'completed'");
                $stmt->execute([$user_id]);
                $total_spent = $stmt->fetchColumn() ?? 0;
                ?>
                
                <div class="mb-3">
                    <small class="text-muted">Tổng đơn hàng:</small>
                    <h5><?= number_format($order_count) ?></h5>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted">Tổng chi tiêu:</small>
                    <h5 class="text-danger"><?= format_currency($total_spent) ?></h5>
                </div>
                
                <a href="<?= url('admin/pages/orders.php?user=' . $user_id) ?>" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fas fa-shopping-cart"></i> Xem đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>