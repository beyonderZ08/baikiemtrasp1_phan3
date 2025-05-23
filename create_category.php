<?php
// create_category.php

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

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $error_message = "Vui lòng nhập tên danh mục!";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->rowCount() > 0) {
                $error_message = "Tên danh mục đã tồn tại!";
            } else {
                $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                $stmt->execute([$name, $description ?: null]);
                header("Location: manage_categories.php?success=1");
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Lỗi khi thêm danh mục: " . $e->getMessage();
        }
    }
}

$pageTitle = "Thêm Danh Mục - Tech 4.0";
include 'header.php';
?>

    <main class="create-category-content">
        <div class="create-category-box">
            <h2>Thêm Danh Mục Mới</h2>
            <p class="back-link"><a href="manage_categories.php" class="btn-back">Quay lại</a></p>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="create_category.php" method="POST">
                <div class="input-group">
                    <label for="name">Tên danh mục</label>
                    <input type="text" id="name" name="name" placeholder="Nhập tên danh mục" required>
                </div>
                <div class="input-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Nhập mô tả (tùy chọn)"></textarea>
                </div>
                <button type="submit" class="btn-create">Thêm Danh Mục</button>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>