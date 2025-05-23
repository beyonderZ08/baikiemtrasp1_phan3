<?php
// manage_products.php

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
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product['image'] && file_exists($product['image'])) {
            unlink($product['image']);
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_products.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error_message = "Lỗi khi xóa sản phẩm: " . $e->getMessage();
    }
}

try {
    $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $error_message = "Lỗi khi lấy danh sách sản phẩm: " . $e->getMessage();
}

if (isset($_GET['success'])) {
    $success_message = "Thao tác thành công!";
}

$pageTitle = "Quản Lý Sản Phẩm - Tech 4.0";
include 'header.php';
?>

    <main class="manage-products-content">
        <div class="manage-products-box">
            <h2>Quản Lý Sản Phẩm</h2>
            <p class="back-link"><a href="create_product.php" class="btn-create">Thêm Sản Phẩm Mới</a></p>
            <p class="back-link"><a href="dashboard.php" class="btn-back">Quay lại Dashboard</a></p>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($success_message)): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($products)): ?>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Hình ảnh</th>
                            <th>Danh mục</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['description'] ?? 'Không có'); ?></td>
                                <td><?php echo htmlspecialchars(number_format($product['price'], 0, ',', '.')); ?></td>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="product-image">
                                    <?php else: ?>
                                        Không có
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Sửa</a>
                                    <a href="manage_products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="error-message">Chưa có sản phẩm nào!</p>
            <?php endif; ?>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>