<!DOCTYPE html>
<html>


<!-- Mirrored from www.zi-han.net/theme/hplus/form_basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Jan 2016 14:19:15 GMT -->
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>H+ 后台主题UI框架 - 基本表单</title>
    <meta name="keywords" content="H+后台主题,后台bootstrap框架,会员中心主题,后台HTML,响应式后台">
    <meta name="description" content="H+是一个完全响应式，基于Bootstrap3最新版本开发的扁平化主题，她采用了主流的左右两栏式布局，使用了Html5+CSS3等现代技术">

    <link rel="shortcut icon" href="favicon.ico">
    <link href="<?php echo $PUBLICCSS; ?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $PUBLICCSS; ?>/style.min862f.css?v=4.1.0" rel="stylesheet">

</head>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>网站基本信息
                        <small></small>
                    </h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" id="FormId">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">站点标题</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入站点标题" class="form-control" name="title" value="<?php echo $basic['title']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">站点关键字</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入站点关键字" class="form-control" name="keyword" value="<?php echo $basic['keyword']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">站点描述</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入站点描述" class="form-control" name="description" value="<?php echo $basic['description']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">底部版权信息</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入底部版权信息" class="form-control" name="copyright" value="<?php echo $basic['copyright']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备案号</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入备案号" class="form-control" name="icp" value="<?php echo $basic['icp']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系地址</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入联系地址" class="form-control" name="address" value="<?php echo $basic['address']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系电话</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入联系电话" class="form-control" name="mobile" value="<?php echo $basic['mobile']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系邮箱</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入联系邮箱" class="form-control" name="email" value="<?php echo $basic['email']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">工作时间</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="请输入工作时间" class="form-control" name="worktime" value="<?php echo $basic['worktime']; ?>">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">关于我们</label>

                            <div class="col-sm-10">
                                <textarea class="form-control" placeholder="请输入关于我们" name="aboutus"><?php echo $basic['aboutus']; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group sr-only">
                            <label class="col-sm-2 control-label">token</label>

                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $token; ?>" class="form-control" name="token">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="button" onclick="request.apiPost(ROUTER.BASCI_SAVE_PATH)">
                                    保存内容
                                </button>
                                <button class="btn btn-white" type="reset">重置</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $PUBLICJS; ?>/jquery.min.js?v=2.1.4"></script>
<script src="<?php echo $PUBLICJS; ?>/bootstrap.min.js?v=3.3.6"></script>
<script src="<?php echo $PUBLICJS; ?>/content.min.js?v=1.0.0"></script>
<script src="<?php echo $PUBLICJS; ?>/plugins/iCheck/icheck.min.js"></script>
<?php require_once ROOT . '/application/web/admin/v1/view/Common/footer.php'; ?>
<script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>
</body>


<!-- Mirrored from www.zi-han.net/theme/hplus/form_basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Jan 2016 14:19:15 GMT -->
</html>
