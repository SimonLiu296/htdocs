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
    
    // 先检查表是否存在
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('heart_relations', $tables) || !in_array('bosses', $tables) || !in_array('accounts', $tables)) {
        throw new Exception('必要的数据表不存在');
    }
    
    // 获取总数
    $countStmt = $pdo->query("
        SELECT COUNT(*) 
        FROM heart_relations hr
        JOIN bosses b ON hr.boss_id = b.id
        JOIN accounts a ON hr.account_id = a.id
    ");
    $total = $countStmt->fetchColumn();
    
    // 获取分页数据 - 修改这里的 SQL 语句
    $sql = sprintf("
        SELECT 
            b.name as boss_name, 
            a.remark as account_remark,
            hr.created_at as relation_date
        FROM heart_relations hr
        JOIN bosses b ON hr.boss_id = b.id
        JOIN accounts a ON hr.account_id = a.id
        ORDER BY hr.created_at DESC
        LIMIT %d OFFSET %d
    ", $limit, $offset);
    
    $stmt = $pdo->query($sql);
    $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 如果没有数据，返回空数组而不是错误
    if (empty($relations)) {
        echo json_encode([
            'code' => 0,
            'msg' => '',
            'count' => 0,
            'data' => []
        ]);
        exit;
    }
    
    // 获取所有关系用于图形展示
    $allStmt = $pdo->query("
        SELECT 
            b.name as boss_name, 
            a.remark as account_remark
        FROM heart_relations hr
        JOIN bosses b ON hr.boss_id = b.id
        JOIN accounts a ON hr.account_id = a.id
        ORDER BY b.name, a.remark
    ");
    
    $allRelations = $allStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 处理图形数据
    $nodes = [];
    $links = [];
    $categories = [
        ['name' => '老板'],
        ['name' => '账号']
    ];
    
    $bossMap = [];
    $accountMap = [];
    
    foreach ($allRelations as $relation) {
        // 处理老板节点
        if (!isset($bossMap[$relation['boss_name']])) {
            $bossId = count($nodes);
            $bossMap[$relation['boss_name']] = $bossId;
            $nodes[] = [
                'id' => $bossId,
                'name' => $relation['boss_name'],
                'category' => 0,
                'symbolSize' => 40,
                'itemStyle' => [
                    'color' => '#c23531'
                ]
            ];
        }
        
        // 处理账号节点
        if (!isset($accountMap[$relation['account_remark']])) {
            $accountId = count($nodes);
            $accountMap[$relation['account_remark']] = $accountId;
            $nodes[] = [
                'id' => $accountId,
                'name' => $relation['account_remark'],
                'category' => 1,
                'symbolSize' => 30,
                'itemStyle' => [
                    'color' => '#2f4554'
                ]
            ];
        }
        
        // 添加关系连线
        $links[] = [
            'source' => $bossMap[$relation['boss_name']],
            'target' => $accountMap[$relation['account_remark']]
        ];
    }
    
    echo json_encode([
        'code' => 0,
        'msg' => '',
        'count' => $total,
        'data' => $relations
    ]);
    
} catch(Exception $e) {
    // 输出详细错误信息
    echo json_encode([
        'code' => 1, 
        'msg' => '获取数据失败: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} catch(PDOException $e) {
    // 输出数据库错误信息
    echo json_encode([
        'code' => 1, 
        'msg' => '数据库错误: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?> 