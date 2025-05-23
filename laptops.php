<?php
session_start();
include 'db_connect.php';

// Lấy sản phẩm thuộc danh mục Laptop (category_id = 2)
try {
    $stmt = $conn->prepare("SELECT p.* FROM products p JOIN categories c ON p.category_id = c.id WHERE c.id = 2");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}

$pageTitle = "Laptop - Tech 4.0";
include 'header.php';
?>

<main class="product-content">
    <div class="product-box">
        <h1>LAPTOP</h1>
        <div class="product-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có sản phẩm nào trong danh mục này.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    <p>© 2025 - Công nghệ 4.0</p>
</footer>
</body>
</html>

