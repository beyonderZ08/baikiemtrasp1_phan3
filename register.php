<?php
// register.php

// Khởi tạo session
session_start();

// Kết nối database
include 'db_connect.php';

// Khởi tạo các biến thông báo
$error_message = "";
$success_message = "";

// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// Xử lý đăng ký khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và làm sạch khoảng trắng
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Vui lòng điền đầy đủ thông tin!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email không hợp lệ!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Mật khẩu nhập lại không khớp!";
    } else {
        try {
            // Kiểm tra xem username hoặc email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->rowCount() > 0) {
                $error_message = "Tên đăng nhập hoặc email đã được sử dụng!";
            } else {
                // Mã hóa mật khẩu
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Lưu thông tin vào database (reset_token để NULL)
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, reset_token) VALUES (?, ?, ?, NULL)");
                $stmt->execute([$username, $email, $hashed_password]);

                // Chuyển hướng đến trang đăng nhập với thông báo thành công
                header("Location: login.php?success=1");
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi khi đăng ký: " . $e->getMessage();
        }
    }
}

// Include header
$pageTitle = "Đăng Ký - Tech 4.0";
include 'header.php';
?>

    <main class="register-content">
        <div class="register-box">
            <h2>Đăng Ký Tài Khoản</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" type="email" id="email" name="email" placeholder="Nhập email" required>
                </div>
                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Nhập lại mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                </div>
                <button type="submit" class="btn-register">Đăng Ký</button>
                <p class="login-link">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>