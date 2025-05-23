<?php
session_start();
include '../db_connect.php'; // Giả định file kết nối cơ sở dữ liệu

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if (!$isAdmin) {
    header("Location: ../dashboard.php");
    exit;
}

// Xử lý logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
try {
    $stmt = $conn->prepare("SELECT id, name, price, description, category_id FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $productCount = count($products);
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy danh sách sản phẩm: " . $e->getMessage();
}

// Xác định trang hiện tại
$current_page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tech 4.0</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin</h2>
            </div>
            <nav class="sidebar-menu">
                <a href="?page=dashboard" class="menu-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="?page=manage-posts" class="menu-item <?php echo $current_page === 'manage-posts' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> Quản lý sản phẩm</a>
                <a href="?page=manage-users" class="menu-item <?php echo $current_page === 'manage-users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Quản lý người dùng</a>
                <a href="?page=settings" class="menu-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Cài đặt</a>
                <a href="?action=logout" class="menu-item"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <?php if ($current_page === 'dashboard'): ?>
                <h1>Dashboard</h1>
                <div class="stats-cards">
                    <div class="stat-card" style="background-color: #00aaff;">
                        <i class="fas fa-box"></i>
                        <h3><?php echo $productCount; ?></h3>
                        <p>Sản phẩm</p>
                        <a href="?page=manage-posts" class="card-link">Chi tiết</a>
                    </div>
                    <div class="stat-card" style="background-color: #00cc00;">
                        <i class="fas fa-users"></i>
                        <h3>0</h3>
                        <p>Người dùng</p>
                        <a href="?page=manage-users" class="card-link">Chi tiết</a>
                    </div>
                    <div class="stat-card" style="background-color: #ffcc00;">
                        <i class="fas fa-cog"></i>
                        <h3>-</h3>
                        <p>Cài đặt</p>
                        <a href="?page=settings" class="card-link">Chi tiết</a>
                    </div>
                </div>
            <?php elseif ($current_page === 'manage-posts'): ?>
                <h1>Quản lý sản phẩm</h1>
                <div class="product-list-box">
                    <a href="create.php" class="btn-add-product">Thêm sản phẩm mới</a>
                    <?php if (isset($error_message)): ?>
                        <p style="color: red;"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <?php if (empty($products)): ?>
                        <p>Không có sản phẩm nào.</p>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Giá (VNĐ)</th>
                                    <th>Mô tả</th>
                                    <th>Danh mục</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_id']); ?></td>
                                        <td>
                                            <a href="update.php?id=<?php echo $product['id']; ?>" class="btn-update">Cập nhật</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php elseif ($current_page === 'manage-users'): ?>
                <h1>Quản lý người dùng</h1>
                <div class="product-list-box">
                    <p>Chức năng quản lý người dùng chưa được triển khai.</p>
                </div>
            <?php elseif ($current_page === 'settings'): ?>
                <h1>Cài đặt</h1>
                <div class="product-list-box">
                    <p>Chức năng cài đặt chưa được triển khai.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>
</body>
</html>