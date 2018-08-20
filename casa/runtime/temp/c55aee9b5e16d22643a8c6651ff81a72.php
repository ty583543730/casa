<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:34:"templates/admin/rewards\index.html";i:1533869651;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>分佣管理</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" id="userId" name="userId" placeholder="请输入操作用户" class="layui-input">
                </div>
                <div class=" layui-input-inline">
                    <select name="type" id="type" class="layui-input">
                        <option value="">奖励类型</option>
                        <option value="1">交易奖励</option>
                        <option value="2">团队业绩</option>
                        <option value="3">新增业绩</option>
                        <option value="4">定期复投</option>
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
            <table class="layui-hide" id="usersTable"></table>
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
            elem: '#usersTable',
            id: 'tables',
            url: SK.U('admin/Rewards/pageQuery'),
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'orderNo', title: '订单号', align: 'center'},
                {
                    field: 'type', title: '奖励类型', align: 'center', templet: function (d) {
                        if (d.type == 1) {
                            return '交易奖励';
                        } else if (d.type == 2) {
                            return '团队业绩';
                        } else if (d.type == 3) {
                            return '新增业绩';
                        } else if (d.type == 4) {
                            return '定期复投';
                        }
                    }
                },
                {
                    field: 'total', title: '订单数量INMC)', align: 'center', templet: function (d) {
                        return d.total ;
                    }
                },
                {
                    field: 'num', title: '奖励值数量(INMC)', align: 'center', templet: function (d) {
                        return d.num ;
                    }
                },
                {field: 'createTime', title: '时间', align: 'center'},
                {
                    fixed: 'right', title: '操作', align: 'center', width: 180, templet: function (d) {
                        var h = '';
                        h += '<span class="layui-btn layui-btn-xs" onclick="info(' + d.id + ');">详情</span>';
                        return h;
                    }
                }
            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.layui-input');
                console.log(where);
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
            active[type] ? active[type].call(this) : '';
        });
    });

    /*详情页面*/
    function info(id) {
        SK.popupRight('LAY_PopupUsers', SK.U('admin/Rewards/info', 'id=' + id), '600px');
    }

</script>

</body>
</html>