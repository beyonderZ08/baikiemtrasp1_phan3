<?php
// product_details.php

session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=2");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=4");
    exit();
}

$product_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: dashboard.php?error=5");
        exit();
    }
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin sản phẩm: " . $e->getMessage();
}

// Xử lý giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $item = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
    $_SESSION['cart'][] = $item;
    header("Location: product_details.php?id=" . $product['id']);
    exit();
}

if (isset($_POST['buy_now'])) {
    $item = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
    $_SESSION['cart'] = [$item];
    header("Location: checkout.php");
    exit();
}

$pageTitle = htmlspecialchars($product['name']) . " - Tech 4.0";
include 'header.php';
?>

    <main class="product-details-content">
        <div class="product-details-box">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/450'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="main-product-image">
                </div>
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="product-price">
                    <span class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                    <?php if ($product['price'] > 0): ?>
                        <span class="old-price"><?php echo number_format($product['price'] * 1.1, 0, ',', '.'); ?>đ</span>
                        <span class="discount">-10%</span>
                    <?php endif; ?>
                </div>
                <div class="product-options">
                    <label>Màu sắc:</label>
                    <select>
                        <option>Đen</option>
                        <option>Trắng</option>
                        <option>Xám</option>
                    </select>
                    <label>Dung lượng:</label>
                    <select>
                        <option>64GB</option>
                        <option>128GB</option>
                        <option>256GB</option>
                    </select>
                </div>
                <div class="product-promotion">
                    <p><strong>Khuyến mãi:</strong> Giảm ngay 10% + Tặng kèm ốp lưng</p>
                </div>
                <div class="product-rating">
                    <span>★★★★☆ (5.0/5)</span> - 100 đánh giá
                </div>
                <div class="product-actions">
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="add_to_cart" class="btn-add-cart">Thêm vào giỏ hàng</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="buy_now" class="btn-buy-now">Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>