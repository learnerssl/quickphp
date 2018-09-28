<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>singwa赛事直播-主持人页面</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi"/>
    <link rel="stylesheet" href="<?php echo \config::$domain ?>/admin/css/font.css">
    <link rel="stylesheet" href="<?php echo \config::$domain ?>/admin/css/xadmin.css">
    <link rel="stylesheet" type="text/css" href="<?php echo \config::$domain ?>/admin/webuploader/webuploader.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo \config::$domain ?>/admin/lib/layui/layui.js"
            charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo \config::$domain ?>/admin/js/xadmin.js"></script>
    <script type="text/javascript" src="<?php echo \config::$domain ?>/admin/webuploader/webuploader.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
<div class="x-body">
    <form class="layui-form">

        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>第几节
            </label>
            <div class="layui-input-inline">
                <select id="type" name="type" class="valid">
                    <option value="1">第一节</option>
                    <option value="2">第二节</option>
                    <option value="3">第三节</option>
                    <option value="4">第四节</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>球队
            </label>
            <div class="layui-input-inline">
                <select id="team_id" name="team_id" class="valid">
                    <option value="0">请选择</option>
                    <option value="1">马刺</option>
                    <option value="4">火箭</option>
                </select>
            </div>
        </div>


        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                赛况内容
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" id="content" name="content" class="layui-textarea"></textarea>
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label for="desc" class="layui-form-label">
                赛况图
            </label>
            <div id="uploader-demo">
                <div id="fileList" class="uploader-list"></div>
                <div id="filePicker">选择图片</div>
                <input type="hidden" id="hidden_img">
            </div>

        </div>

        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
            </label>
            <button type="submit" class="layui-btn" lay-filter="add" id="submit-btn" lay-submit="">
                增加
            </button>
        </div>
    </form>
</div>
<script>
    var $ = jQuery,
        $list = $('#fileList'),
        ratio = window.devicePixelRatio || 1,

        thumbnailWidth = 100 * ratio,
        thumbnailHeight = 100 * ratio,
        uploader

    var uploader = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,

        // swf文件路径
        swf: '<?php echo \config::$domain ?>/admin/webuploader/Uploader.swf',

        // 文件接收服务端。
        server: 'http://quickphp.romantic.ren:9504/index.php/web/home/v1/Admin/upload',

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#filePicker',

        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });

    // 当有文件添加进来的时候
    uploader.on('fileQueued', function (file) {
        var $li = $(
            '<div id="' + file.id + '" class="file-item thumbnail">' +
            '<img>' +
            '</div>'
            ),
            $img = $li.find('img');


        // $list为容器jQuery实例
        $list.append($li);

        // 创建缩略图
        // 如果为非图片文件，可以不用调用此方法。
        // thumbnailWidth x thumbnailHeight 为 100 x 100
        uploader.makeThumb(file, function (error, src) {
            if (error) {
                $img.replaceWith('<span>不能预览</span>');
                return;
            }

            $img.attr('src', src);
        }, thumbnailWidth, thumbnailHeight);
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on('uploadProgress', function (file, percentage) {
        var $li = $('#' + file.id),
            $percent = $li.find('.progress span');

        // 避免重复创建
        if (!$percent.length) {
            $percent = $('<p class="progress"><span></span></p>')
                .appendTo($li)
                .find('span');
        }

        $percent.css('width', percentage * 100 + '%');
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on('uploadSuccess', function (file, response) {
        if (parseInt(response.error) === 1) {
            $('#hidden_img').val(response.data.src);
        }
        $('#' + file.id).addClass('upload-state-done');
    });

    // 文件上传失败，显示上传出错。
    uploader.on('uploadError', function (file) {
        var $li = $('#' + file.id),
            $error = $li.find('div.error');

        // 避免重复创建
        if (!$error.length) {
            $error = $('<div class="error"></div>').appendTo($li);
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on('uploadComplete', function (file) {
        $('#' + file.id).find('.progress').remove();
    });

    var $submitBtn = $('#submit-btn');
    // 提交表单
    $submitBtn.click(function (event) {
        event.preventDefault();
        var formData = $('form').serialize();
        // TODO: 请求后台接口跳转界面，前端跳转或者后台跳
        $.get("http://singwa.swoole.com:8811?s=admin/live/push&" + formData, function (data) {

            if (data.status == 1) {
                // 登录成功
            }
            // location.href='index.html';
        }, 'json');
    });


</script>
<script>var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();</script>
</body>

</html>