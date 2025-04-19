<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['code' => 1, 'msg' => '未登录']));
}

try {
    // 获取送心总数和老板总数
    $sql = "SELECT 
            (SELECT COUNT(*) FROM heart_relations) as total_hearts,
            (SELECT COUNT(DISTINCT boss_id) FROM heart_relations) as total_bosses,
            (SELECT COUNT(DISTINCT account_id) FROM heart_relations) as total_accounts";
    
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 检查数据是否成功获取
    if ($result === false) {
        throw new PDOException("获取统计数据失败");
    }

    echo json_encode([
        'code' => 0,
        'msg' => '',
        'data' => [
            'total_hearts' => intval($result['total_hearts']),
            'total_bosses' => intval($result['total_bosses']),
            'total_accounts' => intval($result['total_accounts'])
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '获取数据失败：' . $e->getMessage()
    ]);
}
?>
