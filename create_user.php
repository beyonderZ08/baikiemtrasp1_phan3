<?php
// create_user.php

// Khởi tạo session
session_start();

// Kết nối database
include 'db_connect.php';

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=2"); // Vui lòng đăng nhập
    exit();
}

// Kiểm tra quyền quản trị
if ($_SESSION['username'] !== 'admin') {
    header("Location: dashboard.php?error=3"); // Không có quyền truy cập
    exit();
}

// Khởi tạo các biến thông báo
$error_message = "";
$success_message = "";

// Xử lý thêm người dùng khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Xử lý file avatar
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['avatar']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_ext)) {
            $error_message = "Chỉ cho phép tải lên file JPG, JPEG, PNG hoặc GIF!";
        } else {
            $new_file_name = uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                $error_message = "Lỗi khi tải lên ảnh đại diện!";
            } else {
                $avatar_path = $target_path;
            }
        }
    }

    // Kiểm tra dữ liệu đầu vào
    if (empty($error_message)) {
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

                    // Lưu thông tin vào database
                    $stmt = $conn->prepare("INSERT INTO users (username, email, avatar, password, reset_token) VALUES (?, ?, ?, ?, NULL)");
                    $stmt->execute([$username, $email, $avatar_path, $hashed_password]);

                    // Chuyển hướng về trang quản lý người dùng
                    header("Location: manage_users.php?success=1");
                    exit();
                }
            } catch (PDOException $e) {
                $error_message = "Lỗi khi thêm người dùng: " . $e->getMessage();
            }
        }
    }
}

// Include header
$pageTitle = "Thêm Người Dùng - Tech 4.0";
include 'header.php';
?>

    <main class="create-user-content">
        <div class="create-user-box">
            <h2>Thêm Người Dùng Mới</h2>
            <p class="back-link"><a href="manage_users.php" class="btn-back">Quay lại</a></p>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="create_user.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email" required>
                </div>
                <div class="input-group">
                    <label for="avatar">Ảnh đại diện</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
                <div class="input-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Nhập lại mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                </div>
                <button type="submit" class="btn-create">Thêm Người Dùng</button>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>