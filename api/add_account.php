<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['remark'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO accounts (remark) VALUES (?)");
    $stmt->execute([$_POST['remark']]);
    
    echo json_encode(['code' => 0, 'msg' => '添加成功']);
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '添加失败']);
}
?> 