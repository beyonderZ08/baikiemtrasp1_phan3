<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=2");
    exit();
}

$username = $_SESSION['username'];

// Lấy thông tin người dùng (bao gồm ảnh đại diện từ cơ sở dữ liệu)
try {
    $stmt = $conn->prepare("SELECT avatar FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $avatar = $user['avatar'] ?? 'https://via.placeholder.com/150'; // Ảnh mặc định nếu không có
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin người dùng: " . $e->getMessage();
}

// Xử lý upload ảnh mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $target_dir = "uploads/"; // Thư mục lưu ảnh
    $target_file = $target_dir . basename($_FILES['avatar']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
            // Cập nhật avatar vào cơ sở dữ liệu
            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE username = ?");
            $stmt->execute([$target_file, $username]);
            $avatar = $target_file;
        } else {
            $error_message = "Lỗi khi upload ảnh.";
        }
    } else {
        $error_message = "Chỉ chấp nhận file JPG, JPEG, PNG, GIF.";
    }
}

// Xử lý thay đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Lấy mật khẩu hiện tại từ database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$hashed_password, $username]);
            $password_message = "Đổi mật khẩu thành công!";
        } else {
            $password_message = "Mật khẩu mới không khớp.";
        }
    } else {
        $password_message = "Mật khẩu hiện tại không đúng.";
    }
}

$pageTitle = "Trang hồ sơ - Tech 4.0";
include 'header.php';
?>
<main class="profile-content">
    <div class="profile-box">
        <h1>TRANG HỒ SƠ</h1>
        <div class="avatar-section">
            <div class="avatar-wrapper">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Ảnh đại diện" class="avatar-image" onclick="document.getElementById('avatar-upload').click();">
                <form method="POST" enctype="multipart/form-data" id="avatar-form" style="display: none;">
                    <input type="file" name="avatar" id="avatar-upload" accept="image/*" onchange="this.form.submit();">
                </form>
            </div>
            <p>Chào mừng, <?php echo htmlspecialchars($username); ?>!</p>
            <?php if (isset($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>
            <button type="button" class="btn-update-avatar" onclick="document.getElementById('avatar-upload').click();">Cập nhật</button>
        </div>
        <div class="password-change-section">
            <h3>Đổi mật khẩu</h3>
            <?php if (isset($password_message)) echo "<p>$password_message</p>"; ?>
            <form method="POST" action="">
                <div>
                    <label>Mật khẩu hiện tại:</label>
                    <input type="password" name="current_password" required>
                </div>
                <div>
                    <label>Mật khẩu mới:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div>
                    <label>Xác nhận mật khẩu mới:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password">Cập nhật mật khẩu</button>
            </form>
        </div>
    </div>
    <div class="tech-overlay"></div>
</main>

<footer>
    <p>© 2025 - Công nghệ 4.0</p>
</footer>
</body>
</html>