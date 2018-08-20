<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:38:"templates/admin/releaseconfig\add.html";i:1534390854;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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

<style>
    .layui-form-label {
        width: 160px;
        padding: 9px 10px 9px 0;
    }

    .layui-input-block {
        margin-left: 170px;
    }
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">持币量区间最小值</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="min" id="min" value="<?php echo $data['min']; ?>"
                               lay-verify="required" placeholder="请填写数字" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">持币量区间大值</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="max" id="max" value="<?php echo $data['max']; ?>" lay-verify="required"
                               placeholder="提现手续费" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">算力基数</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="baseRatio" id="baseRatio" value="<?php echo $data['baseRatio']; ?>"
                               lay-verify="required"
                               placeholder="请填写数字" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">算力增加系数</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="addRatio" id="addRatio" value="<?php echo $data['addRatio']; ?>" lay-verify="required"
                               placeholder="请填写数字" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">算力增加系数封顶</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="addTop" id="addTop" value="<?php echo $data['addTop']; ?>" lay-verify="required"
                               placeholder="请填写数字" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">创建时间</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="createTime" id="createTime" value="<?php echo $data['createTime']; ?>"
                               lay-verify="required"
                               placeholder="请填写中文名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" style="border-top:1px solid #DCDCDC;margin-top:20px;">
                    <div class="layui-input-block" style="margin-top:20px;">
                        <button class="layui-btn" lay-submit lay-filter="coinForm" id="submitCoin">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    layui.use(['laydate', 'form', 'layer'], function () {
        var form = layui.form;
        var laydate = layui.laydate,
            table = layui.table;
        laydate.render({
            elem: '#createTime',
            type: 'datetime'
        });
        //新增、编辑用户钱包地址
        form.on('submit(coinForm)', function (data) {
            $('#submitCoin').attr('disabled', true);
            var param = data.field;
            var loading = SK.msg('正在提交处理中，请稍后...', {
                time: 600000
            });
            $.post(SK.U('admin/Releaseconfig/add'), param, function (data) {
                var json = SK.toJson(data);
                if (json.status == 0) {
                    layer.msg(json.msg, {
                        icon: 1,
                        time: 2000
                    });
                    parent.location.reload();
                } else {
                    layer.msg(json.msg, {
                        icon: 5,
                        time: 3000
                    });
                    $('#submitCoin').attr('disabled', false);
                }
                layer.close(loading);
            });
        });
    });
</script>

</body>
</html>