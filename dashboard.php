<?php
// dashboard.php

session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php?error=2");
    exit();
}

$username = $_SESSION['username'];
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: login.php?error=3");
        exit();
    }
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin người dùng: " . $e->getMessage();
}

try {
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $error_message = "Lỗi khi lấy danh sách sản phẩm: " . $e->getMessage();
}

if (isset($_GET['error']) && $_GET['error'] == 3) {
    $error_message = "Bạn không có quyền truy cập trang quản lý!";
}
// Xử lý mua hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO orders (username, product_id, product_name, price, order_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$username, $product_id, $product_name, $price]);
}

// Lấy danh sách đơn hàng (chỉ cho admin)
$orders = [];
if ($_SESSION['username'] === 'admin') {
    try {
        $stmt = $conn->prepare("SELECT * FROM orders ORDER BY order_date DESC");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Lỗi khi lấy danh sách đơn hàng: " . $e->getMessage();
    }
}

if (isset($_GET['error']) && $_GET['error'] == 3) {
    $error_message = "Bạn không có quyền truy cập trang quản lý!";
}

$pageTitle = "Dashboard - Tech 4.0";
include 'header.php';
?>

    <main class="dashboard-content">
        <div class="dashboard-box">
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="product-list">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <img src="<?php echo htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/200'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <p class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="error-message">Chưa có sản phẩm nào!</p>
                <?php endif; ?>
            </div>
            <?php if ($_SESSION['username'] === 'admin'): ?>
                <p><a href="manage_users.php" class="btn-manage">Quản Lý Người Dùng</a></p>
                <p><a href="manage_categories.php" class="btn-manage">Quản Lý Danh Mục</a></p>
                <p><a href="manage_products.php" class="btn-manage">Quản Lý Sản Phẩm</a></p>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Đăng Xuất</a>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>