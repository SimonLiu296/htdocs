<?php
$host = 'localhost';
$dbname = 'candle_system';
$username = 'root';
$password = '123456';  // XAMPP 默认 MySQL 密码为空

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("连接失败: " . $e->getMessage());
}
?> 