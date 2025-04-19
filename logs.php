<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>操作记录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <style>
        .layui-card {
            margin: 15px;
        }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">操作记录</div>
        <div class="layui-card-body">
            <table id="logTable" lay-filter="logTable"></table>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script>
        layui.use(['table'], function(){
            var table = layui.table;
            
            // 渲染表格
            table.render({
                elem: '#logTable'
                ,url: 'api/get_logs.php'
                ,cols: [[
                    {field:'created_at', title: '操作时间', width: 180}
                    ,{field:'operation_type', title: '操作类型', width: 120}
                    ,{field:'operation_detail', title: '操作详情'}
                    ,{field:'operator', title: '操作人', width: 120}
                ]]
                ,page: true
            });
        });
    </script>
</body>
</html> 