<?php
session_start();
include '../db_connect.php'; // Giả định file kết nối cơ sở dữ liệu

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Thêm Sản Phẩm Mới - Tech 4.0";
include '../header.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';

    if (!empty($name) && is_numeric($price) && $price >= 0) {
        try {
            $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $description]);
            $success_message = "Thêm sản phẩm thành công!";
        } catch (PDOException $e) {
            $error_message = "Lỗi khi thêm sản phẩm: " . $e->getMessage();
        }
    } else {
        $error_message = "Vui lòng nhập đầy đủ thông tin và giá hợp lệ!";
    }
}
?>

<main class="product-create-content">
    <div class="product-create-box">
        <h1>THÊM SẢN PHẨM MỚI</h1>
        <?php if ($success_message) echo "<p style='color: green;'>$success_message</p>"; ?>
        <?php if ($error_message) echo "<p style='color: red;'>$error_message</p>"; ?>
        <form method="POST" class="product-form">
            <div class="form-group">
                <label for="name">Tên Sản Phẩm:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Giá (VNĐ):</label>
                <input type="number" id="price" name="price" min="0" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit" class="btn-submit">THÊM SẢN PHẨM</button>
        </form>
        <a href="index.php" class="btn-back">QUAY LẠI</a>
    </div>
    <div class="tech-overlay"></div>
</main>

<footer>
    <p>© 2025 - Công nghệ 4.0</p>
</footer>
</body>
</html>