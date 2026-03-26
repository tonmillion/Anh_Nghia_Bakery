<?php
/**
 * Users management
 * File: admin/pages/users.php
 */

require_once '../../includes/init.php';

$page_title = 'Quản lý người dùng';

$user = new User();

// Xử lý xóa user
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];

    // Không cho xóa chính mình
    if ($user_id === get_user_id()) {
        set_flash('error', 'Không thể xóa tài khoản của chính mình');
    } else {
        if ($user->deleteUser($user_id)) {
            set_flash('success', 'Đã xóa người dùng thành công');
        } else {
            set_flash('error', 'Không thể xóa người dùng');
        }
    }
    redirect(url('admin/pages/users.php'));
}

// Xử lý kích hoạt/ vô hiệu hóa
if (isset($_GET['toggle'])) {
    $user_id = (int)$_GET['toggle'];
    $db = getDB();

    // Lấy trạng thái hiện tại
    $stmt = $db->prepare("SELECT is_active FROM users WHERE user_id = ?");
    $stmt -> execute([$user_id]);
    $current = $stmt->fetch();

    // Toggle
    $new_status = $current['is_active'] ? 0 : 1;
    $stmt = $db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");

    if ($stmt->execute([$new_status, $user_id])) {
        set_flash('success', $new_status ? 'Đã kích hoạt tài khoản' : 'Đã vô hiệu hóa tài khoản');
    }
    redirect(url('admin/pages/users.php'));
}

// Lấy danh sách người dùng
$users = $user->getAllUsers(100, 0);

// Include header
include '../includes/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-users"></i> Quản lý người dùng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Người dùng</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Danh sách người dùng (<?= count($users) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['user_id'] ?></td>
                        <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge rounded-pill bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-primary">Customer</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge rounded-pill bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-secondary">Vô hiệu</span>
                            <?php endif; ?>
                        </td>
                        <td><?= format_date($u['created_at'], 'd/m/Y') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/pages/user-edit.php?id=' . $u['user_id']) ?>" 
                                   class="btn btn-info" 
                                   title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($u['user_id'] != get_user_id()): ?>
                                    <a href="?toggle=<?= $u['user_id'] ?>" 
                                       class="btn btn-<?= $u['is_active'] ? 'warning' : 'success' ?>"
                                       title="<?= $u['is_active'] ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                        <i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i>
                                    </a>
                                    
                                    <a href="?delete=<?= $u['user_id'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirmDelete('Bạn có chắc muốn xóa người dùng này?')"
                                       title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled title="Tài khoản của bạn">
                                        <i class="fas fa-user"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>