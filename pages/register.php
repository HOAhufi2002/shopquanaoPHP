<?php
session_start();
include 'config/db.php'; // Kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Thực hiện truy vấn để thêm khách hàng vào cơ sở dữ liệu
    $sql = "INSERT INTO Customers (FullName, Email, Password) VALUES (?, ?, ?)";
    $params = [$fullName, $email, $password]; // Không mã hóa mật khẩu
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $_SESSION['message'] = "Đăng ký thành công! Bạn có thể đăng nhập.";
        header("Location: index.php?page=login");
        exit();
    } else {
        $error = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Đăng ký tài khoản</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="full_name">Họ và tên</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng ký</button>
        <a href="index.php?page=login" class="btn btn-link">Đã có tài khoản? Đăng nhập</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>