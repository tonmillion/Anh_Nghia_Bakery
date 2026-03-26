<?php
/**
 * Contacts magagement (admin)
 * File: admin/pages/contacts.php
 */

require_once '../../includes/init.php';

$page_title = 'Quản lý tin nhắn';

// Kiểm tra quyền admin
require_admin();

$db = getDB();

// Xử lý cập nhật status
if (isset($_POST['update_status'])) {
    $contact_id = (int)$_POST['contact_id'];
    $status = sanitize($_POST['status']);

    $stmt = $db->prepare("UPDATE contacts SET status = ? WHERE contact_id = ?");
    if ($stmt->execute([$status, $contact_id])) {
        set_flash('success', 'Đã cập nhật trạng thái');
    }
    redirect(url('admin/pages/contacts.php'));
}

// Xử lý thêm ghi chú
if (isset($_POST['add_note'])) {
    $contact_id = (int)$_POST['contact_id'];
    $admin_note = sanitize($_POST['admin_note']);

    $stmt = $db->prepare("UPDATE contacts SET admin_note = ?, status = 'replied' WHERE contact_id = ?");
    if ($stmt->execute([$admin_note, $contact_id])) {
        set_flash('success', 'Đã thêm ghi chú');
    }
    redirect(url('admin/pages/contacts.php'));
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $contact_id = (int)$_GET['delete'];

    $stmt = $db->prepare("DELETE FROM contacts WHERE contact_id = ?");
    if ($stmt->execute([$contact_id])) {
        set_flash('success', 'Đã xóa tin nhăn');
    }
    redirect(url('admin/pages/contacts.php'));
}

// Lấy filter status
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Đếm số lượng theo status
$stmt = $db->query("SELECT status, COUNT(*) as count FROM contacts GROUP BY status");
$status_counts = [];
while ($row = $stmt->fetch()) {
    $status_counts[$row['status']] = $row['count'];
}
$total_count = array_sum($status_counts);

// Lấy dnah sách contacts
$sql = "SELECT * FROM contacts WHERE 1=1";
$params = [];

