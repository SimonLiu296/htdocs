<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['account_id']) || !isset($_POST['candles']) || !isset($_POST['hearts']) || !isset($_POST['season_candles']) || !isset($_POST['ascended_candles'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE accounts SET candles = ?, hearts = ?, season_candles = ?, ascended_candles = ? WHERE id = ?");
    $stmt->execute([
        $_POST['candles'],
        $_POST['hearts'],
        $_POST['season_candles'],
        $_POST['ascended_candles'],
        $_POST['account_id']
    ]);
    
    echo json_encode(['code' => 0, 'msg' => '更新成功']);
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '更新失败']);
}
?> 