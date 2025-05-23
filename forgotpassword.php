<?php
// forgot_password.php

// Khởi tạo session
session_start();

// Khởi tạo các biến thông báo
$error_message = "";
$success_message = "";

// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['username'])) {
    // Nếu đã đăng nhập, chuyển hướng về dashboard
    header("Location: dashboard.php");
    exit();
}

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và làm sạch khoảng trắng
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($email) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Vui lòng điền đầy đủ thông tin!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email không hợp lệ!";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Mật khẩu nhập lại không khớp!";
    } else {
        // Kiểm tra thông tin đăng ký trong session
        if (isset($_SESSION['registered_username']) && isset($_SESSION['registered_email'])) {
            $registered_username = $_SESSION['registered_username'];
            $registered_email = $_SESSION['registered_email'];

            // Xác minh danh tính
            if ($username === $registered_username && $email === $registered_email) {
                // Cập nhật mật khẩu mới vào session
                $_SESSION['registered_password'] = $new_password;
                $success_message = "Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.";
            } else {
                $error_message = "Tên đăng nhập hoặc email không đúng!";
            }
        } else {
            $error_message = "Không tìm thấy tài khoản đăng ký. Vui lòng đăng ký trước!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - Tech 4.0</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Tech 4.0</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="#">About</a>
                <a href="contact.php">Contact</a>
            </nav>
        </div>
    </header>

    <main class="forgot-password-content">
        <div class="forgot-password-box">
            <h2>Đặt Lại Mật Khẩu</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
                <p class="login-link">Quay lại <a href="login.php">Đăng nhập</a></p>
            <?php else: ?>
                <form action="forgot_password.php" method="POST">
                    <div class="input-group">
                        <label for="username">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Nhập email" required>
                    </div>
                    <div class="input-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Nhập lại mật khẩu mới</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                    <button type="submit" class="btn-reset">Đặt Lại Mật Khẩu</button>
                    <p class="login-link">Quay lại <a href="login.php">Đăng nhập</a></p>
                </form>
            <?php endif; ?>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>