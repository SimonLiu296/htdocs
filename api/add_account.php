<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['code' => 1, 'msg' => '未登录']);
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['remark'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    // 检查备注是否已存在
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE remark = ?");
    $stmt->execute([$_POST['remark']]);
    $existingAccount = $stmt->fetch();
    
    if ($existingAccount) {
        echo json_encode(['code' => 1, 'msg' => '该账号备注已存在']);
        exit;
    }
    
    // 添加新账号
    $stmt = $pdo->prepare("INSERT INTO accounts (remark) VALUES (?)");
    $stmt->execute([$_POST['remark']]);
    
    // 记录日志
    $stmt = $pdo->prepare("INSERT INTO operation_logs (operation_type, operation_detail, operator) VALUES (?, ?, ?)");
    $stmt->execute([
        '添加账号',
        '添加新账号：' . $_POST['remark'],
        $_SESSION['username'] ?? '未知用户'
    ]);
    
    echo json_encode(['code' => 0, 'msg' => '添加成功']);
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '添加失败：' . $e->getMessage()]);
}
?> 