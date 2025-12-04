<?php ob_start(); ?>

<section class="hero">
    <div class="hero-text">
        <h1>Quản lý Chi tiêu Thông minh</h1>
        <p>Giúp bạn theo dõi thu nhập – chi tiêu, lập ngân sách, xem thống kê trực quan và kiểm soát tài chính dễ dàng hơn mỗi ngày.</p>

        <div class="hero-buttons">
            <a href="?controller=auth&action=register" class="btn-primary">Bắt đầu miễn phí</a>
            <a href="?controller=auth&action=login" class="btn-secondary">Đăng nhập</a>
        </div>
    </div>

    <div class="hero-image">
        <i class="fa-solid fa-coins hero-icon"></i>
    </div>
</section>

<section class="features">
    <h2>Tính năng nổi bật</h2>
    <div class="feature-list">

        <div class="feature-item">
            <i class="fa-solid fa-pen-to-square"></i>
            <h3>Ghi chép thu chi nhanh</h3>
            <p>Thêm giao dịch chỉ trong vài giây với giao diện đơn giản, dễ dùng.</p>
        </div>

        <div class="feature-item">
            <i class="fa-solid fa-chart-line"></i>
            <h3>Biểu đồ chi tiêu trực quan</h3>
            <p>Xem bạn đang tiêu vào đâu nhiều nhất bằng biểu đồ đẹp và dễ hiểu.</p>
        </div>

        <div class="feature-item">
            <i class="fa-solid fa-wallet"></i>
            <h3>Lập ngân sách thông minh</h3>
            <p>Cảnh báo khi bạn sắp vượt hạn mức đã đặt.</p>
        </div>

        <div class="feature-item">
            <i class="fa-solid fa-file-export"></i>
            <h3>Xuất báo cáo</h3>
            <p>Xuất PDF, CSV để theo dõi hoặc gửi cho người khác.</p>
        </div>

    </div>
</section>

<?php $content = ob_get_clean(); include 'layout.php'; ?>
