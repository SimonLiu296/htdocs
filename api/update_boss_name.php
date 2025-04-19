<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['name'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE bosses SET name = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['id']]);
    
    echo json_encode(['code' => 0, 'msg' => '更新成功']);
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '更新失败']);
}
?> 