</main>
<!-- End Main Content Wrapper -->

<!-- Footer -->
<footer class="footer mt-5">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <!-- About -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">
                        <i class="fas fa-cake-candles"></i> <?= SITE_NAME ?>
                    </h5>
                    <p class="footer-text">
                        Chuyên cung cấp các loại bánh ngọt cao cấp, tươi ngon mỗi ngày. 
                        Cam kết chất lượng và an toàn thực phẩm.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/profile.php?id=61587075875189"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Liên kết nhanh</h5>
                    <ul class="footer-links">
                        <li><a href="<?= url('index.php') ?>">Trang chủ</a></li>
                        <li><a href="<?= url('user/pages/products.php') ?>">Sản phẩm</a></li>
                        <li><a href="<?= url('user/pages/about.php') ?>">Giới thiệu</a></li>
                        <li><a href="<?= url('user/pages/contact.php') ?>">Liên hệ</a></li>
                        <li><a href="<?= url('user/pages/cart.php') ?>">Giỏ hàng</a></li>
                    </ul>
                </div>

                <!-- Customer Support -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Hỗ trợ khách hàng</h5>
                    <ul class="footer-links">
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Hướng dẫn đặt hàng</a></li>
                        <li><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="footer-title">Thông tin liên hệ</h5>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            34 Bằng Ca, Lý Quốc, Cao Bằng
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            Hotline: 0376 473 470
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            Email: anhnghiabakery@gmail.com
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            7:00 - 19:00 (Hàng ngày)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">
                        Thiết kế bởi <a href="#" class="text-white">Your Team</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
    /* Footer Styles */
    .footer {
        background: #2c3e50;
        color: #ecf0f1;
    }
    
    .footer-top {
        padding: 50px 0 30px;
    }
    
    .footer-title {
        color: white;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .footer-text {
        font-size: 14px;
        line-height: 1.8;
        color: #bdc3c7;
    }
    
    .social-links {
        margin-top: 15px;
    }
    
    .social-links a {
        display: inline-block;
        width: 35px;
        height: 35px;
        line-height: 35px;
        text-align: center;
        background: #34495e;
        color: white;
        border-radius: 50%;
        margin-right: 10px;
        transition: all 0.3s;
    }
    
    .social-links a:hover {
        background: #667eea;
        transform: translateY(-3px);
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
    }
    
    .footer-links li {
        margin-bottom: 10px;
    }
    
    .footer-links a {
        color: #bdc3c7;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .footer-links a:hover {
        color: white;
        padding-left: 5px;
    }
    
    .footer-contact {
        list-style: none;
        padding: 0;
    }
    
    .footer-contact li {
        margin-bottom: 15px;
        font-size: 14px;
        color: #bdc3c7;
    }
    
    .footer-contact i {
        width: 25px;
        color: #667eea;
    }
    
    .footer-bottom {
        background: #1a252f;
        padding: 20px 0;
        border-top: 1px solid #34495e;
    }
    
    .footer-bottom p {
        font-size: 14px;
        color: #bdc3c7;
    }
    
    .footer-bottom a {
        color: white;
        text-decoration: none;
    }
    
    .footer-bottom a:hover {
        text-decoration: underline;
    }
    
    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 45px;
        height: 45px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        z-index: 999;
    }
    
    .back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    .back-to-top:hover {
        background: #5568d3;
        transform: translateY(-5px);
    }
    
    /* Content Wrapper */
    .content-wrapper {
        min-height: calc(100vh - 400px);
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
// Back to Top Button
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTop.classList.add('show');
    } else {
        backToTop.classList.remove('show');
    }
});

backToTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Auto dismiss alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }, 5000);
});
</script>

</body>
</html>