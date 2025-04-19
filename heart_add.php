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
    <title>送心添加</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/xm-select.js"></script>
    <style>
        .layui-card {
            margin: 15px;
        }
        .layui-form-item {
            margin-bottom: 25px;
        }
        .layui-form-label {
            width: 100px;
        }
        .layui-input-block {
            margin-left: 130px;
        }
        /* 添加选择器容器样式 */
        #mySelect {
            width: 100%;
            min-height: 38px;
        }
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">送心添加</div>
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="heartForm">
                <div class="layui-form-item">
                    <label class="layui-form-label">老板名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="boss_name" required lay-verify="required" 
                               placeholder="请输入老板名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">账号备注</label>
                    <div class="layui-input-block">
                        <!-- 修改选择器容器 -->
                        <div id="mySelect"></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="save">保存</button>
                        <button type="reset" class="layui-btn layui-btn-primary" id="resetBtn">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
            var accountSelect = null;

            // 初始化xm-select
            function initXmSelect() {
                // 先获取账号数据
                $.get('api/get_accounts.php', function(res) {
                    if(res.code === 0 && Array.isArray(res.data)) {
                        try {
                            // 准备选项数据
                            var options = res.data.map(function(account) {
                                return {
                                    name: account.remark,
                                    value: account.id
                                };
                            });

                            // 使用正确的初始化方式
                            accountSelect = xmSelect.render({
                                el: '#mySelect', 
                                data: options,
                                filterable: true,
                                height: '300px',
                                theme: {
                                    color: '#009688',
                                },
                                tips: '请选择账号',
                                empty: '暂无数据',
                                searchTips: '搜索账号',
                                toolbar: {
                                    show: true,
                                    list: ['ALL', 'CLEAR', 'REVERSE']
                                }
                            });

                        } catch (error) {
                            console.error('初始化选择器失败:', error);
                            layer.msg('初始化选择器失败：' + error.message);
                        }
                    } else {
                        layer.msg('获取账号列表失败：' + (res.msg || '未知错误'));
                    }
                });
            }

            // 页面加载完成后初始化选择器
            $(document).ready(function() {
                initXmSelect();
            });
            
            // 监听提交
            form.on('submit(save)', function(data){
                if (!accountSelect) {
                    layer.msg('账号选择器未初始化');
                    return false;
                }

                var selectedAccounts = accountSelect.getValue('value');
                if(!selectedAccounts || selectedAccounts.length === 0){
                    layer.msg('请选择至少一个账号');
                    return false;
                }
                
                $.ajax({
                    url: 'api/add_heart_relation.php',
                    type: 'POST',
                    data: {
                        boss_name: data.field.boss_name,
                        account_ids: selectedAccounts
                    },
                    success: function(res){
                        if(res.code === 0){
                            layer.msg('保存成功');
                            // 重置表单
                            $('form')[0].reset();
                            if (accountSelect) {
                                accountSelect.setValue([]);
                            }
                        } else {
                            layer.msg('保存失败：' + res.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        layer.msg('请求失败：' + error);
                    }
                });
                return false;
            });

            // 重置按钮点击事件
            $('#resetBtn').on('click', function(e){
                e.preventDefault();
                $('form')[0].reset();
                if (accountSelect) {
                    accountSelect.setValue([]);
                }
            });
        });
    </script>
</body>
</html> 