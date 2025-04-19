<?php
require_once 'config/database.php';

try {
    // 创建账号表
    $pdo->exec("CREATE TABLE IF NOT EXISTS accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        remark VARCHAR(255) NOT NULL,
        candles INT DEFAULT 0,
        hearts INT DEFAULT 0,
        season_candles INT DEFAULT 0,
        ascended_candles INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // 创建老板表
    $pdo->exec("CREATE TABLE IF NOT EXISTS bosses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // 创建送心关系表
    $pdo->exec("CREATE TABLE IF NOT EXISTS heart_relations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        boss_id INT NOT NULL,
        account_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (boss_id) REFERENCES bosses(id),
        FOREIGN KEY (account_id) REFERENCES accounts(id)
    )");

    // 创建操作日志表
    $pdo->exec("CREATE TABLE IF NOT EXISTS operation_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        operation_type VARCHAR(50) NOT NULL,
        operation_detail TEXT,
        operator VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "数据库初始化成功！";
} catch(PDOException $e) {
    die("数据库初始化失败: " . $e->getMessage());
}
?> 