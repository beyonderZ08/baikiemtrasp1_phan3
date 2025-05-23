<?php
// edit_category.php

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

if (!isset($_GET['id'])) {
    header("Location: manage_categories.php?error=4");
    exit();
}

$category_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        header("Location: manage_categories.php?error=5");
        exit();
    }
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin danh mục: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $error_message = "Vui lòng nhập tên danh mục!";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
            $stmt->execute([$name, $category_id]);
            if ($stmt->rowCount() > 0) {
                $error_message = "Tên danh mục đã tồn tại!";
            } else {
                $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $description ?: null, $category_id]);
                header("Location: manage_categories.php?success=1");
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi khi cập nhật danh mục: " . $e->getMessage();
        }
    }
}

$pageTitle = "Chỉnh Sửa Danh Mục - Tech 4.0";
include 'header.php';
?>

    <main class="edit-category-content">
        <div class="edit-category-box">
            <h2>Chỉnh Sửa Danh Mục</h2>
            <p class="back-link"><a href="manage_categories.php" class="btn-back">Quay lại</a></p>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="edit_category.php?id=<?php echo $category_id; ?>" method="POST">
                <div class="input-group">
                    <label for="name">Tên danh mục</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" placeholder="Nhập tên danh mục" required>
                </div>
                <div class="input-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Nhập mô tả (tùy chọn)"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn-edit">Cập Nhật</button>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>