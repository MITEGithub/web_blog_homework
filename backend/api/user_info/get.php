<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require_once '../../config/db.php';

$headers = getallheaders();
$token = $headers['Authorization']
    ?? $_SERVER['HTTP_AUTHORIZATION']
    ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
    ?? '';

if (!$token) {
    echo json_encode(['error' => '未提供 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "SELECT id, username, email, role, created_at FROM users WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode([
        'code' => 0,
        'msg' => 'success',
        'data' => $user
    ], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(401);  // 设置状态码
    echo json_encode([
        'code' => 1,
        'msg' => '无效 token'
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

