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
                        Thiết kế bởi <a href="#" class="text-white">Tồn cùng nhiều công cụ =))</a>
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
    /* Bakery Theme Footer Styles */
    .footer {
        background: #D5A070; /* Warm tan/brown */
        color: #5E3A21;
        position: relative;
        margin-top: 100px;
    }
    
    /* Top Wave for Footer */
    .footer::before {
        content: "";
        position: absolute;
        top: -49px;
        left: 0;
        width: 100%;
        height: 50px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' fill='%23D5A070' opacity='.8'/%3E%3Cpath d='M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-23.84V0Z' fill='%23D5A070'/%3E%3C/svg%3E");
        background-size: cover;
        background-repeat: no-repeat;
        transform: rotate(180deg);
    }
    
    .footer-top {
        padding: 50px 0 30px;
    }
    
    .footer-title {
        color: #5E3A21;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(94, 58, 33, 0.2);
    }
    
    .footer-text {
        font-size: 14px;
        line-height: 1.8;
        color: #5E3A21;
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
        background: #5E3A21;
        color: white;
        border-radius: 50%;
        margin-right: 10px;
        transition: all 0.3s;
    }
    
    .social-links a:hover {
        background: white;
        color: #5E3A21;
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
        color: #5E3A21;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
        font-weight: 500;
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
        color: #5E3A21;
        font-weight: 500;
    }
    
    .footer-contact i {
        width: 25px;
        color: #5E3A21;
    }
    
    .footer-bottom {
        background: rgba(94, 58, 33, 0.1);
        padding: 20px 0;
        border-top: 1px solid rgba(94, 58, 33, 0.2);
    }
    
    .footer-bottom p {
        font-size: 14px;
        color: #5E3A21;
        font-weight: bold;
    }
    
    .footer-bottom a {
        color: #5E3A21;
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
        background: #F39C12;
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        z-index: 999;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    
    .back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    .back-to-top:hover {
        background: #5E3A21;
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

// Add to cart AJAX
document.querySelectorAll('form[action*="cart-add.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('ajax', '1');
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (data.success) {
                // Show a mini toast
                const toastDiv = document.createElement('div');
                toastDiv.className = 'alert alert-success position-fixed bottom-0 end-0 m-3 shadow-lg';
                toastDiv.style.zIndex = '9999';
                toastDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                document.body.appendChild(toastDiv);
                setTimeout(() => toastDiv.remove(), 3000);
                
                // Update badge
                if (data.cart_count > 0) {
                    let badge = document.querySelector('.cart-badge');
                    if (badge) {
                        badge.textContent = data.cart_count;
                    } else {
                        const cartIcon = document.querySelector('.cart-icon');
                        if (cartIcon) {
                            cartIcon.innerHTML += '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">' + data.cart_count + '</span>';
                        }
                    }
                }
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
});
</script>

</body>
</html>