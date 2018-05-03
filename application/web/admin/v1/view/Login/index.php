<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="<?php echo $PUBLICCSS; ?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/style.min862f.css?v=4.1.0" rel="stylesheet">
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html"/>
    <![endif]-->
    <script>if (window.top !== window.self) {
            window.top.location = window.location;
        }</script>
</head>

<body class="gray-bg">
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">H+</h1>
        </div>
        <h3>欢迎使用 H+</h3>
        <form class="m-t" role="form" id="FormId">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="用户名" required="" name="username">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="密码" required="" name="password">
            </div>
            <div class="form-group">
                <input type="hidden" class="form-control" value="<?php echo $token; ?>" name="token">
            </div>
            <button type="button" class="btn btn-primary block full-width m-b" onclick="request.apiPost(ROUTER.LOGIN_PATH)">
                登 录
            </button>
            <!--                <p class="text-muted text-center"> <a href="#"><small>忘记密码了？</small></a> | <a href="/index.php/admin/Login/register">注册一个新账号</a></p>-->
        </form>
    </div>
</div>
<script src="<?php echo $PUBLICJS; ?>/jquery.min.js?v=2.1.4"></script>
<script src="<?php echo $PUBLICJS; ?>/bootstrap.min.js?v=3.3.6"></script>
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>
<?php require_once ROOT . '/application/web/admin/v1/view/Common/footer.php'; ?>
</body>
</html>
