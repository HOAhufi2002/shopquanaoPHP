<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa có
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý nếu form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra sự tồn tại của các giá trị trong POST trước khi sử dụng
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : null;
    $product_price = isset($_POST['product_price']) ? $_POST['product_price'] : null;

    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user'])) {
        // Hiển thị thông báo nếu chưa đăng nhập
        echo "<script>
            alert('Bạn phải đăng nhập để thêm sản phẩm vào giỏ hàng hoặc đặt hàng.');
            window.location.href = 'index.php?page=login'; // Chuyển đến trang đăng nhập
        </script>";
        exit();
    }

    // Nếu các thông tin sản phẩm đầy đủ
    if ($product_id && $product_name && $product_price) {
        // Nếu hành động là xóa sản phẩm khỏi giỏ hàng
        if (isset($_POST['action']) && $_POST['action'] == 'remove') {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['product_id'] == $product_id) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['message'] = "Xóa sản phẩm thành công!";
                    break;
                }
            }
        } else {
            // Kiểm tra sản phẩm có trong giỏ hàng chưa
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            // Nếu chưa có, thêm sản phẩm vào giỏ
            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product_id,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'quantity' => 1
                ];
            }
        }
    }

    // Xử lý đặt hàng
    if (isset($_POST['action']) && $_POST['action'] == 'place_order') {
        // Kiểm tra xem giỏ hàng có trống hay không
        if (empty($_SESSION['cart'])) {
            echo "<script>alert('Giỏ hàng của bạn trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');</script>";
            exit();
        }

        // Kết nối đến cơ sở dữ liệu
        include 'config/db.php';

        // Thêm đơn hàng vào bảng Orders
        $customerId = $_SESSION['user']['id'];
        $totalAmount = 0;

        foreach ($_SESSION['cart'] as $item) {
            $totalAmount += $item['product_price'] * $item['quantity'];
        }

        $orderSql = "INSERT INTO Orders (CustomerID, TotalAmount) VALUES (?, ?)";
        $orderParams = [$customerId, $totalAmount];
        $orderStmt = sqlsrv_query($conn, $orderSql, $orderParams);

        if ($orderStmt) {
            // Lấy ID của đơn hàng vừa thêm
            $orderIdSql = "SELECT MAX(OrderID) AS LastOrderID FROM Orders";
            $orderIdStmt = sqlsrv_query($conn, $orderIdSql);
            $orderIdRow = sqlsrv_fetch_array($orderIdStmt, SQLSRV_FETCH_ASSOC);
            $orderId = $orderIdRow['LastOrderID']; // Sửa lại để lấy LastOrderID

            // Thêm chi tiết đơn hàng vào bảng OrderDetails
            foreach ($_SESSION['cart'] as $item) {
                $orderDetailSql = "INSERT INTO OrderDetails (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)";
                $orderDetailParams = [$orderId, $item['product_id'], $item['quantity'], $item['product_price']];
                $orderDetailStmt = sqlsrv_query($conn, $orderDetailSql, $orderDetailParams);
                
                // Kiểm tra lỗi khi thêm chi tiết đơn hàng
                if ($orderDetailStmt === false) {
                    echo "<script>alert('Có lỗi xảy ra khi thêm chi tiết đơn hàng: " . print_r(sqlsrv_errors(), true) . "');</script>";
                    break; // Dừng vòng lặp nếu có lỗi
                }
            }

            // Xóa giỏ hàng sau khi đặt hàng
            unset($_SESSION['cart']);
            
            // Đưa thông tin đơn hàng ra console
            echo "<script>
                console.log('Đặt hàng thành công! Đơn hàng ID: $orderId');
                console.log('Chi tiết đơn hàng:', " . json_encode($_SESSION['cart']) . ");
                alert('Đặt hàng thành công! Đơn hàng ID: $orderId'); 
                window.location.href = 'index.php?page=home'; 
            </script>";
            exit();
        } else {
            echo "<script>alert('Có lỗi xảy ra trong quá trình đặt hàng.');</script>";
        }
    }
}
?>

<div class="container cart-container">
    <h2>Giỏ hàng của bạn</h2>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng cộng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo number_format($item['product_price'], 2); ?> VND</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['product_price'] * $item['quantity'], 2); ?> VND</td>
                        <td>
                            <form method="POST" action="index.php?page=cart">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $item['product_name']; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $item['product_price']; ?>">
                                <button type="submit" name="action" value="remove" class="btn btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total += $item['product_price'] * $item['quantity']; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="3">Tổng cộng</th>
                    <th colspan="2"><?php echo number_format($total, 2); ?> VND</th>
                </tr>
            </tfoot>
        </table>

        <!-- Nút Đặt hàng -->
        <form method="POST" action="index.php?page=cart">
            <input type="hidden" name="action" value="place_order">
            <button type="submit" class="btn btn-success">Đặt hàng</button>
        </form>

    <?php else: ?>
        <p class="text-center">Giỏ hàng trống.</p>
    <?php endif; ?>
</div>

<style>
    .cart-container {
        margin-top: 50px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
    }
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
    .total-row {
        font-weight: bold;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
