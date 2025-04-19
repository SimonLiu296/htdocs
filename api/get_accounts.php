<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // 获取所有账号数据
    $stmt = $pdo->query("SELECT * FROM accounts ORDER BY id DESC");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'code' => 0,
        'msg' => '',
        'data' => $accounts
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '获取数据失败',
        'data' => []
    ]);
}
?> 