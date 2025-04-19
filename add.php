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
    <title>添加数据</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
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
    </style>
</head>
<body>
    <div class="layui-card">
        <div class="layui-card-header">添加数据</div>
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="addForm">
                <div class="layui-form-item">
                    <label class="layui-form-label">账号备注</label>
                    <div class="layui-input-block">
                        <input type="text" name="remark" required lay-verify="required" placeholder="请输入账号备注" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="save">保存</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="/layui/layui.js"></script>
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
            
            // 监听提交
            form.on('submit(save)', function(data){
                $.ajax({
                    url: 'api/add_account.php',
                    type: 'POST',
                    dataType: 'json',
                    data: data.field,
                    success: function(res){
                        if(res.code === 0){
                            layer.msg('保存成功', {
                                icon: 1,
                                time: 1500
                            }, function(){
                                // 重置表单
                                $('form')[0].reset();
                                form.render();
                            });
                        } else {
                            layer.msg(res.msg, {
                                icon: 2,
                                time: 2000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('请求失败:', xhr.responseText);
                        layer.msg('请求失败：' + error, {
                            icon: 2,
                            time: 2000
                        });
                    }
                });
                return false;
            });
        });
    </script>
</body>
</html> 