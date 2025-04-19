<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // 这里应该使用更安全的密码验证方式
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        
        // 记录登录日志
        $stmt = $pdo->prepare("INSERT INTO operation_logs (operation_type, operation_detail, operator) VALUES (?, ?, ?)");
        $stmt->execute(['登录', '用户登录系统', $username]);
        
        header('Location: index.php');
        exit;
    } else {
        header('Location: login.php?error=1');
        exit;
    }
}
?> 