<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:34:"templates/admin/savings\index.html";i:1534301388;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>储蓄</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" placeholder="用户ID" id="userId" name="userId" class="layui-input ipt">
                </div>
                <div class="layui-input-inline">
                    <input type="text" placeholder="订单号" id="orderNo" name="orderNo" class="layui-input ipt">
                </div>
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
            <table id="userTable" lay-filter="userTable"></table>
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
            url: SK.U('admin/Savings/index'),
            id: 'tables',
            unresize: true,
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            where: where,
            edit: false,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {
                    field: 'userId', title: '用户ID', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.userId + '">' + d.userId + '</span>';
                    }
                },
                {
                    field: 'orderNo', title: '订单号', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.orderNo + '">' + d.orderNo + '</span>';
                    }
                },
                {
                    field: 'month', title: '月数', align: 'center', width: 200, templet: function (d) {
                        return '<span title=" ' + d.month + '">' + d.month + '</span>';
                    }
                },
                {
                    field: 'endTime', title: '到期时间', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.endTime + '">' + d.endTime + '</span>';
                    }
                },
                {
                    field: 'num', title: '复投币数', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.num + '">' + d.num + '</span>';
                    }
                },
                {
                    field: 'marketValue', title: '复投时的市值', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.marketValue + '">' + d.marketValue + '</span>';
                    }
                },
                {
                    field: 'ratio', title: '倍数', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.ratio + '">' + d.ratio + '</span>';
                    }
                },
                {
                    field: 'surplusRatio', title: '奖励剩余比例', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.surplusRatio + '">' + d.surplusRatio + '</span>';
                    }
                },
                {
                    field: 'status', title: '复投币数', align: 'center', templet: function (d) {
                        if (d.status == 1) {
                            return '<span title=" ' + d.dataFlag + '">' + '进行中' + '</span>';
                        } else if (d.status == 2) {
                            return '<span title=" ' + d.dataFlag + '">' + '奖励中' + '</span>';
                        } else if (d.status == 3) {
                            return '<span title=" ' + d.dataFlag + '">' + '已完结' + '</span>';
                        }
                    }
                },
                {
                    field: 'dataFlag', title: '删除标记', align: 'center', templet: function (d) {
                        if (d.dataFlag == 1) {
                            return '<span title=" ' + d.dataFlag + '">' + '有效' + '</span>';
                        } else if (d.dataFlag == 0) {
                            return '<span title=" ' + d.dataFlag + '">' + '已删除' + '</span>';
                        }
                    }
                },
                {
                    field: 'createTime', title: '创建时间', align: 'center', width: 160, templet: function (d) {
                        return '<span title=" ' + d.createTime + '">' + d.createTime + '</span>';
                    }
                },
            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.ipt');
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

</script>

</body>
</html>