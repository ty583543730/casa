<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:36:"templates/admin/assets\changein.html";i:1533172220;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533172220;}*/ ?>
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
        <div class="layui-card-header"><span>充值订单管理</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form" style="margin-bottom:10px;">
                <div class="layui-input-inline">
                    <input type="text" placeholder="用户手机号" id="userPhone" name="userPhone" class="layui-input ipt">
                </div>
                <div class="layui-input-inline">
                    <select id="coin" name="coin" lay-verify="required" class="ipt">
                        <option value="">请选择币种</option>
                        <?php if(is_array($coinList) || $coinList instanceof \think\Collection || $coinList instanceof \think\Paginator): $i = 0; $__LIST__ = $coinList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['coin']; ?>"><?php echo $vo['coin']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="startTime" name="startTime" placeholder="请选择时间" class="layui-input ipt">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="endTime" name="endTime" placeholder="请选择时间" class="layui-input ipt">
                </div>
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
            <div class="content" style="min-width:1000px;">
                <table id="userTable" lay-filter="userTable"></table>
            </div>
        </div>
    </div>
</div>


<script>
    layui.use(['laydate', 'table'], function () {
        var laydate = layui.laydate,
            table = layui.table;
        laydate.render({
            elem: '#startTime'
        });
        laydate.render({
            elem: '#endTime'
        });
        //渲染数据表格
        table.render({
            elem: '#userTable',
            url: SK.U('admin/Assets/changeIn'),
            id: 'tables',
            unresize: true,
            limit:20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            edit:false,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'userPhone', title: '用户手机号', align: 'center',templet:function(d) {
                    return '<span title=" ' + d.userPhone + '">' + d.userPhone + '</span>';
                }},
                {field: 'orderNo', title: '订单号', align: 'center',templet:function(d) {
                    return '<span title=" ' + d.orderNo + '">' + d.orderNo + '</span>';
                }},
                {field: 'coin', title: '币种', align: 'coin',templet:function(d){
                    return '<span title=" '+d.coin+'">'+d.coin+'</span>';
                }},
                {field: 'num', title: '充值数量', align: 'num', templet:function(d) {
                    return '<span title=" ' + d.num + '">' + d.num + '</span>';
                }},
                {field: 'status', title: '状态', align: 'status', templet:function(d) {
                    return '<span title=" ' + d.status + '">' + d.status + '</span>';
                }},
                {field: 'createTime', title: '创建时间', align: 'createTime', templet:function(d) {
                    return '<span title=" ' + d.createTime + '">' + d.createTime + '</span>';
                }},
                {field: 'checkRemark', title: '审核备注', align: 'checkRemark', templet:function(d) {
                    return '<span title=" ' + d.checkRemark + '">' + d.checkRemark + '</span>';
                }}
            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.ipt');
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
</script>

</body>
</html>