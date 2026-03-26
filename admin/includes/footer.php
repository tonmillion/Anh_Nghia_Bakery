</div>
    <!-- End Content Area -->
    
    <!-- Footer -->
    <footer class="admin-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <p class="footer-text mb-0">
                        &copy; <?= date('Y') ?> <strong><?= SITE_NAME ?></strong>. All rights reserved. 
                        <span> | Version 1.0.0 | Made with <i class="fas fa-heart text-danger"></i> by <strong>Anh Nghĩa Team</strong></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>
<!-- End Main Content -->

<link rel="stylesheet" href="<?= url('admin/css/footer.css') ?>?v=<?= time() ?>">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script>
// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    
    // Save state to localStorage
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
}

// Load sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('mainContent').classList.add('expanded');
    }
});

// Auto dismiss alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }, 5000);
});

// Initialize DataTables
$(document).ready(function() {
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
});

// Confirm delete
function confirmDelete(message = 'Bạn có chắc muốn xóa?') {
    return confirm(message);
}

// Show current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('timeDisplay');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Update time every second
setInterval(updateTime, 1000);
updateTime();
</script>

</body>
</html>