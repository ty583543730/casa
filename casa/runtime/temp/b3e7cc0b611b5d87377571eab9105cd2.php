<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:36:"templates/admin/transfers\index.html";i:1533172220;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533172220;}*/ ?>
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
        <div class="layui-card-header"><span>订单管理</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" id="orderNo" name="orderNo" placeholder="订单号" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="sendPhone" name="sendPhone" placeholder="转账付款用户手机号" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userPhone" name="userPhone" placeholder="到账收款用户手机号" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="startDate" name="startDate" placeholder="开始时间" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="endDate" name="endDate" placeholder="结束时间" class="layui-input">
                </div>
                <div class=" layui-input-inline">
                    <select name="type" id="type" class="layui-input">
                        <option value="">类型</option>
                        <option value="1">转账</option>
                        <option value="2">收款</option>
                    </select>
                </div>
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
            <table class="layui-hide" id="Transfers"></table>
        </div>
    </div>
</div>


<script type="text/html" id="toolBar">
    <span class="layui-btn layui-btn-xs" onclick="info({{d.id}});">详情</span>
</script>
<script>
    layui.use(['form', 'laydate', 'layer', 'table'], function () {
        var laydate = layui.laydate, layer = layui.layer, table = layui.table, form = layui.form;
        laydate.render({elem: '#startDate'});
        laydate.render({elem: '#endDate'});

        //渲染数据表格
        table.render({
            elem: '#Transfers',
            id: 'tables',
            url: SK.U('admin/Transfers/pageQuery'),
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'orderNo', title: '订单编号', align: 'center'},
                {field: 'sendPhone', title: '转账付款用户', align: 'center'},
                {field: 'userPhone', title: '到账收款用户', align: 'center'},
                {field: 'type', title: '类型', align: 'center'},
                {field: 'coinType', title: '币的类型', align: 'center'},
                {field: 'total', title: '转账数量', align: 'center'},
                {field: 'fee', title: '手续费', align: 'center'},
                {field: 'num', title: '到账数量', align: 'center'},
                {field: 'createTime', title: '时间', align: 'center'},
                {fixed: 'right', title: '操作', align: 'center', toolbar: '#toolBar'}
            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.layui-input');
                table.reload('tables', {
                    page:{
                        curr:1//从第一页重新开始
                    },
                    where: where
                });
            }
        }
        //查询按钮
        $('.query').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });
    });

    /*详情页面*/
    function info(id) {
        SK.popupRight('LAY_PopupTransfers', SK.U('admin/Transfers/info', 'id=' + id), '600px');
    }
</script>

</body>
</html>