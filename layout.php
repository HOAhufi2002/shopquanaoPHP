<?php
session_start(); // Khởi tạo session nếu chưa có
include 'config/db.php'; // Đảm bảo file db.php đã được nhúng
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Shop</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Chiều cao tối thiểu là 100% viewport */
        }
        .container {
            flex: 1; /* Chiếm toàn bộ không gian còn lại */
        }
    </style>
</head>
<body>

<!-- Header -->
<?php include 'includes/header.php'; ?>

<!-- Nội dung chính -->
<div class="container">
    <?php
    // Dựa trên tham số URL để tải trang con
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch ($page) {
        case 'products':
            include 'pages/products.php';
            break;
        case 'categories':
            include 'pages/categories.php';
            break;
        case 'cart':
            include 'pages/cart.php';
            break;
        case 'orders':
            include 'pages/orders.php';
            break;
        case 'login':
            include 'pages/login.php'; // Trang đăng nhập
            break;
        case 'register':
            include 'pages/register.php'; // Trang đăng ký
            break;
        default:
            include 'pages/home.php'; // Trang chủ mặc định
    }
    ?>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>
