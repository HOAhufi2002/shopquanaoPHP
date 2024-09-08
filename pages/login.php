<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa có
}
include 'config/db.php'; // Kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin đăng nhập
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Thực hiện truy vấn kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM Customers WHERE Email = ? AND Password = ?";
    $params = [$email, $password]; // Không mã hóa mật khẩu
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt && sqlsrv_has_rows($stmt)) {
        // Nếu đăng nhập thành công
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $_SESSION['user'] = [
            'id' => $user['CustomerID'], // ID người dùng
            'name' => $user['FullName'],  // Lưu tên người dùng vào session
            'email' => $user['Email']      // Lưu email vào session nếu cần
        ];
        echo '<meta http-equiv="refresh" content="0;url=index.php?page=home">';
        exit();
    } else {
        $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Đăng nhập</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
        <a href="index.php?page=register" class="btn btn-link">Đăng ký tài khoản mới</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
