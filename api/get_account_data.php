<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($account) {
        echo json_encode(['code' => 0, 'data' => $account]);
    } else {
        echo json_encode(['code' => 1, 'msg' => '账号不存在']);
    }
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '获取数据失败']);
}
?> 