<?php
/**
 * Order cancel page (user)
 * File: user/pages/order-cancel.php
 */

require_once '../../includes/init.php';

// Bắt buộc đăng nhập
require_login();

// Lấy order_id từ URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    set_flash('error', 'Đơn hàng không tồn tại');
    redirect('user/pages/orders.php');
}

$order = new Order();

// lấy thông tin đơn hàng
$order_info = $order->getOrderById($order_id);

// Kiểm tra đơn hàng có tồn tại và thuộc về user này không
if (!$order_info || $order_info['user_id'] != get_user_id()) {
    set_flash('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập');
    redirect(url('user/pages/orders.php'));
}

// Kiểm tra trạng thái đơn hàng
// Chỉ cho phép hủy đơn ở trạng thái pending hoặc processing
$allowed_statues = ['pending', 'processing'];
if (!in_array($order_info['order_status'], $allowed_statues)) {
    set_flash('error', 'Đơn hàng không thể hủy ở trạng thái hiện tại');
    redirect(url('user/pages/orders.php'));
}

$errors = [];

// Xử lý hủy đơn hàng
if (is_method('POST')) {
    $cancel_reason = sanitize($_POST['cancel_reason'] ?? '');

    // Validate lý do hủy
    if (empty($cancel_reason)) {
        $errors['cancel_reason'] = 'Vui lòng chọn lý do hủy đơn';
    }

    $other_reason = '';
    if ($cancel_reason === 'Khác') {
        $order_reason = sanitize($_POST['order_reason'] ?? '');
        if (empty($order_reason)) {
            $error['other_reason'] = 'Vui lòng nhập lý do cụ thể';
        }
    }

    if (empty($errors)) {
        // Tạo ghi chú hủy đơn
        $note = $cancel_reason;
        if ($cancel_reason === 'Khác' && !empty($other_reason)) {
            $note = 'Khác: ' . $order_reason;
        }

        // Hủy đơn hàng
        $result = $order->cancelOrder($order_id, 'Khách hàng hủy - ' . $note);

        if($result) {
            set_flash('success', 'Đơn hàng đã được hủy thành công, số lượng sản phẩm đã được hoàn trả vào kho');
            redirect(url('user/pages/orders.php'));
        } else {
            $errors['general'] = 'Có lỗi xảy ra khi hủy đơn hàng, vui lòng thử lại';
        }
    }
}

$page_title = 'Hủy đơn hàng - ' . SITE_NAME;

// Include headere
include '../../includes/layouts/header.php';
?>

