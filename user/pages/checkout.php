<?php
/**
 * Checkout page
 * File: user/pages/checkout.php
 */

require_once '../../includes/init.php';

// Bắt buộc đăng nhập
require_login();

$page_title = 'Thanh toán - ' . SITE_NAME;

// Lấy thông tin giỏ hàng
$cart = new Cart();
$cart_items = $cart->getCart();
$total = $cart->getCartTotal();

// Nếu giỏ hàng rỗng, redirect về Cart
if (empty($cart_items)) {
    set_flash('error', 'Giỏ hàng của bạn đang trống');
    redirect(url('user/pages/cart.php'));
}

// Kiểm tra tồn kho tất cả sản phẩm
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock_quantity']) {
        set_flash('error', 'Sản phẩm ' . $item['product_name'] . ' không đủ số lượng');
        redirect(url('user/pages/cart.php'));
    }
}

// Lấy thông tin user
$user = new User();
$user_info = $user->getUserById(get_user_id());

$errors = [];

// Xử lý đặt hàng
if (is_method('POST')) {
    // Validate dữ liệu
    $shipping_name = sanitize($_POST['shipping_name'] ?? '');
    $shipping_phone = sanitize($_POST['shipping_phone'] ?? '');
    $shipping_address = sanitize($_POST['shipping_address'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? '');
    $customer_note = sanitize($_POST['customer_note'] ?? '');

    if (empty($shipping_name)) {
        $errors['shipping_name'] = 'Vui lòng nhập họ tên người nhận';
    }

    if (empty($shipping_phone)) {
        $errors['shipping_phone'] = 'Vui lòng nhập số điện thoại người nhận';
    } elseif (!is_valid_phone($shipping_phone)) {
        $errors['shipping_phone'] = 'Số điện thoại không hợp lệ';
    }

    if (empty($shipping_address)) {
        $errors['shipping_address'] = 'Vui lòng nhập địa chỉ người nhận';
    }

    if (!in_array($payment_method, ['COD', 'VNPAY'])) {
        $errors['payment_method'] = 'Vui lòng chọn phương thức thanh toán';
    }

    // Nếu không có lỗi thì tạo đơn hàng
    if (empty($errors)) {
        $order = new Order();

        // Chuẩn bị dữ liệu đơn hàng
        $order_data = [
            'user_id' => get_user_id(),
            'total_amount' => $total,
            'payment_method' => $payment_method,
            'shipping_name' => $shipping_name,
            'shipping_phone' => $shipping_phone,
            'shipping_address' => $shipping_address,
            'customer_note' => $customer_note,
        ];

        // Tạo đơn hàng
        $result = $order->createOrder($order_data, $cart_items);

        if ($result['success']) {
            // Xóa giỏ hàng
            $cart->clearCart();

            // Nếu thanh toán COD
            if ($payment_method === 'COD') {
                set_flash('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order_code']);
                redirect(url('user/pages/order-success.php?order=' . $result['order_code']));
            }
            // Nếu thanh toán VNPAY
            else {
                // Tạo URL thanh toán VNPay
                $vnpay = new VNPay();
                $vnpay_data = [
                    'order_code' => $result['order_code'],
                    'amount' => $total,
                    'bank_code' => $_POST['bank_code'] ?? '', // Mã ngân hàng (nếu muốn)
                ];

                $payment_url = $vnpay->createPaymentUrl($vnpay_data);

                // Redirect đến VNPay
                redirect($payment_url);
            }
        } else {
            $errors['general'] = $result['message'];
        }
    }
}

// Include header
include '../../includes/layouts/header.php';
?>

