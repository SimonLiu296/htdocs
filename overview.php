<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/database.php';

// 获取账号总数
$stmt = $pdo->query("SELECT COUNT(*) FROM accounts");
$total_accounts = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据总览</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <style>
        .layui-card {
            margin: 15px;
        }
        .layui-tab {
            margin: 15px;
        }
        .header-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .refresh-btn {
            margin: 0;
        }
        .layui-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tab-title {
            font-size: 16px;
            font-weight: bold;
            margin-right: 30px;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        /* 隐藏表格底部滚动条 */
        .layui-table-body::-webkit-scrollbar {
            display: none;
        }
        .layui-table-body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* 确保分页控件完整显示 */
        .layui-table-page {
            position: relative;
            padding: 10px 0;
            text-align: center;
            background-color: #fff;
        }
        .layui-table-view {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="layui-tab">
        <div class="layui-card">
            <div class="layui-card-header">
                <div class="header-left">
                    <span class="tab-title">账号管理</span>
                    <div class="header-flex">
                        <button class="layui-btn layui-btn-sm refresh-btn" id="refresh">刷新数据</button>
                        <span class="layui-badge layui-bg-blue"><?php echo $total_accounts; ?>个账号</span>
                    </div>
                </div>
            </div>
            <div class="layui-card-body">
                <table id="dataTable" lay-filter="dataTable"></table>
            </div>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script>
        layui.use(['table', 'jquery', 'element'], function(){
            var table = layui.table;
            var $ = layui.jquery;
            var element = layui.element;
            
            // 渲染表格
            table.render({
                elem: '#dataTable'
                ,url: 'api/get_accounts.php'
                ,cols: [[
                    {field:'remark', title: '账号备注', width: 200}
                    ,{field:'candles', title: '蜡烛数量', width: 120, sort: true}
                    ,{field:'hearts', title: '爱心数量', width: 120, sort: true}
                    ,{field:'season_candles', title: '季蜡数量', width: 120, sort: true}
                    ,{field:'ascended_candles', title: '升华数量', width: 120, sort: true}
                ]]
                ,page: {
                    layout: ['prev', 'page', 'next', 'count', 'skip', 'limit']
                    ,groups: 5
                    ,first: false
                    ,last: false
                }
                ,limit: 10
                ,limits: [10, 20, 30]
            });
            
            // 刷新按钮点击事件
            $('#refresh').click(function(){
                table.reload('dataTable');
            });
        });
    </script>
</body>
</html> 