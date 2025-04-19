<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['boss_name']) || !isset($_POST['account_ids'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // 检查老板是否存在，不存在则创建
    $stmt = $pdo->prepare("SELECT id FROM bosses WHERE name = ?");
    $stmt->execute([$_POST['boss_name']]);
    $boss = $stmt->fetch();
    
    if (!$boss) {
        $stmt = $pdo->prepare("INSERT INTO bosses (name) VALUES (?)");
        $stmt->execute([$_POST['boss_name']]);
        $bossId = $pdo->lastInsertId();
    } else {
        $bossId = $boss['id'];
    }
    
    // 添加送心关系
    $stmt = $pdo->prepare("INSERT INTO heart_relations (boss_id, account_id) VALUES (?, ?)");
    foreach ($_POST['account_ids'] as $accountId) {
        $stmt->execute([$bossId, $accountId]);
    }
    
    $pdo->commit();
    echo json_encode(['code' => 0, 'msg' => '添加成功']);
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['code' => 1, 'msg' => '添加失败']);
}
?> 