<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$user = 'root';        // 你的数据库用户名
$pass = '123';            // 你的数据库密码
$dbname = 'blog';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'error' => '数据库连接失败',
        'details' => $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>

