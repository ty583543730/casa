<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:31:"templates/admin/users\info.html";i:1534144941;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
    <link rel="icon" type="image/png" href="/static/images/home/footer-logo.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/static/images/home/footer-logo.png" sizes="16x16">
    <link rel="stylesheet" href="/static/plugins/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/plugins/layuiadmin/style/admin.css" media="all">
    
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

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">用户详情</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <form class="layui-form">
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户ID</label>
                            <div class="layui-input-block">
                                <input type="text" id="userId" value="<?php echo $info['userId']; ?>" class="layui-input"
                                       disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户编号</label>
                            <div class="layui-input-block">
                                <input type="text" id="userNo" value="<?php echo $info['userNo']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户名称</label>
                            <div class="layui-input-block">
                                <input type="text" id="userName" value="<?php echo $info['userName']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <!--<div class="layui-col-md4">-->
                    <!--<div class="layui-form-item">-->
                    <!--<label class="layui-form-label">国家码</label>-->
                    <!--<div class="layui-input-block">-->
                    <!--<input type="text" name="phoneArea" id="phoneArea" value="<?php echo $info['phoneArea']; ?>"-->
                    <!--class="layui-input" disabled>-->
                    <!--</div>-->
                    <!--</div>-->
                    <!--</div>-->
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">手机号</label>
                            <div class="layui-input-block">
                                <input type="text" id="userPhone" value="<?php echo $info['userPhone']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <!--<div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">登录密码</label>
                            <div class="layui-input-block">
                                <input type="text" name="loginPwd" id="loginPwd" placeholder="登录密码 修改时不填不修改" value=""
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">支付密码</label>
                            <div class="layui-input-block">
                                <input type="text" name="payPwd" id="payPwd" placeholder="支付密码 修改时不填不修改" value=""
                                       autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>-->
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户类型</label>
                            <div class="layui-input-block">
                                <input type="text" id="userType" value="<?php echo $info['userType']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">真实姓名</label>
                            <div class="layui-input-block">
                                <input type="text" id="trueName" value="<?php echo $info['trueName']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">交易所id</label>
                            <div class="layui-input-block">
                                <input type="text" id="exchangeId" value="<?php echo $info['exchangeId']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">交易所名称</label>
                            <div class="layui-input-block">
                                <input type="text" id="exchangeName" value="<?php echo $info['exchangeName']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <!--<div class="layui-col-md4">-->
                    <!--<div class="layui-form-item">-->
                    <!--<label class="layui-form-label">头像</label>-->
                    <!--<div class="layui-input-block">-->
                    <!--<img src="<?php echo $info['userPhoto']; ?>" width="100">-->
                    <!--</div>-->
                    <!--</div>-->
                    <!--</div>-->
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">推荐人id</label>
                            <div class="layui-input-block">
                                <input type="text" name="parentId" id="parentId" placeholder="推荐人id"
                                       value="<?php echo $info['parentId']; ?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>

                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户状态</label>
                            <div class="layui-input-block">
                                <input type="radio" name="userStatus" value="1" title="正常" <?php if($info['userStatus'] == 1): ?>checked<?php endif; ?> >
                                <input type="radio" name="userStatus" value="-1" title="锁定账户" <?php if($info['userStatus'] == -1): ?>checked<?php endif; ?> >
                                <input type="radio" name="userStatus" value="2" title="锁定奖励" <?php if($info['userStatus'] == 2): ?>checked<?php endif; ?> >
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">支付开关</label>
                            <div class="layui-input-block">
                                <input type="radio" name="payStatus" value="1" title="开启" <?php if($info['payStatus'] == 1): ?>checked<?php endif; ?> >
                                <input type="radio" name="payStatus" value="0" title="关闭" <?php if($info['payStatus'] == 0): ?>checked<?php endif; ?> >
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">登录状态</label>
                            <div class="layui-input-block">
                                <input type="radio" name="loginStatus" value="1" title="开启" <?php if($info['loginStatus'] == 1): ?>checked<?php endif; ?> >
                                <input type="radio" name="loginStatus" value="0" title="关闭" <?php if($info['loginStatus'] == 0): ?>checked<?php endif; ?> >
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    layui.use('form', function () {
        var form = layui.form;

        //监听提交
        form.on('submit(formDemo)', function (data) {
            //console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
            $.post(SK.U('admin/users/edit'), data.field, function (res) {
                layer.msg(res.msg, {time: 2000});
                if (res.status == 1) {
                    setTimeout('parent.location.reload();', 2000);
                }
            }, 'json');
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    });
</script>


</body>
</html>