<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // 获取搜索关键词
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // 构建SQL查询
    $sql = "SELECT * FROM bosses";
    if ($search) {
        $sql .= " WHERE name LIKE :search";
    }
    $sql .= " ORDER BY name";
    
    $stmt = $pdo->prepare($sql);
    if ($search) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $bosses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'code' => 0,
        'msg' => '',
        'data' => $bosses
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'code' => 1,
        'msg' => '获取数据失败',
        'data' => []
    ]);
}
?> 