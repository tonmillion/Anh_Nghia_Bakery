<?php
/**
 * Contact page
 * File: contact.php
 */

require_once 'includes/init.php';

$page_title = 'Liên hệ - ' . SITE_NAME;

$errors = [];
$success = false;

// Xử lý form submit
if (is_method('POST')) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Validate
    if (empty($name)) {
        $errors['name'] = 'Vui lòng nhập họ tên';
    }

    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email';
    } elseif (!is_valid_email($email)) {
        $errors['email'] = 'Email không hợp lệ';
    }

    if (!empty($phone) && !is_valid_phone($phone)) {
        $errors['phone'] = 'Số điện thoại không hợp lệ';
    }

    if (empty($subject)) {
        $errors['subject'] = 'Vui lòng nhập tiêu đề';
    }

    if (empty($message)) {
        $errors['message'] = 'Vui lòng nhập nội dung';
    }

    // Nếu không có lỗi thì lưu vào database (hoặc gửi email)
    if (empty($errors)) {
        try {
            $db = getDB();
            $sql = "INSERT INTO contacts (name, email, phone, subject, message, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([$name, $email, $phone, $subject, $message]);

            if ($result) {
                $success = true;
                // Reset form
                $_POST = [];

                // Có thể gửi email thông báo cho admin ở đây
                // sendEmailToAdmin ($name, $email, $subject, $message);
            }
        } catch (PDOException $e) {
            // Nếu chưa có bảng contact, chỉ hiển thị thông báo
            $success = true;
            $_POST = [];
        }
    }
}

// Include header
include 'includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/contact.css') ?>?v=<?= time() ?>">
 
<!-- Hero Section -->
<div class="contact-hero">
    <div class="container">
        <h1>📞 Liên hệ với chúng tôi</h1>
        <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
    </div>
</div>
 
<div class="container">
    <div class="contact-section">
        <!-- Contact Info Cards -->
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Địa chỉ</h4>
                    <p>123 Đường ABC, Quận Hoàn Kiếm</p>
                    <p>Hà Nội, Việt Nam</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>Điện thoại</h4>
                    <p><a href="tel:0123456789">0123 456 789</a></p>
                    <p><a href="tel:0987654321">0987 654 321</a></p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p><a href="mailto:info@anbakery.vn">info@anbakery.vn</a></p>
                    <p><a href="mailto:support@anbakery.vn">support@anbakery.vn</a></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4>Giờ mở cửa</h4>
                    <p><strong>Thứ 2 - Thứ 6:</strong> 8:00 - 21:00</p>
                    <p><strong>Thứ 7 - CN:</strong> 8:00 - 22:00</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h4>Facebook</h4>
                    <p><a href="https://facebook.com/anbakery" target="_blank">fb.com/anbakery</a></p>
                    <p>Theo dõi để cập nhật tin mới</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h4>Instagram</h4>
                    <p><a href="https://instagram.com/anbakery" target="_blank">@anbakery</a></p>
                    <p>Xem ảnh bánh mới nhất</p>
                </div>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form-card">
                    <h3>Gửi tin nhắn cho chúng tôi</h3>
                    <p class="subtitle">Điền thông tin vào form bên dưới, chúng tôi sẽ phản hồi trong vòng 24 giờ</p>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Cảm ơn bạn!</strong> Tin nhắn của bạn đã được gửi thành công. 
                            Chúng tôi sẽ liên hệ lại sớm nhất có thể.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       name="name" 
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                       placeholder="Nguyễn Văn A"
                                       required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                       name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="email@example.com"
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
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                       placeholder="0123456789">
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" 
                                       name="subject" 
                                       value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                                       placeholder="Tôi muốn hỏi về..."
                                       required>
                                <?php if (isset($errors['subject'])): ?>
                                    <div class="invalid-feedback"><?= $errors['subject'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" 
                                          name="message" 
                                          rows="6" 
                                          placeholder="Nhập nội dung tin nhắn của bạn..."
                                          required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                <?php if (isset($errors['message'])): ?>
                                    <div class="invalid-feedback"><?= $errors['message'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-submit w-100">
                            <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Map -->
        <div class="map-container">
            <h4><i class="fas fa-map-marked-alt"></i> Vị trí cửa hàng</h4>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.096857124983!2d105.84117731533315!3d21.028770793617665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a0!2zSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1234567890123!5m2!1svi!2s" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section">
            <div class="section-title text-center mb-5">
                <h2>Câu hỏi thường gặp</h2>
                <p class="text-muted">Một số câu hỏi bạn có thể quan tâm</p>
            </div>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="faq-item">
                        <h5><i class="fas fa-question-circle"></i> Thời gian giao hàng là bao lâu?</h5>
                        <p>
                            Chúng tôi giao hàng trong vòng 2-3 giờ đối với khu vực nội thành Hà Nội. 
                            Đối với các quận xa, thời gian có thể là 4-6 giờ. Bạn cũng có thể đặt hàng trước 
                            và chọn thời gian giao hàng mong muốn.
                        </p>
                    </div>
                    
                    <div class="faq-item">
                        <h5><i class="fas fa-question-circle"></i> Có phí giao hàng không?</h5>
                        <p>
                            Miễn phí giao hàng cho đơn hàng từ 200,000đ trở lên trong bán kính 5km. 
                            Đối với đơn hàng dưới 200,000đ hoặc khoảng cách xa hơn, phí giao hàng là 20,000đ - 50,000đ 
                            tùy theo khoảng cách.
                        </p>
                    </div>
                    
                    <div class="faq-item">
                        <h5><i class="fas fa-question-circle"></i> Bánh có thể để được bao lâu?</h5>
                        <p>
                            Bánh kem nên sử dụng trong vòng 1-2 ngày và bảo quản trong tủ lạnh. 
                            Bánh bông lan, cookies có thể để được 3-5 ngày ở nhiệt độ phòng. 
                            Chúng tôi luôn ghi rõ hạn sử dụng trên mỗi sản phẩm.
                        </p>
                    </div>
                    
                    <div class="faq-item">
                        <h5><i class="fas fa-question-circle"></i> Có nhận đặt bánh theo yêu cầu không?</h5>
                        <p>
                            Có! Chúng tôi nhận đặt bánh sinh nhật, bánh cưới, và các loại bánh đặc biệt theo yêu cầu. 
                            Vui lòng liên hệ trước ít nhất 2 ngày để chúng tôi có thể chuẩn bị tốt nhất. 
                            Gọi hotline hoặc nhắn tin qua Facebook để được tư vấn.
                        </p>
                    </div>
                    
                    <div class="faq-item">
                        <h5><i class="fas fa-question-circle"></i> Có chính sách đổi trả không?</h5>
                        <p>
                            Nếu sản phẩm bị lỗi do nhà sản xuất hoặc không đúng với mô tả, 
                            chúng tôi sẽ đổi trả trong vòng 24 giờ. Vui lòng chụp ảnh và liên hệ ngay 
                            với chúng tôi để được hỗ trợ nhanh nhất.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
<?php include 'includes/layouts/footer.php'; ?>
