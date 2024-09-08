<?php
// Nhận từ khóa tìm kiếm và danh mục nếu có
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Nhận id danh mục từ URL

if ($conn !== false) {
    // Truy vấn sản phẩm theo từ khóa và danh mục
    $sql = "SELECT * FROM Products WHERE 1=1"; // 1=1 để dễ dàng thêm điều kiện
    $params = [];

    // Thêm điều kiện tìm kiếm nếu có từ khóa
    if (!empty($keyword)) {
        $sql .= " AND ProductName LIKE ?";
        $params[] = "%$keyword%";
    }

    // Thêm điều kiện lọc theo danh mục nếu có
    if ($categoryId > 0) {
        $sql .= " AND CategoryID = ?";
        $params[] = $categoryId;
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $products = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $products[] = $row;
    }
} else {
    echo "Không thể kết nối tới cơ sở dữ liệu.";
}
?>

<h2 class="text-center my-5"><?php echo !empty($keyword) ? "Kết quả tìm kiếm cho: " . htmlspecialchars($keyword) : "Tất cả Sản phẩm"; ?></h2>

<div class="container">
    <div class="row">
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $product) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                    <img src="<?php echo $product['ImagePath']; ?>" class="card-img-top img-fluid" alt="<?php echo $product['ProductName']; ?>" />
                    <div class="card-body">
                            <h5 class="card-title"><?php echo $product['ProductName']; ?></h5>
                            <p class="card-text"><?php echo $product['Description']; ?></p>
                            <p class="card-text text-primary font-weight-bold">Giá: <?php echo number_format($product['Price'], 2); ?> VND</p>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <form method="POST" action="index.php?page=cart" onsubmit="return checkLogin();">
                                <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $product['ProductName']; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
                                <button type="submit" name="action" value="add_to_cart" class="btn btn-outline-secondary btn-block">Thêm vào giỏ hàng</button>
                                <button type="button" class="btn btn-primary btn-block" onclick="addToCart('<?php echo $product['ProductID']; ?>', '<?php echo $product['ProductName']; ?>', <?php echo $product['Price']; ?>)">Đặt hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function checkLogin() {
        <?php if (!isset($_SESSION['user'])): ?>
            $('#loginModal').modal('show');
            return false; // Ngăn gửi form
        <?php endif; ?>
        return true; // Cho phép gửi form
    }

    function addToCart(productId, productName, productPrice) {
        // Nếu chưa đăng nhập, hiển thị modal yêu cầu đăng nhập
        <?php if (!isset($_SESSION['user'])): ?>
            $('#loginModal').modal('show');
            return;
        <?php endif; ?>

        // Tạo form động để gửi yêu cầu thêm vào giỏ hàng
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?page=cart';

        var hiddenField1 = document.createElement('input');
        hiddenField1.type = 'hidden';
        hiddenField1.name = 'product_id';
        hiddenField1.value = productId;
        form.appendChild(hiddenField1);

        var hiddenField2 = document.createElement('input');
        hiddenField2.type = 'hidden';
        hiddenField2.name = 'product_name';
        hiddenField2.value = productName;
        form.appendChild(hiddenField2);

        var hiddenField3 = document.createElement('input');
        hiddenField3.type = 'hidden';
        hiddenField3.name = 'product_price';
        hiddenField3.value = productPrice;
        form.appendChild(hiddenField3);

        var hiddenField4 = document.createElement('input');
        hiddenField4.type = 'hidden';
        hiddenField4.name = 'action';
        hiddenField4.value = 'add_to_cart';
        form.appendChild(hiddenField4);

        document.body.appendChild(form);
        form.submit(); // Gửi form
    }
</script>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: scale(1.05);
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
    }
    .card-img-top {
        border-radius: 10px;
        object-fit: cover;
        height: 250px;
    }
    .card-footer {
        border-top: none;
        padding-top: 0;
    }
    h2 {
        font-weight: 700;
        font-size: 32px;
    }
    .btn-block {
        margin-bottom: 5px;
    }
</style>
