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
    <title>修改数据</title>
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
        <div class="layui-card-header">修改数据</div>
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="editForm">
                <div class="layui-form-item">
                    <label class="layui-form-label">账号备注</label>
                    <div class="layui-input-block">
                        <select name="account_id" lay-verify="required" lay-search>
                            <option value="">请选择账号</option>
                            <?php
                            $stmt = $pdo->query("SELECT id, remark FROM accounts ORDER BY remark");
                            while ($row = $stmt->fetch()) {
                                echo "<option value='{$row['id']}'>{$row['remark']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">蜡烛数量</label>
                    <div class="layui-input-block">
                        <input type="number" name="candles" required lay-verify="required|number" placeholder="请输入蜡烛数量" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">爱心数量</label>
                    <div class="layui-input-block">
                        <input type="number" name="hearts" required lay-verify="required|number" placeholder="请输入爱心数量" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">季蜡数量</label>
                    <div class="layui-input-block">
                        <input type="number" name="season_candles" required lay-verify="required|number" placeholder="请输入季蜡数量" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">升华数量</label>
                    <div class="layui-input-block">
                        <input type="number" name="ascended_candles" required lay-verify="required|number" placeholder="请输入升华数量" class="layui-input">
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
            
            // 监听账号选择
            form.on('select(account_id)', function(data){
                if(data.value){
                    $.ajax({
                        url: 'api/get_account_data.php',
                        type: 'POST',
                        data: {id: data.value},
                        success: function(res){
                            if(res.code === 0){
                                form.val('editForm', {
                                    candles: res.data.candles,
                                    hearts: res.data.hearts,
                                    season_candles: res.data.season_candles,
                                    ascended_candles: res.data.ascended_candles
                                });
                            }
                        }
                    });
                }
            });
            
            // 监听提交
            form.on('submit(save)', function(data){
                $.ajax({
                    url: 'api/update_account.php',
                    type: 'POST',
                    data: data.field,
                    success: function(res){
                        if(res.code === 0){
                            layer.msg('保存成功');
                            // 记录操作日志
                            $.ajax({
                                url: 'api/add_log.php',
                                type: 'POST',
                                data: {
                                    operation_type: '修改',
                                    operation_detail: '修改账号数据',
                                    operator: '<?php echo $_SESSION['username']; ?>'
                                }
                            });
                        } else {
                            layer.msg('保存失败：' + res.msg);
                        }
                    }
                });
                return false;
            });
        });
    </script>
</body>
</html> 