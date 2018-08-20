<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"templates/admin/releaseconfig\index.html";i:1534301522;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>锁仓币释放配置</span>
            <button class="layui-btn  layui-btn-sm" onclick="edit(0);" style="float: right;margin-top: 8px;">
                新增配置
            </button>
        </div>
        <div class="layui-card-body">
            <table id="userTable" lay-filter="userTable"></table>
                <script type="text/html" id="toolBar">
                    <span class="layui-btn layui-btn-xs" onclick="edit({{d.id}});">编辑</span>
                    <span class="layui-btn layui-btn-danger layui-btn-xs" onclick="del({{d.id}});">删除</span>
                </script>
            </div>
    </div>
</div>


<script>
    layui.use(['laydate', 'table'], function () {
        var where = SK.getParams('.layui-input');
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
            url: SK.U('admin/Releaseconfig/index'),
            id: 'tables',
            unresize: true,
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            where:where,
            edit: false,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'id', title: 'ID', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.id + '">' + d.id + '</span>';
                    }},
                {field: 'min', title: '持币量区间小值', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.min + '">' + d.min + '</span>';
                    }},
                {field: 'max', title: '持币量区间大值', align: 'center',width:200, templet: function (d) {
                        return '<span title=" ' + d.max + '">' + d.max + '</span>';
                    }},
                {field: 'baseRatio', title: '算力基数', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.baseRatio + '">' + d.baseRatio + '</span>';
                    }},
                {field: 'addRatio', title: '算力增加系数', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.addRatio + '">' + d.addRatio + '</span>';
                    }},
                {field: 'addTop', title: '算力增加系数的封顶', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.addTop + '">' + d.addTop + '</span>';
                    }},
                {field: 'createTime', title: '创建时间', align: 'center',width:160, templet: function (d) {
                        return '<span title=" ' + d.createTime + '">' + d.createTime + '</span>';
                    }},
                {fixed: 'right', title: '操作', align: 'center', width: '10%', toolbar: '#toolBar'}
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
    });

    /*编辑页面*/
    function edit(id) {
        var url = '';
            url = SK.U('admin/Releaseconfig/add', 'id=' + id);
        SK.popupRight('LAY_PopupArt', url, '600px');
    }

    /*删除页面*/
    function del(id) {
        layer.confirm('您确认删除 ' + id + ' 配置？', {title: '删除配置'}, function (index) {
            $.post(SK.U('admin/Releaseconfig/del'), {id: id}, function (data) {
                layer.msg(data.msg);
                if (data.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            })
        });
    }

</script>

</body>
</html>