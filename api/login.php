<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';

// 记录登录日志
function recordLoginLog($pdo, $username, $status) {
    try {
        $operationType = '登录';
        $operationDetail = sprintf(
            "用户 %s 登录%s", 
            $username,
            $status === 'success' ? '成功' : '失败'
        );

        $stmt = $pdo->prepare("
            INSERT INTO operation_logs 
            (operation_type, operation_detail, operator) 
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([
            $operationType,
            $operationDetail,
            $username
        ]);
        
        // 记录日志成功
        error_log("Login log recorded for user: " . $username . ", status: " . $status);
    } catch (PDOException $e) {
        // 记录错误
        error_log("Failed to record login log: " . $e->getMessage());
    }
}

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'code' => 1,
        'msg' => '非法请求方式'
    ]));
}

// 获取并验证输入
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    die(json_encode([
        'code' => 1,
        'msg' => '用户名和密码不能为空'
    ]));
}

try {
    // 查询用户
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 验证用户是否存在
    if (!$user) {
        // 记录登录失败日志（用户不存在）
        recordLoginLog($pdo, $username, 'failed');
        
        die(json_encode([
            'code' => 1,
            'msg' => '用户名或密码错误'
        ]));
    }

    // 验证密码
    if ($password !== $user['password']) {
        // 记录登录失败日志（密码错误）
        recordLoginLog($pdo, $username, 'failed');
        
        die(json_encode([
            'code' => 1,
            'msg' => '用户名或密码错误'
        ]));
    }

    // 登录成功，设置会话
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    // 更新最后登录时间
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // 记录登录成功日志
    recordLoginLog($pdo, $username, 'success');

    // 返回成功响应
    echo json_encode([
        'code' => 0,
        'msg' => '登录成功',
        'data' => [
            'username' => $user['username']
        ]
    ]);

} catch (PDOException $e) {
    // 记录错误日志
    error_log("Login error: " . $e->getMessage());
    
    echo json_encode([
        'code' => 1,
        'msg' => '系统错误，请稍后重试'
    ]);
}
?>