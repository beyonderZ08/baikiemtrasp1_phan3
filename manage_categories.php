<?php
// manage_categories.php

session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=2");
    exit();
}

if ($_SESSION['username'] !== 'admin') {
    header("Location: dashboard.php?error=3");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_categories.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error_message = "Lỗi khi xóa danh mục: " . $e->getMessage();
    }
}

try {
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error_message = "Lỗi khi lấy danh sách danh mục: " . $e->getMessage();
}

if (isset($_GET['success'])) {
    $success_message = "Thao tác thành công!";
}

$pageTitle = "Quản Lý Danh Mục - Tech 4.0";
include 'header.php';
?>

    <main class="manage-categories-content">
        <div class="manage-categories-box">
            <h2>Quản Lý Danh Mục Sản Phẩm</h2>
            <p class="back-link"><a href="create_category.php" class="btn-create">Thêm Danh Mục Mới</a></p>
            <p class="back-link"><a href="dashboard.php" class="btn-back">Quay lại Dashboard</a></p>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($categories)): ?>
                <table class="categories-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['id']); ?></td>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><?php echo htmlspecialchars($category['description'] ?? 'Không có'); ?></td>
                                <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                                <td>
                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn-edit">Sửa</a>
                                    <a href="manage_categories.php?action=delete&id=<?php echo $category['id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="error-message">Chưa có danh mục nào!</p>
            <?php endif; ?>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>
