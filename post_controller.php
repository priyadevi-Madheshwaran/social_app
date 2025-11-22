<?php
session_start();
include "connect.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type'] ?? '';


if ($type == "create_post") {

    $content = trim($_POST['content']);
    $visibility = $_POST['visibility'];

    if ($content == "") {
        echo json_encode(["status" => "error", "message" => "Post content is empty"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, visibility) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $content, $visibility);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Post created"]);
    exit;
}


if ($type == "fetch_posts") {

    $stmt = $conn->prepare("
        SELECT p.*, u.username 
        FROM posts p 
        JOIN users u ON p.user_id = u.id
        WHERE p.visibility = 'public' OR p.user_id = ?
        ORDER BY p.id DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode(["status" => "success", "posts" => $posts]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Invalid Request"]);
