<?php
header('Content-Type: application/json; charset=utf-8');
require_once('../../config/db.php');

$sql = "SELECT id, title, likes, views, created_at, (likes + views) AS popularity
        FROM articles
        ORDER BY popularity DESC
        LIMIT 10";

$result = $conn->query($sql);

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

echo json_encode($articles, JSON_UNESCAPED_UNICODE);

