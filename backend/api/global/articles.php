<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pageSize = 8;
$offset = ($page - 1) * $pageSize;

$sql = "SELECT a.id, a.title, a.summary, a.created_at, a.views, a.likes, u.username AS author
        FROM articles a
        JOIN users u ON a.author_id = u.id
        ORDER BY a.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pageSize, $offset);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

echo json_encode($articles, JSON_UNESCAPED_UNICODE);

