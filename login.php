<?php
// login.php

// Khởi tạo session
session_start();

// Kết nối database
include 'db_connect.php';

// Khởi tạo các biến thông báo
$welcome_message = "";
$error_message = "";
$success_message = "";

// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['username'])) {
    // Nếu đã đăng nhập, chuyển hướng ngay đến dashboard
    if (file_exists("dashboard.php")) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "File dashboard.php không tồn tại!";
    }
}

// Xử lý đăng nhập khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và làm sạch khoảng trắng
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Kiểm tra thông tin đăng nhập từ database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công, lưu username vào session
            $_SESSION['username'] = $user['username'];

            // Chuyển hướng đến dashboard
            if (file_exists("dashboard.php")) {
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "File dashboard.php không tồn tại!";
            }
        } else {
            $error_message = "Email hoặc mật khẩu không đúng!";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi khi đăng nhập: " . $e->getMessage();
    }
}

// Kiểm tra thông báo lỗi hoặc thành công từ các trang khác
if (isset($_GET['error'])) {
    if ($_GET['error'] == 2) {
        $error_message = "Vui lòng đăng nhập để truy cập danh sách sản phẩm!";
    }
}
if (isset($_GET['success'])) {
    $success_message = "Đăng ký thành công! Vui lòng đăng nhập.";
}

// Include header
$pageTitle = "Đăng Nhập - Tech 4.0";
include 'header.php';
?>

    <main class="login-content">
        <div class="login-box">
            <h2>Đăng Nhập Hệ Thống</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="btn-login">Đăng Nhập</button>
                <div class="login-options">
                    <p class="signup-link">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    <p class="forgot-link"><a href="forgotpassword.php">Quên mật khẩu?</a></p>
                </div>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>