<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => '只允许 POST 请求'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$email = trim($data['email'] ?? '');

if (!$username || !$password || !$email) {
    echo json_encode(['error' => '参数缺失'], JSON_UNESCAPED_UNICODE);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 生成64位 token
function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}
$token = generateToken();

// 检查用户名是否已存在
$checkSql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['error' => '用户名已存在'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 插入用户
$insertSql = "INSERT INTO users (username, email, password, token) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("ssss", $username, $email, $hashedPassword, $token);

if ($stmt->execute()) {
    echo json_encode(['message' => '注册成功', 'token' => $token], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => '注册失败', 'details' => $stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>

