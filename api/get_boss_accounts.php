<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['code' => 1, 'msg' => '未登录']));
}

$boss_id = isset($_GET['boss_id']) ? intval($_GET['boss_id']) : 0;
if ($boss_id <= 0) {
    die(json_encode(['code' => 1, 'msg' => '参数错误']));
}

try {
    // 查询该老板的所有送心账号及次数
    $sql = "SELECT 
            a.remark as account_remark,
            COUNT(*) as heart_count,
            MAX(hr.created_at) as last_heart_time
            FROM heart_relations hr
            INNER JOIN accounts a ON hr.account_id = a.id
            WHERE hr.boss_id = ?
            GROUP BY a.id, a.remark
            ORDER BY heart_count DESC, last_heart_time DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$boss_id]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'code' => 0,
        'msg' => '',
        'count' => count($data),
        'data' => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '获取数据失败：' . $e->getMessage()
    ]);
} 