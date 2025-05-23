<?php
// Kết nối CSDL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Lấy danh sách danh mục
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        echo '<a href="product-list.php?category=' . htmlspecialchars($category['name']) . '">' . htmlspecialchars($category['name']) . '</a>';
    }
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
$conn = null;
?>  