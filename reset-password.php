<?php
require_once 'config_user.php';

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $new_password = $_POST["new-password"] ?? "";
    $confirm_password = $_POST["confirm-password"] ?? "";

    // Validate dữ liệu
    if (!$email) $errors["email"] = "Vui lòng nhập email.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors["email"] = "Email không hợp lệ.";
    if (!$new_password) $errors["password"] = "Vui lòng nhập mật khẩu mới.";
    elseif (strlen($new_password) < 6) $errors["password"] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
    if ($new_password !== $confirm_password) $errors["confirm_password"] = "Mật khẩu xác nhận không khớp.";

    if (!$errors) {
        // Kiểm tra email có tồn tại không
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Cập nhật mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_password = NULL WHERE email = ?");
            if ($stmt->execute([$hashed_password, $email])) {
                $success = "Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.";
                header("Refresh: 2; url=login.php");
            } else {
                $errors["database"] = "Đã có lỗi xảy ra. Vui lòng thử lại.";
            }
        } else {
            $errors["email"] = "Email không tồn tại.";
        }
    }
}
?>