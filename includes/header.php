<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Khởi tạo session nếu chưa có
}
?>

<header>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="py-3">Fashion Shop</h1>
            <div class="ml-3">
                <?php if (isset($_SESSION['user'])): ?>
                    <span class="navbar-text">
                        Xin chào, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                    </span>
                    <form method="POST" action="pages/logout.php" class="d-inline">
                        <button class="btn btn-outline-danger btn-sm" type="submit">Đăng xuất</button>
                    </form>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn btn-outline-primary">Đăng nhập</a>
                <?php endif; ?>
            </div>
        </div>
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=products">Sản phẩm</a>
                    </li>
                    <li class="nav-item dropdown">
                        
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            // Bao gồm tệp kết nối đến SQL Server
                            include './config/db.php'; // Đảm bảo đường dẫn đúng

                            // Truy vấn danh sách danh mục
                            $sql = "SELECT * FROM Categories";
                            $query = sqlsrv_query($conn, $sql);

                            if ($query === false) {
                                die(print_r(sqlsrv_errors(), true)); // Hiển thị lỗi nếu truy vấn không thành công
                            }

                            while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                                echo '<a class="dropdown-item" href="./pages/categoryproduct.php?id=' . htmlspecialchars($row['CategoryID']) . '">' . htmlspecialchars($row['CategoryName']) . '</a>';
                            }
                            ?>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=cart">Giỏ hàng</a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link" href="index.php?page=orders">Đơn hàng</a>
</li>

                </ul>
                <form class="form-inline" action="index.php" method="GET">
                    <input type="hidden" name="page" value="products">
                    
                    <select class="form-control mr-sm-2" name="id">
                        <option value="">Tất cả danh mục</option>
                        <?php
                        // Truy vấn danh sách danh mục
                        $sql = "SELECT * FROM Categories";
                        $query = sqlsrv_query($conn, $sql);

                        if ($query === false) {
                            die(print_r(sqlsrv_errors(), true)); // Hiển thị lỗi nếu truy vấn không thành công
                        }

                        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($row['CategoryID']) . '">' . htmlspecialchars($row['CategoryName']) . '</option>';
                        }
                        ?>
                    </select>

                    <input class="form-control mr-sm-2" type="search" placeholder="Tìm kiếm sản phẩm" aria-label="Search" name="keyword">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Tìm kiếm</button>
                </form>
            </div>
        </nav>
    </div>
</header>

<style>
    /* Google Fonts Import Link */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    header {
        background-color: #f8f9fa; /* Thay đổi màu nền của header */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Thêm bóng đổ cho header */
    }

    .navbar-nav {
        display: flex;
        align-items: center;
    }

    .navbar-nav li {
        list-style: none;
        margin: 0 12px;
    }

    .navbar-nav li a {
        position: relative;
        color: #333;
        font-size: 18px;
        font-weight: 500;
        padding: 6px 0;
        text-decoration: none;
    }

    .navbar-nav li a:before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: #34efdf;
        border-radius: 12px;
        transition: all 0.4s ease;
    }

    .navbar-nav li a:hover:before {
        width: 100%;
    }

    .navbar-brand {
        font-size: 24px;
        font-weight: bold;
        color: #34efdf;
    }

    /* Hiển thị dropdown khi rê chuột */
    .dropdown:hover .dropdown-menu {
        display: block;
    }
</style>
