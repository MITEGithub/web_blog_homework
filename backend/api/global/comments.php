<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$sql = "SELECT c.id, c.content, c.created_at, u.username AS user, a.title AS article_title
        FROM comments c
        JOIN users u ON c.user_id = u.id
        JOIN articles a ON c.article_id = a.id
        ORDER BY c.created_at DESC
        LIMIT 10";

$result = $conn->query($sql);

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments, JSON_UNESCAPED_UNICODE);

