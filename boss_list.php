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
    <title>老板一览表</title>
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
        <div class="layui-card-header">老板一览表</div>
        <div class="layui-card-body">
            <table id="bossTable" lay-filter="bossTable"></table>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script>
        layui.use(['table'], function(){
            var table = layui.table;
            
            // 渲染表格
            table.render({
                elem: '#bossTable'
                ,url: 'api/get_bosses.php'
                ,cols: [[
                    {field:'id', title: 'ID', width: 80, sort: true}
                    ,{field:'name', title: '老板名称', width: 200}
                    ,{field:'account_count', title: '送心账号数', width: 120, sort: true}
                    ,{field:'created_at', title: '创建时间', width: 180, sort: true}
                    ,{title: '操作', width: 120, toolbar: '#tableToolbar'}
                ]]
                ,page: true
            });

            // 监听工具条
            table.on('tool(bossTable)', function(obj){
                var data = obj.data;
                if(obj.event === 'view'){
                    // 跳转到对应老板的送心管理页面
                    window.location.href = 'heart_view.php?boss_id=' + data.id;
                }
            });
        });
    </script>

    <!-- 表格操作按钮模板 -->
    <script type="text/html" id="tableToolbar">
        <a class="layui-btn layui-btn-xs" lay-event="view">查看送心</a>
    </script>
</body>
</html> 