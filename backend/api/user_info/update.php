<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();
$token = $headers['Authorization'] ?? '';

if (!$token) {
    echo json_encode(['error' => '未提供 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$currentPassword = $data['current_password'] ?? '';
$newPassword = $data['new_password'] ?? '';
$confirmPassword = $data['confirm_password'] ?? '';

if (!$currentPassword || !$newPassword || !$confirmPassword) {
    echo json_encode(['error' => '请填写完整的密码信息'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['error' => '两次输入的新密码不一致'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = $conn->prepare("SELECT id, password FROM users WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['error' => '无效 token'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_result($userId, $hashedPassword);
$stmt->fetch();

if (!password_verify($currentPassword, $hashedPassword)) {
    echo json_encode(['error' => '当前密码不正确'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->close();

$newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
$updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$updateStmt->bind_param("si", $newHashed, $userId);

if ($updateStmt->execute()) {
    echo json_encode(['message' => '密码修改成功'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => '密码修改失败'], JSON_UNESCAPED_UNICODE);
}

$updateStmt->close();
$conn->close();
?>

