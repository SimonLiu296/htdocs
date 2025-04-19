<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/database.php';

// 获取老板总数
$stmt = $pdo->query("SELECT COUNT(*) FROM bosses");
$total_bosses = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>送心查看</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/js/jquery-3.7.1.min.js"></script>
    <style>
        .layui-card {
            margin: 15px;
            height: calc(100vh - 30px);
            display: flex;
            flex-direction: column;
        }
        .layui-card-header {
            flex-shrink: 0;
        }
        .layui-card-body {
            flex: 1;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        .header-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .boss-select-container {
            padding: 15px;
        }
        .layui-form-select {
            width: 300px;
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
        /* 使tab内容撑满 */
        .layui-tab {
            margin: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .layui-tab-title {
            margin-bottom: 0;
            background-color: #fff;
            border-bottom: 1px solid #e6e6e6;
        }
        .layui-tab-content {
            padding: 0;
            height: calc(100vh - 150px);
            flex: 1;
            background-color: #fff;
            position: relative;
        }
        .layui-tab-item {
            height: 100%;
            padding: 0;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: none;
        }
        .layui-tab-item.layui-show {
            display: flex;
            flex-direction: column;
        }
        .layui-tab-item > div {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .layui-table-box {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .layui-table-body {
            flex: 1;
        }
        .boss-select-container {
            padding: 15px;
            background-color: #fff;
            border-bottom: 1px solid #f6f6f6;
        }
        /* 送心详情tab页样式 */
        .heart-detail-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
        /* 老板管理tab页样式 */
        .boss-management-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="header-flex">
                <span>送心查看</span>
                <span class="layui-badge layui-bg-blue" id="totalHearts">加载中...</span>
                <span class="layui-badge layui-bg-orange" id="totalBosses">加载中...</span>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li class="layui-this">送心详情</li>
                    <li>老板管理</li>
                </ul>
                <div class="layui-tab-content">
                    <!-- 送心详情 -->
                    <div class="layui-tab-item layui-show">
                        <div class="heart-detail-container">
                            <table id="heartDetailTable" lay-filter="heartDetailTable"></table>
                        </div>
                    </div>
                    
                    <!-- 老板管理 -->
                    <div class="layui-tab-item">
                        <div class="boss-management-container">
                            <div class="boss-select-container">
                                <form class="layui-form">
                                    <div class="layui-form-item">
                                        <select name="boss" lay-filter="bossSelect" lay-search>
                                            <option value="">请选择老板</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div style="flex: 1;">
                                <table id="bossAccountsTable" lay-filter="bossAccountsTable"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script>
        layui.use(['table', 'layer', 'element', 'form'], function(){
            var table = layui.table;
            var layer = layui.layer;
            var element = layui.element;
            var form = layui.form;

            // 获取送心统计
            function loadTotalHearts() {
                $.get('api/get_heart_stats.php', function(res) {
                    if(res.code === 0) {
                        $('#totalHearts').html('总送心数: ' + res.data.total_hearts);
                        $('#totalBosses').html('总老板数: ' + res.data.total_bosses);
                    }
                });
            }

            // 初始化送心详情表格
            table.render({
                elem: '#heartDetailTable',
                url: 'api/get_heart_relations.php',
                page: true,
                cols: [[
                    {field: 'account_remark', title: '送心账号', width: '25%'},
                    {field: 'boss_name', title: '送心老板', width: '75%'}
                ]],
                height: 'full-200',
                limit: 10,
                limits: [10, 20, 30],
                done: function(res) {
                    if(res.code === 1 && res.msg === '未登录') {
                        top.location.href = '/login.php';
                    }
                }
            });

            // 监听分页大小变化
            table.on('toolbar(heartDetailTable)', function(obj){
                if(obj.event === 'LAYTABLE_COLS' || obj.event === 'LAYTABLE_EXPORT' || obj.event === 'LAYTABLE_PRINT') {
                    return;
                }
                // 根据分页大小调整表格高度
                var limit = obj.config.limit;
                var height = 'full-200';
                if(limit === 50) {
                    height = 'full-300';
                } else if(limit === 100) {
                    height = 'full-400';
                }
                table.reload('heartDetailTable', {
                    height: height
                });
            });

            // 初始化老板选择器
            function initBossSelect() {
                $.get('api/get_bosses.php', function(res) {
                    if(res.code === 0 && Array.isArray(res.data)) {
                        var selectElem = $('select[name="boss"]');
                        selectElem.empty().append('<option value="">请选择老板</option>');
                        res.data.forEach(function(boss) {
                            selectElem.append(
                                '<option value="' + boss.id + '">' + boss.name + '</option>'
                            );
                        });
                        form.render('select');
                    } else if(res.code === 1 && res.msg === '未登录') {
                        layer.msg('登录已过期，请重新登录');
                        setTimeout(function() {
                            top.location.href = '/login.php';
                        }, 1500);
                    } else {
                        layer.msg('获取老板列表失败：' + (res.msg || '未知错误'));
                    }
                }).fail(function(xhr, status, error) {
                    layer.msg('请求失败：' + error);
                });
            }

            // 加载老板的送心账号
            function loadBossAccounts(bossId) {
                table.render({
                    elem: '#bossAccountsTable',
                    url: 'api/get_boss_accounts.php',
                    where: { boss_id: bossId },
                    page: true,
                    cols: [[
                        {field: 'account_remark', title: '账号备注', width: '50%'},
                        {field: 'heart_count', title: '送心次数', width: '25%', sort: true},
                        {field: 'last_heart_time', title: '最后送心时间', width: '25%', sort: true}
                    ]],
                    height: 'full-200',
                    limit: 10,
                    limits: [10, 20, 30],
                    done: function(res) {
                        if(res.code === 1) {
                            if(res.msg === '未登录') {
                                layer.msg('登录已过期，请重新登录');
                                setTimeout(function() {
                                    top.location.href = '/login.php';
                                }, 1500);
                            } else {
                                layer.msg(res.msg || '获取数据失败');
                            }
                        }
                    }
                });
            }

            // 监听Tab切换
            element.on('tab(docDemoTabBrief)', function(data){
                if(data.index === 1) {
                    initBossSelect();
                }
            });

            // 监听老板选择
            form.on('select(bossSelect)', function(data){
                if(data.value) {
                    loadBossAccounts(data.value);
                } else {
                    // 清空表格
                    table.reload('bossAccountsTable', {
                        data: []
                    });
                }
            });

            // 页面加载时获取送心总数
            loadTotalHearts();
        });
    </script>
</body>
</html> 