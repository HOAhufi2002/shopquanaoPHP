<?php
$serverName = "localhost";
$connectionOptions = array(
    "Database" => "FashionShop",
    "Uid" => "sa",
    "PWD" => "123",
    "CharacterSet" => "UTF-8"
);

// Thực hiện kết nối
$conn = sqlsrv_connect($serverName, $connectionOptions);

?>
