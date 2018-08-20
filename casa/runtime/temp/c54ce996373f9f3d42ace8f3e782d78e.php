<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:41:"templates/admin/usersrealname\handle.html";i:1533609386;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header">实名审核处理</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <form class="layui-form">
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">用户ID</label>
                            <div class="layui-input-block">
                                <input type="text" name="userId" id="userId" value="<?php echo $info['userId']; ?>" class="layui-input"
                                       disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">真实姓名</label>
                            <div class="layui-input-block">
                                <input type="text" name="trueName" id="trueName" value="<?php echo $info['trueName']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证号码</label>
                            <div class="layui-input-block">
                                <input type="text" name="cardID" id="cardID" value="<?php echo $info['cardID']; ?>"
                                       class="layui-input" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证正面</label>
                            <div class="layui-input-block">
                                <img src="<?php echo $info['cardUrl']; ?>" width="200">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">身份证反面</label>
                            <div class="layui-input-block">
                                <img src="<?php echo $info['cardBackUrl']; ?>" width="200">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">审核结果</label>
                            <div class="layui-input-block">
                                <select name="checkStatus" id="checkStatus" lay-filter="checkStatus">
                                    <option value="">请选择审核结果</option>
                                    <option value="1">审核通过</option>
                                    <option value="2">审核不通过</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item layui-form-text">
                        <label class="layui-form-label">审核备注</label>
                        <div class="layui-input-block">
                            <textarea placeholder="若不通过请输入内容" name="checkRemark" id="checkRemark" class="layui-textarea"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id" value="<?php echo $info['id']; ?>">
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
            var param = {};
            param.id = $('#id').val();
            param.checkStatus = $('#checkStatus').val();
            param.checkRemark = $('#checkRemark').val();
            param.trueName = $('#trueName').val();
            param.userId = $('#userId').val();
            if (param.checkStatus == ''){
                layer.msg('请选择审核结果',{icon:2});
            }
            if (param.checkStatus == 2 && param.checkRemark==''){
                layer.msg('请填写审核不通过原因',{icon:2});
                return false;
            }
            //console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
            $.post(SK.U('admin/Usersrealname/toHandle'), param, function (res) {
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