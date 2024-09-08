<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa có
}

include '../config/db.php'; // Kết nối đến cơ sở dữ liệu

// Lấy ID đơn hàng từ tham số GET
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$orderId) {
    echo 'Không tìm thấy đơn hàng.';
    exit;
}

// Truy vấn chi tiết đơn hàng
$sql = "SELECT 
            products.ProductName, 
            orderdetails.Price, 
            orderdetails.Quantity 
        FROM 
            products 
        JOIN 
            orderdetails ON products.ProductID = orderdetails.ProductID 
        JOIN 
            orders ON orders.OrderID = orderdetails.OrderID 
        WHERE 
            orders.OrderID = ?";
$params = [$orderId];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$orderDetails = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $orderDetails[] = $row;
}

// Trả về chi tiết đơn hàng dưới dạng HTML để AJAX hiển thị
if (!empty($orderDetails)) {
    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Tên sản phẩm</th><th>Giá (VND)</th><th>Số lượng</th></tr></thead>';
    echo '<tbody>';
    foreach ($orderDetails as $detail) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($detail['ProductName']) . '</td>';
        echo '<td>' . number_format($detail['Price'], 2) . '</td>';
        echo '<td>' . htmlspecialchars($detail['Quantity']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p class="text-center">Không có sản phẩm nào trong đơn hàng này.</p>';
}
?>
