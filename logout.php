<?php
session_start();

// 记录退出日志
if (isset($_SESSION['username'])) {
    require_once 'config/database.php';
    $stmt = $pdo->prepare("INSERT INTO operation_logs (operation_type, operation_detail, operator) VALUES (?, ?, ?)");
    $stmt->execute(['退出', '用户退出系统', $_SESSION['username']]);
}

// 清除所有会话数据
session_destroy();

// 重定向到登录页面
header('Location: login.php');
exit;
?> 