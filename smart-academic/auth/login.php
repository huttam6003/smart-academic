<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Log activity
        $log = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
        $action = "User logged in";
        $log->bind_param("is", $user['id'], $action);
        $log->execute();

        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../student/dashboard.php");
        }
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid email or password!";
        header("Location: ../index.php");
        exit;
    }
}
?>
