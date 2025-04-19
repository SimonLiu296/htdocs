<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // 获取所有老板及其送心账号数量
    $sql = "SELECT b.id, b.name, b.created_at, 
            COUNT(hr.id) as account_count
            FROM bosses b
            LEFT JOIN heart_relations hr ON b.id = hr.boss_id
            GROUP BY b.id
            ORDER BY b.created_at DESC";
    
    $stmt = $pdo->query($sql);
    $bosses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'code' => 0,
        'msg' => '获取成功',
        'data' => $bosses
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '获取失败：' . $e->getMessage()
    ]);
}
?> 