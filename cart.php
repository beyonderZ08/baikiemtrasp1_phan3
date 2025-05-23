<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Xử lý cập nhật số lượng sản phẩm
if (isset($_POST['update_quantity'])) {
    $index = $_POST['index'];
    $newQuantity = max(1, (int)$_POST['quantity']); // Đảm bảo số lượng không nhỏ hơn 1
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $newQuantity;
    }
    header("Location: cart.php");
    exit;
}

$pageTitle = "Giỏ hàng - Tech 4.0";
include 'header.php';
?>

<main class="cart-content">
    <div class="cart-box">
        <h2>Giỏ hàng của bạn</h2>
        <?php
        $total = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            echo '<div class="cart-items">';
            foreach ($_SESSION['cart'] as $index => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                // Dynamically set image based on product, fallback to default if not set
                $imagePath = isset($item['image']) && !empty($item['image']) 
                    ? './uploads/' . htmlspecialchars(basename($item['image'])) 
                    : './uploads/default_image.png';
                $defaultImage = './uploads/default_image.png';
                $finalImagePath = file_exists($imagePath) ? $imagePath : $defaultImage;
                ?>
                <div class="cart-item">
                   
                    <div class="cart-item-info">
                        <h3><?php echo htmlspecialchars($item['name'] ?? 'Unnamed Product'); ?></h3>
                        <p class="cart-item-price"><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</p>
                        <form method="POST" action="" class="cart-item-quantity">
                            <label for="quantity-<?php echo $index; ?>">Số lượng:</label>
                            <input type="number" id="quantity-<?php echo $index; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="update_quantity" class="btn-update">Cập nhật</button>
                        </form>
                        <p class="cart-item-subtotal">Tổng: <?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</p>
                        <a href="remove_from_cart.php?id=<?php echo $index; ?>" class="btn-delete">Xóa</a>
                    </div>
                </div>
        <?php
            }
            echo '</div>';
            echo '<div class="cart-summary">';
            echo '<h3>Tổng cộng: <span>' . number_format($total, 0, ',', '.') . ' VNĐ</span></h3>';
            echo '<a href="checkout.php" class="btn-primary">Thanh toán</a>';
            echo '</div>';
        } else {
            echo '<p class="empty-cart">Giỏ hàng của bạn trống.</p>';
        }
        ?>
    </div>
    <div class="tech-overlay"></div>
</main>

<?php include 'footer.php'; ?>