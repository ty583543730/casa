<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:32:"templates/admin/login\index.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1534468899;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理-<?php echo SysConfig("websiteTitle"); ?></title>
    <meta name="keywords" content="<?php echo SysConfig('websiteKeywords'); ?>">
    <meta name="description" content="<?php echo SysConfig('websiteDesc'); ?>" >

    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" type="image/png" href="<?php echo SysConfig('websiteLogo'); ?>" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo SysConfig('websiteLogo'); ?>" sizes="16x16">
    <link rel="stylesheet" href="/static/plugins/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/plugins/layuiadmin/style/admin.css" media="all">
    
<link rel="stylesheet" href="/static/css/admin/login.css">

    <script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <script src="/static/plugins/layuiadmin/layui/layui.js"></script>
    <script type="text/javascript" src="/static/js/common.js?v=<?php echo $v; ?>"></script>
    <script>
        window.conf = {
            "APP": "",
        };
        <?php if(!empty(session('sk_staff.privileges'))): ?>
        /*设置权限代码到js 可以使用if(SK.GRANT.GGGL_02) xxxxx;判断是否具有权限*/
        var grants='<?php echo implode(",",session("sk_staff.privileges")); ?>';
        SK.setGrants(grants);
        <?php endif; ?>
    </script>
</head>
<body>

<div class="layui-header">
    <div class="layui-container">
        <h1 class="header-logo"><?php echo SysConfig("websiteName"); ?>-后台管理系统</h1>
    </div>
</div>
<div class="layui-container">
    <div class="login-content">
        <div class="login-box">
            <div class="tab-container clearfix">
                <div class="tabs fl active">登录</div>
            </div>
            <form class="layui-form forms">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" name="loginName" id="loginName" lay-verify="required" placeholder="用户名"
                               autocomplete="off" class="layui-input"/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="password" name="loginPwd" id="loginPwd" lay-verify="required" placeholder="密码"
                               autocomplete="off" class="layui-input"/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline code-input" style="width:268px;">
                        <input type="text" name="verifyCode" id="verifyCode" lay-verify="required" placeholder="验证码"
                               maxlength="6"
                               autocomplete="off" class="layui-input"/>
                    </div>
                    <img id='verifyImg' style="width:120px;height:50px;cursor: pointer;"
                         src="<?php echo url('admin/login/getVerify'); ?>" onclick='javascript:getVerify(this)'>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <span class="layui-btn" style="width:100%;" id="loginSubmit" onclick="onSubmit()">登录</span>
                        <span class="layui-btn" style="width:100%;background-color:#dedede;margin-left:0;display:none;"
                              id="disabledSubmit">登录</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    if (self != top) {
        top.location.reload();
    }
    var isSend = 0;
    layui.use(['form', 'layer'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.jquery;
    });

    //获取验证码
    function getVerify(img) {
        $(img).attr('src', SK.U('admin/login/getVerify', 'rnd=' + Math.random()));
    };

    //提交登录表单
    function onSubmit() {
        var params = SK.getParams('.layui-input', 1);
        if (!params.hasOwnProperty('loginName')) {
            layer.msg('用户名不能为空', {
                icon: 5,
                time: 2000
            });
            return false;
        }
        if (!params.hasOwnProperty('loginPwd')) {
            layer.msg('密码不能为空', {
                icon: 5,
                time: 2000
            });
            return false;
        }
        if (!params.hasOwnProperty('verifyCode')) {
            layer.msg('图形验证码不能为空', {
                icon: 5,
                time: 2000
            });
            return false;
        }
        $('#loginSubmit').hide();
        $('#disabledSubmit').show();
        $.post(SK.U('admin/login/checkLogin'), params, function (data) {
            var json = SK.toJson(data);
            if (json.status == 1) {
                top.location.href = SK.U('admin/index/index');
            } else {
                layer.msg(json.msg, {
                    icon: 2,
                    time: 2000
                });
                getVerify($("#verifyImg"));
            }
            $('#disabledSubmit').hide();
            $('#loginSubmit').show();
        });
    }

    //登录回车键登录事件
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $("#loginSubmit").trigger("click");
        }
    });
</script>

</body>
</html>