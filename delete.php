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
    <title>删除数据</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <style>
        .layui-card {
            margin: 15px;
        }
        .layui-tab {
            margin: 15px;
        }
    </style>
</head>
<body>
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">账号管理</li>
            <li>老板管理</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-card">
                    <div class="layui-card-header">账号列表</div>
                    <div class="layui-card-body">
                        <table id="accountTable" lay-filter="accountTable"></table>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-card">
                    <div class="layui-card-header">老板列表</div>
                    <div class="layui-card-body">
                        <table id="bossTable" lay-filter="bossTable"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <!-- 添加 jQuery 引入 -->
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script type="text/html" id="accountToolbar">
        <a class="layui-btn layui-btn-xs" lay-event="edit">更改名称</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script type="text/html" id="bossToolbar">
        <a class="layui-btn layui-btn-xs" lay-event="edit">更改名称</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    </script>
    <script>
        layui.use(['table', 'layer', 'element'], function(){
            var table = layui.table;
            var layer = layui.layer;
            var element = layui.element;
            
            // 渲染账号表格
            table.render({
                elem: '#accountTable'
                ,url: 'api/get_accounts.php'
                ,cols: [[
                    {field:'remark', title: '账号备注', width: 200}
                    ,{field:'candles', title: '蜡烛数量', width: 120}
                    ,{field:'hearts', title: '爱心数量', width: 120}
                    ,{field:'season_candles', title: '季蜡数量', width: 120}
                    ,{field:'ascended_candles', title: '升华数量', width: 120}
                    ,{title:'操作', toolbar: '#accountToolbar', width: 150}
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
            
            // 渲染老板表格
            table.render({
                elem: '#bossTable'
                ,url: 'api/get_bosses.php'
                ,cols: [[
                    {field:'name', title: '老板名称', width: 200}
                    ,{title:'操作', toolbar: '#bossToolbar', width: 150}
                ]]
                ,page: {
                    layout: ['prev', 'page', 'next', 'count', 'skip', 'limit']
                    ,groups: 5
                    ,first: false
                    ,last: false
                }
                ,limit: 10
                ,limits: [10, 20, 30, 50]
            });
            
            // 监听工具条
            table.on('tool(accountTable)', function(obj){
                var data = obj.data;
                if(obj.event === 'edit'){
                    layer.prompt({
                        formType: 2,
                        value: data.remark,
                        title: '修改账号备注'
                    }, function(value, index){
                        $.ajax({
                            url: 'api/update_account_remark.php',
                            type: 'POST',
                            data: {
                                id: data.id,
                                remark: value
                            },
                            success: function(res){
                                if(res.code === 0){
                                    layer.msg('修改成功');
                                    table.reload('accountTable');
                                    // 记录操作日志
                                    $.ajax({
                                        url: 'api/add_log.php',
                                        type: 'POST',
                                        data: {
                                            operation_type: '修改',
                                            operation_detail: '修改账号备注：' + data.remark + ' -> ' + value,
                                            operator: '<?php echo $_SESSION['username']; ?>'
                                        }
                                    });
                                } else {
                                    layer.msg('修改失败：' + res.msg);
                                }
                            }
                        });
                        layer.close(index);
                    });
                } else if(obj.event === 'del'){
                    layer.prompt({
                        formType: 2,
                        title: '确认删除',
                        value: '我确认'
                    }, function(value, index){
                        if(value === '我确认'){
                            $.ajax({
                                url: 'api/delete_account.php',
                                type: 'POST',
                                data: {id: data.id},
                                success: function(res){
                                    if(res.code === 0){
                                        layer.msg('删除成功');
                                        table.reload('accountTable');
                                        // 记录操作日志
                                        $.ajax({
                                            url: 'api/add_log.php',
                                            type: 'POST',
                                            data: {
                                                operation_type: '删除',
                                                operation_detail: '删除账号：' + data.remark,
                                                operator: '<?php echo $_SESSION['username']; ?>'
                                            }
                                        });
                                    } else {
                                        layer.msg('删除失败：' + res.msg);
                                    }
                                }
                            });
                        } else {
                            layer.msg('请输入正确的确认文字');
                        }
                        layer.close(index);
                    });
                }
            });
            
            table.on('tool(bossTable)', function(obj){
                var data = obj.data;
                if(obj.event === 'edit'){
                    layer.prompt({
                        formType: 2,
                        value: data.name,
                        title: '修改老板名称'
                    }, function(value, index){
                        $.ajax({
                            url: 'api/update_boss_name.php',
                            type: 'POST',
                            data: {
                                id: data.id,
                                name: value
                            },
                            success: function(res){
                                if(res.code === 0){
                                    layer.msg('修改成功');
                                    table.reload('bossTable');
                                    // 记录操作日志
                                    $.ajax({
                                        url: 'api/add_log.php',
                                        type: 'POST',
                                        data: {
                                            operation_type: '修改',
                                            operation_detail: '修改老板名称：' + data.name + ' -> ' + value,
                                            operator: '<?php echo $_SESSION['username']; ?>'
                                        }
                                    });
                                } else {
                                    layer.msg('修改失败：' + res.msg);
                                }
                            }
                        });
                        layer.close(index);
                    });
                } else if(obj.event === 'del'){
                    layer.prompt({
                        formType: 2,
                        title: '确认删除',
                        value: '我确认'
                    }, function(value, index){
                        if(value === '我确认'){
                            $.ajax({
                                url: 'api/delete_boss.php',
                                type: 'POST',
                                data: {id: data.id},
                                success: function(res){
                                    if(res.code === 0){
                                        layer.msg('删除成功');
                                        table.reload('bossTable');
                                        // 记录操作日志
                                        $.ajax({
                                            url: 'api/add_log.php',
                                            type: 'POST',
                                            data: {
                                                operation_type: '删除',
                                                operation_detail: '删除老板：' + data.name,
                                                operator: '<?php echo $_SESSION['username']; ?>'
                                            }
                                        });
                                    } else {
                                        layer.msg('删除失败：' + res.msg);
                                    }
                                }
                            });
                        } else {
                            layer.msg('请输入正确的确认文字');
                        }
                        layer.close(index);
                    });
                }
            });
        });
    </script>
</body>
</html> 