if (!empty($filter_status)) {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll();

// Include header
include '../includes/header.php';
?>

<link rel="stylesheet" href="<?= url('admin/css/contacts.css') ?>?v=<?= time() ?>">
 
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-envelope"></i> Quản lý tin nhắn</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tin nhắn</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
 
<!-- Stats -->
<div class="stats-row">
    <a href="<?= url('admin/pages/contacts.php') ?>" 
       class="stat-card all <?= empty($filter_status) ? 'active' : '' ?>"
       style="text-decoration: none;">
        <div class="stat-icon"><i class="fas fa-inbox"></i></div>
        <div class="stat-number"><?= $total_count ?></div>
        <div class="stat-label">Tất cả</div>
    </a>
    
    <a href="<?= url('admin/pages/contacts.php?status=new') ?>" 
       class="stat-card new <?= $filter_status === 'new' ? 'active' : '' ?>"
       style="text-decoration: none;">
        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="stat-number"><?= $status_counts['new'] ?? 0 ?></div>
        <div class="stat-label">Tin mới</div>
    </a>
    
    <a href="<?= url('admin/pages/contacts.php?status=read') ?>" 
       class="stat-card read <?= $filter_status === 'read' ? 'active' : '' ?>"
       style="text-decoration: none;">
        <div class="stat-icon"><i class="fas fa-envelope-open"></i></div>
        <div class="stat-number"><?= $status_counts['read'] ?? 0 ?></div>
        <div class="stat-label">Đã đọc</div>
    </a>
    
    <a href="<?= url('admin/pages/contacts.php?status=replied') ?>" 
       class="stat-card replied <?= $filter_status === 'replied' ? 'active' : '' ?>"
       style="text-decoration: none;">
        <div class="stat-icon"><i class="fas fa-reply"></i></div>
        <div class="stat-number"><?= $status_counts['replied'] ?? 0 ?></div>
        <div class="stat-label">Đã trả lời</div>
    </a>
    
    <a href="<?= url('admin/pages/contacts.php?status=closed') ?>" 
       class="stat-card closed <?= $filter_status === 'closed' ? 'active' : '' ?>"
       style="text-decoration: none;">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-number"><?= $status_counts['closed'] ?? 0 ?></div>
        <div class="stat-label">Đã đóng</div>
    </a>
</div>
 
<!-- Contacts List -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> 
        Danh sách tin nhắn
        <?php if (!empty($filter_status)): ?>
            - Lọc: <strong><?= ucfirst($filter_status) ?></strong>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox" style="font-size: 64px; color: #e1e8ed;"></i>
                <p class="text-muted mt-3">Chưa có tin nhắn nào</p>
            </div>
        <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
            <div class="contact-item <?= $contact['status'] === 'new' ? 'unread' : '' ?>">
                <div class="contact-header">
                    <div class="contact-info">
                        <h5>
                            <i class="fas fa-user-circle"></i> 
                            <?= htmlspecialchars($contact['name']) ?>
                        </h5>
                        <div class="contact-meta">
                            <span>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                    <?= htmlspecialchars($contact['email']) ?>
                                </a>
                            </span>
                            <?php if (!empty($contact['phone'])): ?>
                            <span>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?= htmlspecialchars($contact['phone']) ?>">
                                    <?= htmlspecialchars($contact['phone']) ?>
                                </a>
                            </span>
                            <?php endif; ?>
                            <span>
                                <i class="fas fa-clock"></i>
                                <?= format_date($contact['created_at'], 'd/m/Y H:i') ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="status-badge <?= $contact['status'] ?>">
                            <?= ucfirst($contact['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="contact-subject">
                    <i class="fas fa-tag"></i> <?= htmlspecialchars($contact['subject']) ?>
                </div>
                
                <div class="contact-message">
                    <?= nl2br(htmlspecialchars($contact['message'])) ?>
                </div>
                
                <?php if (!empty($contact['admin_note'])): ?>
                <div class="admin-note-section">
                    <h6><i class="fas fa-sticky-note"></i> Ghi chú của bạn:</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($contact['admin_note'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="contact-actions">
                    <!-- Mark as Read -->
                    <?php if ($contact['status'] === 'new'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="contact_id" value="<?= $contact['contact_id'] ?>">
                        <input type="hidden" name="status" value="read">
                        <button type="submit" name="update_status" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Đánh dấu đã đọc
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <!-- Add Note -->
                    <button type="button" 
                            class="btn btn-sm btn-success" 
                            data-bs-toggle="modal" 
                            data-bs-target="#noteModal<?= $contact['contact_id'] ?>">
                        <i class="fas fa-reply"></i> Trả lời / Ghi chú
                    </button>
                    
                    <!-- Close -->
                    <?php if ($contact['status'] !== 'closed'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="contact_id" value="<?= $contact['contact_id'] ?>">
                        <input type="hidden" name="status" value="closed">
                        <button type="submit" name="update_status" class="btn btn-sm btn-secondary">
                            <i class="fas fa-check"></i> Đóng
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <!-- Delete -->
                    <a href="?delete=<?= $contact['contact_id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?')">
                        <i class="fas fa-trash"></i> Xóa
                    </a>
                </div>
            </div>
            
            <!-- Note Modal -->
            <div class="modal fade" id="noteModal<?= $contact['contact_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-reply"></i> Trả lời / Ghi chú
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="contact_id" value="<?= $contact['contact_id'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Tin nhắn từ khách hàng:</label>
                                    <div class="alert alert-info">
                                        <strong><?= htmlspecialchars($contact['subject']) ?></strong><br>
                                        <?= nl2br(htmlspecialchars($contact['message'])) ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ghi chú / Phản hồi:</label>
                                    <textarea class="form-control" 
                                              name="admin_note" 
                                              rows="5" 
                                              placeholder="Nhập ghi chú hoặc nội dung đã trả lời khách hàng..."><?= htmlspecialchars($contact['admin_note'] ?? '') ?></textarea>
                                    <small class="text-muted">
                                        Lưu ý: Ghi chú này chỉ lưu nội bộ, không gửi email tự động. 
                                        Bạn cần trả lời khách hàng qua email riêng.
                                    </small>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Email khách hàng:</strong> 
                                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                        <?= htmlspecialchars($contact['email']) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Đóng
                                </button>
                                <button type="submit" name="add_note" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu ghi chú
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>