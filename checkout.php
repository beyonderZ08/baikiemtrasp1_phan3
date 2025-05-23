<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$pageTitle = "Thanh toán - Tech 4.0";
include 'header.php';

// Lấy user_id từ username
$username = $_SESSION['username'];
try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $error_message = "Không tìm thấy thông tin người dùng.";
    } else {
        $user_id = $user['id'];
    }
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin người dùng: " . $e->getMessage();
}

// Xử lý đơn hàng
if (isset($_POST['place_order']) && !isset($error_message)) {
    $address = $_POST['address'];
    $shipping = $_POST['shipping'];
    $payment = $_POST['payment'];
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    try {
        $stmt = $conn->prepare("INSERT INTO orders (username, address, shipping_method, payment_method, total, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $address, $shipping, $payment, $total]);
        $orderId = $conn->lastInsertId();

        foreach ($_SESSION['cart'] as $item) {
            $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['quantity']]);
        }

        unset($_SESSION['cart']); // Xóa giỏ hàng sau khi đặt hàng thành công
        header("Location: order_success.php?order_id=" . $orderId);
        exit;
    } catch (PDOException $e) {
        $error_message = "Lỗi: " . $e->getMessage();
    }
}
?>

<main class="checkout-content">
    <div class="checkout-box">
        <h2>Thanh toán</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="checkout-items">
            <h3>Sản phẩm trong giỏ hàng</h3>
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $index => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="checkout-item">
                    <div class="checkout-item-info">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p>Giá: <?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ x <?php echo $item['quantity']; ?></p>
                        <p>Tổng: <?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</p>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="checkout-total">
                <h4>Tổng cộng: <span><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</span></h4>
            </div>
        </div>

        <form method="POST" action="" class="checkout-form">
            <div class="checkout-section">
                <h3>Thông tin giao hàng</h3>
                <div class="form-group">
                    <label for="address">Địa chỉ nhận hàng:</label>
                    <input type="text" id="address" name="address" required placeholder="Nhập địa chỉ của bạn">
                </div>
                <div class="form-group">
                    <label for="shipping">Phương thức giao hàng:</label>
                    <select id="shipping" name="shipping" required>
                        <option value="standard">Giao hàng tiêu chuẩn (Miễn phí)</option>
                        <option value="express">Giao hàng nhanh (50,000 VNĐ)</option>
                    </select>
                </div>
            </div>

            <div class="checkout-section">
                <h3>Phương thức thanh toán</h3>
                <div class="form-group">
                    <label>
                        <input type="radio" name="payment" value="cod" required> Thanh toán khi nhận hàng (COD)
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="radio" name="payment" value="online"> Thanh toán online (Chuyển khoản)
                    </label>
                </div>
            </div>

            <button type="submit" name="place_order" class="btn-place-order">Đặt hàng</button>
        </form>
    </div>
    <div class="tech-overlay"></div>
</main>

<?php
// Kiểm tra sự tồn tại của footer.php trước khi include
$footerPath = __DIR__ . '/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo "<p style='color: red; text-align: center;'>Error: footer.php not found. Please ensure the file exists in the directory.</p>";
}
?>