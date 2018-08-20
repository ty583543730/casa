<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:33:"templates/admin/rewards\info.html";i:1533869374;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
    .layui-row div {
        line-height: 35px;
        height: 35px;
        border-bottom: #cccccc 1px dashed;
    }
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">奖励详情</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs4">
                    订单编号：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['orderNo']; ?>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    类型：
                </div>
                <div class="layui-col-xs8">
                    <?php switch($info['type']): case "1": ?>交易奖励<?php break; case "2": ?>团队业绩<?php break; case "3": ?>新增业绩<?php break; case "4": ?>定期复投<?php break; endswitch; ?>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    订单数量：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['total']; ?> INMC
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    奖励数量：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['num']; ?> INMC
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    奖励可用积分：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['score']; ?>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    奖励绑定积分：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['binding']; ?>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    奖励锁仓币：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['locker']; ?> INMC
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs4">
                    时间：
                </div>
                <div class="layui-col-xs8">
                    <?php echo $info['createTime']; ?>
                </div>
            </div>
        </div>
    </div>
</div>



</body>
</html>