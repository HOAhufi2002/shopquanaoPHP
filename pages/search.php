<?php
// Nhận từ khóa tìm kiếm
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

if (!empty($keyword)) {
    // Thực hiện truy vấn tìm kiếm sản phẩm
    if ($conn !== false) {
        // Sử dụng toán tử LIKE với ký tự % để tìm kiếm tương đối
        $sql = "SELECT * FROM Products WHERE ProductName LIKE ?";
        $params = ["%$keyword%"];
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $products = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $products[] = $row;
        }
    }
} else {
    echo "Vui lòng nhập từ khóa để tìm kiếm.";
}
?>

<h2>Kết quả tìm kiếm cho "<?php echo htmlspecialchars($keyword); ?>"</h2>
<div class="row">
    <?php if (!empty($products)) : ?>
        <?php foreach ($products as $product) : ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="<?php echo $product['ImagePath']; ?>" class="card-img-top" alt="<?php echo $product['ProductName']; ?>" />
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['ProductName']; ?></h5>
                        <p class="card-text">Giá: <?php echo number_format($product['Price'], 2); ?> VND</p>
                        <p class="card-text"><?php echo $product['Description']; ?></p>
                        <!-- Nút đặt hàng -->
                        <form method="POST" action="index.php?page=cart">
                            <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $product['ProductName']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
                            <button type="submit" class="btn btn-primary">Đặt hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không tìm thấy sản phẩm nào.</p>
    <?php endif; ?>
</div>
