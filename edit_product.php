<?php
// edit_product.php

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
    header("Location: manage_products.php?error=4");
    exit();
}

$product_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: manage_products.php?error=5");
        exit();
    }
} catch (PDOException $e) {
    $error_message = "Lỗi khi lấy thông tin sản phẩm: " . $e->getMessage();
}

try {
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error_message = "Lỗi khi lấy danh sách danh mục: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = str_replace('.', '', trim($_POST['price'])); // Loại bỏ dấu chấm
    $category_id = $_POST['category_id'];

    $image_path = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_ext)) {
            $error_message = "Chỉ cho phép tải lên file JPG, JPEG, PNG hoặc GIF!";
        } else {
            $new_file_name = uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;

                if ($product['image'] && file_exists($product['image'])) {
                    unlink($product['image']);
                }
            } else {
                $error_message = "Lỗi khi tải lên hình ảnh!";
            }
        }
    }

    if (empty($error_message)) {
        if (empty($name) || empty($price) || empty($category_id)) {
            $error_message = "Vui lòng điền đầy đủ thông tin bắt buộc!";
        } elseif (!is_numeric($price) || $price < 0) {
            $error_message = "Giá phải là số không âm!";
        } else {
            try {
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
                $stmt->execute([$name, $description ?: null, $price, $image_path, $category_id, $product_id]);
                header("Location: manage_products.php?success=1");
                exit();
            } catch (PDOException $e) {
                $error_message = "Lỗi khi cập nhật sản phẩm: " . $e->getMessage();
            }
        }
    }
}

$pageTitle = "Chỉnh Sửa Sản Phẩm - Tech 4.0";
include 'header.php';
?>

    <main class="edit-product-content">
        <div class="edit-product-box">
            <h2>Chỉnh Sửa Sản Phẩm</h2>
            <p class="back-link"><a href="manage_products.php" class="btn-back">Quay lại</a></p>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="name">Tên sản phẩm</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Nhập tên sản phẩm" required>
                </div>
                <div class="input-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" placeholder="Nhập mô tả (tùy chọn)"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                <div class="input-group">
                    <label for="price">Giá</label>
                    <input type="text" id="price" name="price" value="<?php echo number_format($product['price'], 0, ',', '.'); ?>" placeholder="Nhập giá" required>
                </div>
                <div class="input-group">
                    <label for="image">Hình ảnh</label>
                    <?php if ($product['image']): ?>
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="product-image">
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <div class="input-group">
                    <label for="category_id">Danh mục</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Chọn danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-edit">Cập Nhật</button>
            </form>
        </div>
        <div class="tech-overlay"></div>
    </main>

    <footer>
        <p>© 2025 - Công nghệ 4.0</p>
    </footer>

    <script>
        // Định dạng giá với dấu chấm phân cách hàng nghìn
        document.getElementById('price').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
            if (value) {
                value = parseInt(value).toLocaleString('vi-VN'); // Định dạng số với dấu chấm
                e.target.value = value;
            }
        });

        // Loại bỏ dấu chấm trước khi gửi form
        document.querySelector('form').addEventListener('submit', function(e) {
            let priceInput = document.getElementById('price');
            priceInput.value = priceInput.value.replace(/\./g, ''); // Loại bỏ dấu chấm
        });
    </script>
</body>
</html>