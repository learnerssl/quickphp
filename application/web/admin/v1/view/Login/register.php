<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="<?php echo $PUBLICCSS;?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS;?>/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS;?>/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS;?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS;?>/style.min862f.css?v=4.1.0" rel="stylesheet">
    <script>if(window.top !== window.self){ window.top.location = window.location;}</script>
</head>

<body class="gray-bg">
<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">H+</h1>
        </div>
        <h3>欢迎注册 H+</h3>
        <p>创建一个H+新账户</p>
        <form class="m-t" role="form" action="http://www.zi-han.net/theme/hplus/login.html">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="请输入用户名" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="请输入密码" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="请再次输入密码" required="">
            </div>
            <div class="form-group text-left">
                <div class="checkbox i-checks">
                    <label class="no-padding">
                        <input type="checkbox"><i></i> 我同意注册协议</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">注 册</button>
            <p class="text-muted text-center"><small>已经有账户了？</small><a href="/index.php/admin/Login/login">点此登录</a></p>
        </form>
    </div>
</div>
<script src="<?php echo $PUBLICJS;?>/jquery.min.js?v=2.1.4"></script>
<script src="<?php echo $PUBLICJS;?>/bootstrap.min.js?v=3.3.6"></script>
<script src="<?php echo $PUBLICJS;?>/plugins/iCheck/icheck.min.js"></script>
<?php require_once ROOT . '/application/web/admin/v1/view/Common/footer.php'; ?>
<script>
    $(document).ready(function(){$(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",})});
</script>
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>
</body>
</html>
