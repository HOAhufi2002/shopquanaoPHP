<?php
session_start(); // Khởi tạo session nếu chưa có

// Xóa tất cả các biến session
$_SESSION = [];

// Nếu muốn xóa session cookie, có thể thêm đoạn này
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Cuối cùng, xóa session
session_destroy(); // Hủy phiên làm việc

// Chuyển hướng đến trang chủ
header("Location: http://localhost:8080/qlshop"); // Hoặc có thể dùng "index.php?page=home"
exit();
?>
