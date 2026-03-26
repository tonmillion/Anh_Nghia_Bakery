<?php
/**
 * About us page
 * File: about.php
 */

require_once 'includes/init.php';

$page_title = 'Giới thiệu - ' . SITE_NAME;

// Include header
include 'includes/layouts/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/about.css') ?>?v=<?= time() ?>">
 
<!-- Hero Section -->
<div class="about-hero">
    <div class="container">
        <h1>🧁 Về chúng tôi</h1>
        <p>
            Chúng tôi là <?= SITE_NAME ?> - nơi mang đến những chiếc bánh ngọt tươi ngon, 
            được làm từ tình yêu và đam mê với nghề làm bánh
        </p>
    </div>
</div>
 
<div class="container">
    <!-- Story Section -->
    <div class="about-section">
        <div class="section-title">
            <h2>Câu chuyện của chúng tôi</h2>
            <p>Hành trình từ niềm đam mê đến thương hiệu</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="story-card">
                    <h3><i class="fas fa-seedling"></i> Khởi nguồn</h3>
                    <p>
                        <?= SITE_NAME ?> được thành lập vào năm 2018 từ niềm đam mê làm bánh của đầu bếp 
                        Nguyễn Văn An. Bắt đầu từ một tiệm bánh nhỏ với chỉ 2 nhân viên, chúng tôi 
                        đã không ngừng phát triển nhờ vào chất lượng sản phẩm và sự tận tâm phục vụ.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="story-card">
                    <h3><i class="fas fa-heart"></i> Tầm nhìn</h3>
                    <p>
                        Chúng tôi mong muốn trở thành thương hiệu bánh ngọt hàng đầu tại Việt Nam, 
                        mang đến những sản phẩm chất lượng cao với giá cả hợp lý. Mỗi chiếc bánh 
                        không chỉ là món ăn mà còn là những kỷ niệm ngọt ngào cho khách hàng.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="story-card">
                    <h3><i class="fas fa-star"></i> Sứ mệnh</h3>
                    <p>
                        Sứ mệnh của <?= SITE_NAME ?> là tạo ra những sản phẩm bánh ngọt tươi ngon nhất, 
                        được làm từ nguyên liệu chất lượng cao và công thức độc quyền. Chúng tôi cam kết 
                        mang đến niềm vui và sự hài lòng cho mọi khách hàng thông qua từng sản phẩm, 
                        từng dịch vụ.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number" data-target="6">0</div>
                        <div class="stat-label">Năm kinh nghiệm</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number" data-target="50">0</div>
                        <div class="stat-label">Loại bánh</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number" data-target="10000">0</div>
                        <div class="stat-label">Khách hàng hài lòng</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item">
                        <div class="stat-number" data-target="5">0</div>
                        <div class="stat-label">Chi nhánh</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Values Section -->
    <div class="about-section">
        <div class="section-title">
            <h2>Giá trị cốt lõi</h2>
            <p>Những giá trị mà chúng tôi luôn theo đuổi</p>
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4>Chất lượng</h4>
                    <p>
                        Sử dụng 100% nguyên liệu tươi ngon, 
                        không chất bảo quản
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4>Tận tâm</h4>
                    <p>
                        Phục vụ khách hàng bằng cả trái tim, 
                        luôn lắng nghe và cải thiện
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4>Sáng tạo</h4>
                    <p>
                        Không ngừng đổi mới, 
                        tạo ra những sản phẩm độc đáo
                    </p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Uy tín</h4>
                    <p>
                        Minh bạch, trung thực 
                        trong mọi giao dịch
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Timeline Section -->
    <div class="about-section">
        <div class="section-title">
            <h2>Hành trình phát triển</h2>
            <p>Những mốc son đáng nhớ</p>
        </div>
        
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">2018</div>
                <div class="timeline-content">
                    <h5>Thành lập</h5>
                    <p>Cửa hàng đầu tiên được mở tại Hà Nội với 2 nhân viên và 10 loại bánh cơ bản</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2019</div>
                <div class="timeline-content">
                    <h5>Mở rộng</h5>
                    <p>Khai trương chi nhánh thứ 2 và tăng quy mô sản xuất lên 50 loại bánh đa dạng</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2021</div>
                <div class="timeline-content">
                    <h5>Chứng nhận</h5>
                    <p>Đạt chứng nhận ATVSTP và giải thưởng "Thương hiệu bánh ngọt uy tín năm 2021"</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2023</div>
                <div class="timeline-content">
                    <h5>Chuyển đổi số</h5>
                    <p>Ra mắt website và hệ thống đặt hàng online, phục vụ hàng nghìn khách hàng mỗi tháng</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2024</div>
                <div class="timeline-content">
                    <h5>Phát triển mạnh mẽ</h5>
                    <p>Mở thêm 3 chi nhánh mới và đạt mốc 10,000 khách hàng thường xuyên</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Section -->
    <div class="about-section">
        <div class="section-title">
            <h2>Đội ngũ của chúng tôi</h2>
            <p>Những người đứng sau thành công</p>
        </div>
        
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="team-member">
                    <div class="team-avatar">AN</div>
                    <h5>Nguyễn Văn An</h5>
                    <div class="position">Founder & Master Chef</div>
                    <p>15 năm kinh nghiệm làm bánh, từng học tập tại Pháp</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="team-member">
                    <div class="team-avatar">BH</div>
                    <h5>Trần Bích Hạnh</h5>
                    <div class="position">Giám đốc Điều hành</div>
                    <p>Chuyên gia quản lý với hơn 10 năm kinh nghiệm</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="team-member">
                    <div class="team-avatar">MT</div>
                    <h5>Lê Minh Tuấn</h5>
                    <div class="position">Trưởng phòng Sản xuất</div>
                    <p>Đảm bảo chất lượng sản phẩm luôn đồng đều</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="team-member">
                    <div class="team-avatar">HL</div>
                    <h5>Phạm Hương Lan</h5>
                    <div class="position">Trưởng phòng Marketing</div>
                    <p>Sáng tạo những chiến dịch marketing độc đáo</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CTA Section -->
    <div class="about-section text-center" style="padding-bottom: 80px;">
        <div class="story-card" style="background: var(--primary-orange); color: white; border: none;">
            <h2 style="color: white; margin-bottom: 20px; font-weight: bold;">Hãy đến và trải nghiệm!</h2>
            <p style="font-size: 18px; margin-bottom: 30px; color: rgba(255,255,255,0.9);">
                Ghé thăm cửa hàng của chúng tôi để cảm nhận trực tiếp chất lượng sản phẩm 
                và sự tận tâm trong từng chiếc bánh
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?= url('user/pages/products.php') ?>" class="btn-cta bg-white" style="color: var(--dark-brown) !important; border-radius:30px; padding: 12px 30px; text-decoration: none; font-weight: bold;">
                    <i class="fas fa-shopping-bag"></i> Xem sản phẩm
                </a>
                <a href="<?= url('contact.php') ?>" class="btn-cta" style="background: var(--dark-brown); color: white; border-radius:30px; padding: 12px 30px; text-decoration: none; font-weight: bold;">
                    <i class="fas fa-phone"></i> Liên hệ ngay
                </a>
            </div>
        </div>
    </div>
</div>
 
<script>
// Counter animation
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}
 
// Intersection Observer for counter animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                animateCounter(counter);
            });
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });
 
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    observer.observe(statsSection);
}
</script>
 
<?php include 'includes/layouts/footer.php'; ?>