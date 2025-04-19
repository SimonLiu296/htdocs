<?php
// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 设置响应头
header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['code' => 1, 'msg' => '未登录']);
    exit;
}

require_once '../config/database.php';

// 添加日志记录函数
function addLog($pdo, $operationType, $operationDetail) {
    try {
        $stmt = $pdo->prepare("INSERT INTO operation_logs (operation_type, operation_detail, operator) VALUES (?, ?, ?)");
        $stmt->execute([
            $operationType,
            $operationDetail,
            $_SESSION['username'] ?? '未知用户'
        ]);
    } catch (Exception $e) {
        error_log("日志记录失败: " . $e->getMessage());
    }
}

// 检查必要参数
if (!isset($_POST['boss_name']) || !isset($_POST['account_ids'])) {
    echo json_encode(['code' => 1, 'msg' => '缺少参数']);
    exit;
}

try {
    // 解析account_ids
    $accountIds = json_decode($_POST['account_ids'], true);
    if (!is_array($accountIds) || empty($accountIds)) {
        echo json_encode(['code' => 1, 'msg' => '账号ID格式错误']);
        exit;
    }

    $pdo->beginTransaction();
    
    // 检查老板是否存在，不存在则创建
    $stmt = $pdo->prepare("SELECT id, name FROM bosses WHERE name = ?");
    $stmt->execute([$_POST['boss_name']]);
    $boss = $stmt->fetch();
    
    if (!$boss) {
        $stmt = $pdo->prepare("INSERT INTO bosses (name) VALUES (?)");
        $stmt->execute([$_POST['boss_name']]);
        $bossId = $pdo->lastInsertId();
        $bossName = $_POST['boss_name'];
        // 记录创建老板的日志
        addLog($pdo, '创建老板', '创建新老板：' . $_POST['boss_name']);
    } else {
        $bossId = $boss['id'];
        $bossName = $boss['name'];
    }
    
    // 添加送心关系
    $stmt = $pdo->prepare("INSERT INTO heart_relations (boss_id, account_id) VALUES (?, ?)");
    foreach ($accountIds as $accountId) {
        $stmt->execute([$bossId, $accountId]);
    }
    
    // 记录添加送心关系的日志
    $accountCount = count($accountIds);
    addLog($pdo, '添加送心', "为老板 {$bossName} 添加了 {$accountCount} 个账号的送心关系");
    
    $pdo->commit();
    echo json_encode(['code' => 0, 'msg' => '添加成功']);
} catch(PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // 记录错误日志
    error_log("数据库错误: " . $e->getMessage());
    addLog($pdo, '错误', '添加送心关系失败：' . $e->getMessage());
    echo json_encode(['code' => 1, 'msg' => '添加失败：数据库错误']);
} catch(Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("系统错误: " . $e->getMessage());
    echo json_encode(['code' => 1, 'msg' => '添加失败：系统错误']);
}
?> 