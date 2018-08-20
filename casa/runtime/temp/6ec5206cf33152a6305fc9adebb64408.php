<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:39:"templates/admin/usersupgrade\index.html";i:1533609386;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>用户升級</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" id="userId" name="userId" placeholder="请输入用户ID" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userPhone" name="userPhone" placeholder="请输入用户手机号码" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <select name="type" id="type" class="layui-input">
                        <option value="">升级类型</option>
                        <option value="1">手动升级</option>
                        <option value="2">自动升级</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="startDate" name="startDate" placeholder="开始时间" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="endDate" name="endDate" placeholder="结束时间" class="layui-input">
                </div>
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
        </div>
        <table class="layui-hide" id="artTable"></table>
    </div>
</div>
</div>


<script>
    layui.use(['form', 'laydate', 'layer', 'table'], function () {
        var laydate = layui.laydate, layer = layui.layer, table = layui.table, form = layui.form;
        laydate.render({elem: '#startDate'});
        laydate.render({elem: '#endDate'});

        //渲染数据表格
        table.render({
            elem: '#artTable',
            id: 'tables',
            url: SK.U('admin/Usersupgrade/pageQuery'),
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'userId', title: '用户ID', align: 'center'},
                {field: 'userPhone', title: '用户手机', align: 'center'},
                {
                    field: 'type', title: '升级类型', align: 'center', templet: function (d) {
                        if (d.type == 1) {
                            return '手动升级';
                        } else if (d.type == 2) {
                            return '自动升级';
                        }
                    }
                },
                {field: 'preRole', title: '升级前状态', align: 'center'},
                {field: 'afterRole', title: '升级后状态', align: 'center'},
                {field: 'createTime', title: '申请时间', align: 'center'}

            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.layui-input');
                table.reload('tables', {
                    page: {
                        curr: 1//从第一页重新开始
                    },
                    where: where
                });
            }
        }
        //查询按钮
        $('.query').on('click', function () {
            var type = $(this).data('type');
            //console.log(type);
            active[type] ? active[type].call(this) : '';
        });
    });

</script>

</body>
</html>