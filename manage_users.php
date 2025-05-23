<?php
// manage_users.php

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

// Xử lý xóa người dùng
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // Lấy thông tin người dùng để xóa avatar nếu có
        $stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Xóa avatar nếu tồn tại
        if ($user['avatar'] && file_exists($user['avatar'])) {
            unlink($user['avatar']);
        }

        // Xóa người dùng
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_users.php?success=1"); // Xóa thành công
        exit();
    } catch (PDOException $e) {
        $error_message = "Lỗi khi xóa người dùng: " . $e->getMessage();
    }
}

// Lấy danh sách người dùng từ database
try {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $error_message = "Lỗi khi lấy danh sách người dùng: " . $e->getMessage();
}

// Kiểm tra thông báo thành công
if (isset($_GET['success'])) {
    $success_message = "Thao tác thành công!";
}

// Include header
$pageTitle = "Quản Lý Người Dùng - Tech 4.0";
include 'header.php';
?>

    <main class="manage-users-content">
        <div class="manage-users-box">
            <h2>Quản Lý Người Dùng</h2>
            <p class="back-link"><a href="create_user.php" class="btn-create">Thêm Người Dùng Mới</a></p>
            <p class="back-link"><a href="dashboard.php" class="btn-back">Quay lại Dashboard</a></p>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($users)): ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Ảnh đại diện</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar-preview">
                                    <?php else: ?>
                                        Không có
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">Sửa</a>
                                    <a href="manage_users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="error-message">Chưa có người dùng nào!</p>
            <?php endif; ?>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>