<style>
    .cancel-page {
        padding: 30px 0;
        min-height: 60vh;
    }
    
    .cancel-warning {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .cancel-warning i {
        font-size: 48px;
        color: #ff9800;
        margin-bottom: 15px;
    }
    
    .order-info-box {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e1e8ed;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .cancel-form {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .reason-option {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .reason-option:hover {
        border-color: #667eea;
        background: #f8f9fa;
    }
    
    .reason-option input[type="radio"] {
        margin-right: 10px;
    }
    
    .reason-option.selected {
        border-color: #667eea;
        background: #f0f4ff;
    }
    
    .other-reason-input {
        display: none;
        margin-top: 15px;
    }
    
    .other-reason-input.show {
        display: block;
    }
</style>

<div class="cancel-page">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Warning -->
                <div class="cancel-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Lưu ý khi hủy đơn hàng</h5>
                    <p class="mb-0">
                        Sau khi hủy, đơn hàng sẽ không thể khôi phục. 
                        Số lượng sản phẩm sẽ được hoàn trả vào kho.
                    </p>
                </div>
                
                <!-- Order Info -->
                <div class="order-info-box">
                    <h5><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
                    
                    <div class="info-row">
                        <span class="text-muted">Mã đơn hàng:</span>
                        <strong><?= htmlspecialchars($order_info['order_code']) ?></strong>
                    </div>
                    
                    <div class="info-row">
                        <span class="text-muted">Ngày đặt:</span>
                        <span><?= format_date($order_info['order_date']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="text-muted">Trạng thái:</span>
                        <span class="badge bg-warning">
                            <?= ORDER_STATUS[$order_info['order_status']] ?? $order_info['order_status'] ?>
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="text-muted">Tổng tiền:</span>
                        <strong class="text-danger"><?= format_currency($order_info['total_amount']) ?></strong>
                    </div>
                    
                    <div class="info-row">
                        <span class="text-muted">Thanh toán:</span>
                        <span><?= PAYMENT_METHODS[$order_info['payment_method']] ?? $order_info['payment_method'] ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="cancel-form">
                    <h3 class="mb-4">
                        <i class="fas fa-ban"></i> Hủy đơn hàng
                    </h3>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= $errors['general'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="cancelForm">
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>Vui lòng chọn lý do hủy đơn hàng</strong>
                                <span class="text-danger">*</span>
                            </label>
                            
                            <?php
                            $reasons = [
                                'Tôi muốn thay đổi địa chỉ giao hàng',
                                'Tôi tìm được giá rẻ hơn ở nơi khác',
                                'Tôi đổi ý không muốn mua nữa',
                                'Thời gian giao hàng quá lâu',
                                'Tôi đặt nhầm sản phẩm',
                                'Khác'
                            ];
                            
                            foreach ($reasons as $reason):
                            ?>
                                <label class="reason-option" onclick="selectReason(this, '<?= $reason ?>')">
                                    <input type="radio" 
                                           name="cancel_reason" 
                                           value="<?= $reason ?>" 
                                           <?= (isset($_POST['cancel_reason']) && $_POST['cancel_reason'] === $reason) ? 'checked' : '' ?>>
                                    <span><?= $reason ?></span>
                                </label>
                            <?php endforeach; ?>
                            
                            <?php if (isset($errors['cancel_reason'])): ?>
                                <div class="text-danger mt-2"><?= $errors['cancel_reason'] ?></div>
                            <?php endif; ?>
                            
                            <!-- Other reason textarea -->
                            <div class="other-reason-input <?= (isset($_POST['cancel_reason']) && $_POST['cancel_reason'] === 'Khác') ? 'show' : '' ?>" 
                                 id="otherReasonInput">
                                <label class="form-label">Vui lòng nhập lý do cụ thể:</label>
                                <textarea class="form-control <?= isset($errors['other_reason']) ? 'is-invalid' : '' ?>" 
                                          name="other_reason" 
                                          rows="3"
                                          placeholder="Nhập lý do..."><?= $_POST['other_reason'] ?? '' ?></textarea>
                                <?php if (isset($errors['other_reason'])): ?>
                                    <div class="invalid-feedback"><?= $errors['other_reason'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Lưu ý:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Đơn hàng sẽ bị hủy ngay lập tức</li>
                                <li>Sản phẩm sẽ được hoàn trả vào kho</li>
                                <li>Nếu đã thanh toán, số tiền sẽ được hoàn lại trong 3-5 ngày làm việc</li>
                                <li>Bạn có thể đặt hàng lại bất cứ lúc nào</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="submit" 
                                    class="btn btn-danger btn-lg"
                                    onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                <i class="fas fa-ban"></i> Xác nhận hủy đơn
                            </button>
                            
                            <a href="<?= url('user/pages/orders.php') ?>" 
                               class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectReason(element, reason) {
    // Remove selected class from all
    document.querySelectorAll('.reason-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selected class to clicked
    element.classList.add('selected');
    
    // Show/hide other reason input
    const otherInput = document.getElementById('otherReasonInput');
    if (reason === 'Khác') {
        otherInput.classList.add('show');
    } else {
        otherInput.classList.remove('show');
    }
}

// Auto select if already checked
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="cancel_reason"]:checked');
    if (checkedRadio) {
        const label = checkedRadio.closest('.reason-option');
        label.classList.add('selected');
    }
});
</script>

<?php include '../../includes/layouts/footer.php'; ?>