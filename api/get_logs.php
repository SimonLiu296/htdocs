<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../config/database.php';

header('Content-Type: application/json');

try {
    // 获取分页参数
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;
    
    // 获取总记录数
    $countStmt = $pdo->query("SELECT COUNT(*) FROM operation_logs");
    $total = $countStmt->fetchColumn();
    
    // 获取分页数据
    $stmt = $pdo->prepare("SELECT * FROM operation_logs ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'code' => 0,
        'msg' => '',
        'count' => $total,
        'data' => $logs
    ]);
} catch(PDOException $e) {
    echo json_encode(['code' => 1, 'msg' => '获取数据失败']);
}
?> 