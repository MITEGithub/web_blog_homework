<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

require_once(__DIR__ . '/../config/db.php');  
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$username = isset($data["username"]) ? trim($data["username"]) : null;
$email = isset($data["email"]) ? trim($data["email"]) : null;
$password = isset($data["password"]) ? trim($data["password"]) : null;

if ((!$username && !$email) || !$password) {
    echo json_encode(["error" => "请提供用户名或邮箱和密码"]);
    exit;
}

if ($username) {
    $stmt = mysqli_prepare($conn, "SELECT id, password, token FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
} else {
    $stmt = mysqli_prepare($conn, "SELECT id, password, token FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $user['password'])) {
        echo json_encode(["token" => $user['token']]);
    } else {
        echo json_encode(["error" => "密码错误"]);
    }
} else {
    echo json_encode(["error" => "用户不存在"]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

