<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <style>
        .layui-layout-admin .layui-logo {
            background-color: #1a365d;
            color: #ffffff;
            font-weight: bold;
            display: flex;
            align-items: center;
            padding: 0;
            transition: all 0.3s ease;
        }
        .layui-layout-admin .layui-logo img {
            width: 25px;
            height: 25px;
            margin: 0 15px;
            vertical-align: middle;
            flex-shrink: 0;
        }
        .layui-layout-admin .layui-logo span {
            white-space: nowrap;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .layui-side-collapsed .layui-logo span {
            display: none;
        }
        .layui-layout-admin .layui-side {
            background-color: #2c3e50;
            transition: all 0.3s ease;
            width: 200px;
        }
        .layui-layout-admin .layui-body {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            left: 200px;
        }
        .layui-nav {
            background-color: #1a365d;
        }
        .layui-nav-tree {
            width: 100%;
        }
        .layui-nav-tree .layui-nav-item {
            width: 100%;
            margin: 0;
        }
        .layui-nav-tree .layui-nav-item a {
            color: #e0e0e0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 15px;
            height: 50px;
            line-height: 50px;
        }
        .layui-nav-tree .layui-nav-item a:hover {
            color: #ffffff;
            background-color: #2c3e50;
        }
        .layui-nav-tree .layui-nav-child dd.layui-this, 
        .layui-nav-tree .layui-nav-child dd.layui-this a, 
        .layui-nav-tree .layui-this, 
        .layui-nav-tree .layui-this>a, 
        .layui-nav-tree .layui-this>a:hover {
            background-color: #3498db;
        }
        .layui-badge {
            margin-left: 5px;
            background-color: #3498db;
        }
        .layui-footer {
            background-color: #1a365d;
            color: #ffffff;
        }
        .layui-nav-item {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .layui-nav-tree .layui-nav-child {
            background-color: #34495e;
            padding: 0;
        }
        .layui-nav-tree .layui-nav-child dd {
            margin: 0;
        }
        .layui-nav-tree .layui-nav-child dd a {
            padding-left: 30px;
        }
        /* 新增折叠动画效果 */
        .layui-nav-tree .layui-nav-item a span {
            display: inline-block;
            transition: all 0.3s ease;
            opacity: 1;
            margin-left: 5px;
        }
        .layui-side-collapsed .layui-nav-item a span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        #sidebar-toggle {
            margin: 0 15px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .layui-side-collapsed {
            width: 60px !important;
        }
        .layui-side-collapsed + .layui-body {
            left: 60px !important;
        }
    </style>
</head>
<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <div class="layui-logo">
                <img src="./favicon.ico" alt="logo">
                <span>数据管理系统</span>
                <i class="layui-icon layui-icon-shrink-right" id="sidebar-toggle" onclick="toggleSidebar()"></i>
            </div>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;">
                        <img src="./images/head.jpg" class="layui-nav-img">
                        <?php echo $_SESSION['username']; ?>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="logout.php">退出登录</a></dd>
                    </dl>
                </li>
            </ul>
        </div>

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <ul class="layui-nav layui-nav-tree">
                    <li class="layui-nav-item">
                        <a href="overview.php" target="mainFrame"><i class="layui-icon layui-icon-chart"></i>&ensp;数据总览</a>
                    </li>
                    <li class="layui-nav-item">
                        <a href="edit.php" target="mainFrame"><i class="layui-icon layui-icon-edit"></i>&ensp;修改数据</a>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;"><i class="layui-icon layui-icon-heart"></i>&ensp;送心管理</a>
                        <dl class="layui-nav-child">
                            <dd><a href="heart_view.php" target="mainFrame"><i class="layui-icon layui-icon-search"></i>&ensp;送心查看</a></dd>
                            <dd><a href="heart_add.php" target="mainFrame"><i class="layui-icon layui-icon-add-1"></i>&ensp;送心添加</a></dd>
                            <dd><a href="boss_list.php" target="mainFrame"><i class="layui-icon layui-icon-user"></i>&ensp;老板一览</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="add.php" target="mainFrame"><i class="layui-icon layui-icon-add-circle"></i>&ensp;添加数据</a>
                    </li>
                    <li class="layui-nav-item">
                        <a href="delete.php" target="mainFrame"><i class="layui-icon layui-icon-delete"></i>&ensp;删除数据</a>
                    </li>
                    <li class="layui-nav-item">
                        <a href="logs.php" target="mainFrame"><i class="layui-icon layui-icon-list"></i>&ensp;操作记录</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="layui-body">
            <iframe name="mainFrame" src="overview.php" frameborder="0" style="width: 100%; height: 100%;"></iframe>
        </div>

        <div class="layui-footer">
            <div style="text-align: center; color: #666; padding: 10px;">
                Copyright © 2024-2025 数据管理系统 Powered & Designed by 晨风 All Rights Reserved 
            </div>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <!-- 替换为本地文件 -->
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script>
        function toggleSidebar() {
            var side = document.querySelector('.layui-side');
            var body = document.querySelector('.layui-body');
            var icon = document.getElementById('sidebar-toggle');
            
            if(side.style.width === '60px' || side.offsetWidth === 60) {
                // 展开状态
                side.style.width = '200px';
                body.style.left = '200px';
                icon.className = 'layui-icon layui-icon-shrink-right';
                side.classList.remove('layui-side-collapsed');
                
                // 恢复所有菜单文字
                var menuItems = document.querySelectorAll('.layui-nav-tree .layui-nav-item a');
                menuItems.forEach(function(item){
                    var originalText = item.getAttribute('data-text');
                    if (originalText) {
                        var icon = item.querySelector('i').outerHTML;
                        item.innerHTML = icon + ' <span>' + originalText + '</span>';
                    }
                });
            } else {
                // 折叠状态
                side.style.width = '60px';
                body.style.left = '60px';
                icon.className = 'layui-icon layui-icon-spread-left';
                side.classList.add('layui-side-collapsed');
                
                // 保存并隐藏所有菜单文字
                var menuItems = document.querySelectorAll('.layui-nav-tree .layui-nav-item a');
                menuItems.forEach(function(item){
                    // 保存原始文字
                    if (!item.getAttribute('data-text')) {
                        var text = item.textContent.trim();
                        item.setAttribute('data-text', text);
                    }
                    // 只显示图标
                    var icon = item.querySelector('i').outerHTML;
                    item.innerHTML = icon + ' <span>' + item.getAttribute('data-text') + '</span>';
                });
            }
        }
    </script>
</body>
</html> 