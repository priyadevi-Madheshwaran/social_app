<?php
session_start();
include "connect.php";
header('Content-Type: application/json');

$type = $_POST['type'] ?? '';

if ($type == 'register') {

    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $sql->bind_param("ss", $username, $email);
    $sql->execute();
    $sql->store_result();

    if ($sql->num_rows > 0) {
        echo json_encode(['status'=>'error', 'message'=>'Username or Email already exists']);
        exit;
    }

    $sql = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $username, $email, $password);

    if ($sql->execute()) {
        echo json_encode(['status'=>'success', 'message'=>'Registration successfully']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Registration failed']);
    }

    exit;
} else if ($type == 'login') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();

    $result = $sql->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            echo json_encode(['status'=>'success', 'message'=>'Login successfully']);

        } else {
            echo json_encode(['status'=>'error', 'message'=>'Invalid password']);
        }

    } else {
        echo json_encode(['status'=>'error', 'message'=>'User not found']);
    }
    exit;
} else if ($type == 'change_password') {

    $username     = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    $sql = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(['status'=>'error', 'message'=>'User not found']);
        exit;
    }

    $user = $result->fetch_assoc();
    $user_id = $user['id'];
    $user_password = $user['password'];

    if (!password_verify($old_password, $user_password)) {
        echo json_encode(['status'=>'error', 'message'=>'Old password incorrect']);
        exit;
    }

    $sql = $conn->prepare("SELECT old_password FROM user_password_logs WHERE user_id = ? ORDER BY id DESC LIMIT 3");
    $sql->bind_param("i", $user_id);
    $sql->execute();
    $res = $sql->get_result();

    while ($row = $res->fetch_assoc()) {
        if (password_verify($new_password, $row['old_password'])) {
            echo json_encode([
                'status'=>'error',
                'message'=>'This password was recently used. Please choose a new one'
            ]);
            exit;
        }
    }

    if (password_verify($new_password, $user_password)) {
        echo json_encode([
            'status'=>'error',
            'message'=>'New password should not match your current password'
        ]);
        exit;
    }

    $sql = $conn->prepare("INSERT INTO user_password_logs (user_id, old_password) VALUES (?, ?)");
    $sql->bind_param("is", $user_id, $user_password);
    $sql->execute();

    $password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $sql->bind_param("si", $password, $user_id);

    if ($sql->execute()) {
        echo json_encode(['status'=>'success', 'message'=>'Password changed successfully']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Password update failed']);
    }

    exit;
}

echo json_encode(['status'=>'error', 'message'=>'Invalid request']);
exit;
?>
