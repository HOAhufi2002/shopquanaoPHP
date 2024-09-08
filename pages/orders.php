<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa có
}

if (!isset($_SESSION['user'])) {
    echo "<p class='text-center'>Bạn cần <a href='index.php?page=login'>đăng nhập</a> để xem đơn hàng.</p>";
    exit;
}

// Kết nối đến cơ sở dữ liệu
include 'config/db.php'; 

// Lấy ID người dùng từ session
$userId = $_SESSION['user']['id'];

// Truy vấn các đơn hàng của người dùng
$sql = "SELECT o.OrderID, o.OrderDate, o.TotalAmount 
        FROM Orders o 
        WHERE o.CustomerID = ?";
$params = [$userId];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$orders = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $orders[] = $row;
}
?>

<h2 class="text-center my-5">Danh sách đơn hàng của bạn</h2>

<div class="container">
    <?php if (!empty($orders)) : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Ngày đặt</th>
                    <th>Tổng số tiền (VND)</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                        <td><?php echo htmlspecialchars($order['OrderDate']->format('d-m-Y')); ?></td>
                        <td><?php echo number_format($order['TotalAmount'], 2); ?></td>
                        <td>
                            <a href="javascript:void(0);" class="view-details" data-order-id="<?php echo htmlspecialchars($order['OrderID']); ?>">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Form con để hiển thị chi tiết đơn hàng -->
        <div id="order-details" style="display: none;">
            <h3>Chi tiết đơn hàng</h3>
            <div id="details-content"></div>
        </div>
    <?php else: ?>
        <p class="text-center">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</div>

<script>
// Xử lý sự kiện khi nhấp vào "Xem chi tiết"
document.querySelectorAll('.view-details').forEach(function(link) {
    link.addEventListener('click', function() {
        var orderId = this.getAttribute('data-order-id');
        var detailsDiv = document.getElementById('order-details');
        var detailsContent = document.getElementById('details-content');

        // Gửi yêu cầu AJAX để lấy chi tiết đơn hàng
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'pages/get_order_details.php?order_id=' + orderId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                detailsContent.innerHTML = xhr.responseText;
                detailsDiv.style.display = 'block'; // Hiển thị phần chi tiết đơn hàng
            } else {
                detailsContent.innerHTML = '<p class="text-center">Không thể lấy chi tiết đơn hàng.</p>';
            }
        };
        xhr.send();
    });
});
</script>
