<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登录</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: url('/images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: "Microsoft YaHei", sans-serif;
        }
        .background {
            position: absolute;
            display: block;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }
        .login-container {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 340px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            color: #333;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .layui-form-item {
            margin-bottom: 25px;
            position: relative;
        }
        .layui-input {
            height: 44px;
            line-height: 44px;
            padding: 0 15px;
            font-size: 15px;
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .layui-input:hover {
            border-color: #1E9FFF;
        }
        .layui-input:focus {
            border-color: #1E9FFF;
            box-shadow: 0 0 8px rgba(30, 159, 255, 0.2);
        }
        .layui-btn {
            height: 44px;
            line-height: 44px;
            font-size: 16px;
            background-color: #1E9FFF;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(30, 159, 255, 0.3);
            transition: all 0.3s ease;
        }
        .layui-btn:hover {
            background-color: #1a90e6;
            box-shadow: 0 6px 16px rgba(30, 159, 255, 0.4);
            transform: translateY(-2px);
        }
        /* 添加输入框图标 */
        .input-prefix {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            transition: all 0.3s;
        }
        .input-with-prefix {
            padding-left: 40px !important;
        }
        /* 添加加载动画 */
        .loading {
            pointer-events: none;
            opacity: 0.7;
        }
        .loading:after {
            content: '';
            display: inline-block;
            width: 1em;
            height: 1em;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <canvas class="background"></canvas>

    <div class="login-container">
    <div class="login-box">
            <div class="login-title">系统登录</div>
            <form class="layui-form" action="api/login.php" method="post">
            <div class="layui-form-item">
                    <i class="layui-icon layui-icon-username input-prefix"></i>
                    <input type="text" name="username" required lay-verify="required" 
                           placeholder="请输入用户名" class="layui-input input-with-prefix">
            </div>
            <div class="layui-form-item">
                    <i class="layui-icon layui-icon-password input-prefix"></i>
                    <input type="password" name="password" required lay-verify="required" 
                           placeholder="请输入密码" class="layui-input input-with-prefix">
            </div>
            <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit id="loginBtn">登录</button>
            </div>
        </form>
        </div>
    </div>

    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/layui/layui.js"></script>
    <script src="/js/particles.min.js"></script>
    <script>
        layui.use(['form', 'layer'], function(){
            var form = layui.form;
            var layer = layui.layer;
            
            // 优化粒子效果配置
            window.onload = function() {
                Particles.init({
                    selector: '.background',
                    maxParticles: 120,
                    sizeVariations: 4,
                    speed: 0.8,
                    color: '#ffffff',
                    minDistance: 140,
                    connectParticles: true,
                    responsive: [
                        {
                            breakpoint: 768,
                            options: {
                                maxParticles: 80,
                                connectParticles: true
                            }
                        },
                        {
                            breakpoint: 425,
                            options: {
                                maxParticles: 50,
                                connectParticles: true
                            }
                        }
                    ]
                });
            };

            // 监听表单提交
            form.on('submit', function(data){
                var $btn = $('#loginBtn').addClass('loading').text('登录中...');
                
                $.ajax({
                    url: 'api/login.php',
                    type: 'POST',
                    data: data.field,
                    success: function(res){
                        if(res.code === 0){
                            layer.msg('登录成功', {icon: 1});
                            setTimeout(function(){
                                window.location.href = 'index.php';
                            }, 1000);
                        } else {
                            layer.msg(res.msg || '登录失败', {icon: 2});
                            $btn.removeClass('loading').text('登录');
                        }
                    },
                    error: function(){
                        layer.msg('请求失败，请重试', {icon: 2});
                        $btn.removeClass('loading').text('登录');
                    }
                });
                return false;
            });

            // 输入框焦点效果
            $('.layui-input').on('focus', function() {
                $(this).prev('.input-prefix').css('color', '#1E9FFF');
            }).on('blur', function() {
                $(this).prev('.input-prefix').css('color', '#999');
            });
        });
    </script>
</body>
</html> 