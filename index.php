<?php
session_start();
include 'db_connect.php'; // Giả định file kết nối cơ sở dữ liệu

// Nếu đã đăng nhập, chuyển hướng ngay đến dashboard.php
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

$pageTitle = "Tech 4.0 - Trang chủ";
include 'header.php';
?>

<main class="content">
    <div class="hero">
        <h1>Chào mừng đến với Tech 4.0</h1>
        <div class="intro-text">
            <p>Khám phá công nghệ tiên tiến và sản phẩm hiện đại tại Tech 4.0. Chúng tôi mang đến trải nghiệm đỉnh cao cho mọi khách hàng.</p>
        </div>
        <div class="action-buttons">
            <a href="login.php" class="btn-primary">Đăng nhập</a>
            <a href="register.php" class="btn-secondary">Đăng ký</a>
        </div>
    </div>
    <div class="tech-overlay"></div>
</main>

<?php include 'footer.php'; ?>