<style>
    .checkout-page {
        padding: 30px 0;
    }
    
    .checkout-header {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .checkout-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 30px 0;
    }
    
    .step {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e1e8ed;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .step.active .step-number {
        background: #667eea;
        color: white;
    }
    
    .step-arrow {
        color: #ccc;
        margin: 0 20px;
    }
    
    .checkout-form {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .payment-methods {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }
    
    .payment-option {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }
    
    .payment-option:hover {
        border-color: #667eea;
    }
    
    .payment-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .payment-option input[type="radio"]:checked ~ .payment-content {
        border-left: 4px solid #667eea;
        padding-left: 16px;
    }
    
    .payment-option input[type="radio"]:checked ~ .payment-content::before {
        content: '✓';
        position: absolute;
        right: 10px;
        top: 10px;
        width: 24px;
        height: 24px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .payment-content {
        position: relative;
        transition: all 0.3s;
    }
    
    .payment-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }
    
    .payment-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .payment-desc {
        font-size: 13px;
        color: #666;
    }
    
    .order-summary {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 20px;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #e1e8ed;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .order-item-info {
        flex: 1;
    }
    
    .order-item-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .order-item-quantity {
        font-size: 13px;
        color: #666;
    }
    
    .order-item-price {
        font-weight: 600;
        color: #ff6b6b;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 15px;
    }
    
    .summary-row.total {
        font-size: 20px;
        font-weight: bold;
        color: #ff6b6b;
        padding-top: 15px;
        border-top: 2px solid #e1e8ed;
        margin-top: 10px;
    }
    
    .btn-place-order {
        width: 100%;
        padding: 15px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-place-order:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
</style>

<div class="checkout-page">
    <div class="container">
        <!-- Header -->
        <div class="checkout-header">
            <h2><i class="fas fa-shopping-cart"></i> Thanh Toán</h2>
            
            <!-- Steps -->
            <div class="checkout-steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <span>Giỏ hàng</span>
                </div>
                <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <span>Thanh toán</span>
                </div>
                <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
                <div class="step">
                    <div class="step-number">3</div>
                    <span>Hoàn thành</span>
                </div>
            </div>
        </div>
        
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= $errors['general'] ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <!-- Form -->
                <div class="col-lg-7">
                    <div class="checkout-form">
                        <!-- Thông tin giao hàng -->
                        <h4 class="section-title">
                            <i class="fas fa-shipping-fast"></i> Thông Tin Giao Hàng
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên người nhận *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['shipping_name']) ? 'is-invalid' : '' ?>" 
                                       name="shipping_name" 
                                       value="<?= $_POST['shipping_name'] ?? $user_info['full_name'] ?>"
                                       required>
                                <?php if (isset($errors['shipping_name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['shipping_name'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại *</label>
                                <input type="tel" 
                                       class="form-control <?= isset($errors['shipping_phone']) ? 'is-invalid' : '' ?>" 
                                       name="shipping_phone" 
                                       value="<?= $_POST['shipping_phone'] ?? $user_info['phone'] ?>"
                                       required>
                                <?php if (isset($errors['shipping_phone'])): ?>
                                    <div class="invalid-feedback"><?= $errors['shipping_phone'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Địa chỉ giao hàng *</label>
                                <textarea class="form-control <?= isset($errors['shipping_address']) ? 'is-invalid' : '' ?>" 
                                          name="shipping_address" 
                                          rows="3" 
                                          required><?= $_POST['shipping_address'] ?? $user_info['address'] ?></textarea>
                                <?php if (isset($errors['shipping_address'])): ?>
                                    <div class="invalid-feedback"><?= $errors['shipping_address'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                                <textarea class="form-control" 
                                          name="customer_note" 
                                          rows="2" 
                                          placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn"><?= $_POST['customer_note'] ?? '' ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Phương thức thanh toán -->
                        <h4 class="section-title mt-4">
                            <i class="fas fa-credit-card"></i> Phương Thức Thanh Toán
                        </h4>
                        
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="COD" checked onclick="hideBankSelect()">
                                <div class="payment-content">
                                    <div class="payment-icon">💵</div>
                                    <div class="payment-name">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="payment-desc">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="VNPAY" onclick="showBankSelect()">
                                <div class="payment-content">
                                    <div class="payment-icon">💳</div>
                                    <div class="payment-name">Thanh toán qua VNPay</div>
                                    <div class="payment-desc">Thanh toán trực tuyến qua cổng VNPay</div>
                                </div>
                            </label>
                        </div>

                        <!-- Chọn ngân hàng VNPay (ẩn mặc định) -->
                        <div id="bankSelectContainer" style="display: none; margin-top: 20px;">
                            <label class="form-label">Chọn ngân hàng (Tùy chọn)</label>
                            <select class="form-select" name="bank_code" id="bank_code">
                                <?php
                                $vnpay = new VNPay();
                                $banks = $vnpay->getBankList();
                                foreach ($banks as $code => $name):
                                ?>
                                    <option value="<?= $code ?>"><?= htmlspecialchars($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Để trống để hiển thị tất cả phương thức thanh toán</small>
                        </div> 
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h4 class="section-title">
                            <i class="fas fa-list"></i> Đơn Hàng (<?= count($cart_items) ?> sản phẩm)
                        </h4>
                        
                        <!-- Items -->
                        <div class="order-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <img src="<?= upload($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                     class="order-item-image"
                                     onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                <div class="order-item-info">
                                    <div class="order-item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="order-item-quantity">Số lượng: <?= $item['quantity'] ?></div>
                                </div>
                                <div class="order-item-price">
                                    <?= format_currency($item['subtotal']) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Summary -->
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <strong><?= format_currency($total) ?></strong>
                        </div>
                        
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <strong class="text-success">Miễn phí</strong>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <strong><?= format_currency($total) ?></strong>
                        </div>
                        
                        <button type="submit" class="btn-place-order">
                            <i class="fas fa-check-circle"></i> Đặt Hàng
                        </button>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lock"></i> Thanh toán an toàn & bảo mật
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showBankSelect() {
    document.getElementById('bankSelectContainer').style.display = 'block';
}

function hideBankSelect() {
    document.getElementById('bankSelectContainer').style.display = 'none';
}
</script>

<?php include '../../includes/layouts/footer.php'; ?>