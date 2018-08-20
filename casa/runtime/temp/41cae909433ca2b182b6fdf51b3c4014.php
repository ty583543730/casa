<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:31:"templates/admin/coin\index.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>币种管理</span>
            <!--<button class="layui-btn  layui-btn-sm"
                    onclick="SK.popupRight('LAY_PopupStaffs', SK.U('admin/coin/addCoin'), '600px');"
                    style="float: right;margin-top: 8px;">
                新增币种
            </button>-->
        </div>
        <div class="layui-card-body">
            <table id="coinTable" lay-filter="coinTable"></table>
            <script type="text/html" id="toolBar">
                <?php if(SKgrant('COIN_12')): ?>
                <span class="layui-btn layui-btn-xs" lay-event="edit">编辑</span>
                <?php endif; if(SKgrant('COIN_13')): ?>
                {{#  if(d.status == 1){ }}
                <span class="layui-btn layui-btn-danger layui-btn-xs" lay-event="close">禁用</span>
                {{#  } else { }}
                <span class="layui-btn layui-btn-xs" lay-event="open">启用</span>
                {{#  } }}
                <?php endif; ?>
            </script>
        </div>
    </div>
</div>


<script>
    layui.use(['form', 'upload', 'laydate', 'layer', 'table'], function () {
        var upload = layui.upload, laydate = layui.laydate, layer = layui.layer, table = layui.table;
        laydate.render({elem: '#startTime'});
        laydate.render({elem: '#endTime'});
        //渲染数据表格
        table.render({
            elem: '#coinTable',
            url: SK.U('admin/coin/index'),
            id: 'tables',
            unresize: true,
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            edit: false,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {
                    field: 'coin', title: '英文简称', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.coin + '">' + d.coin + '</span>';
                    }
                },
                {
                    field: 'title', title: '中文名', align: 'center', templet: function (d) {
                        return '<span title=" ' + d.title + '">' + d.title + '</span>';
                    }
                },
                {
                    field: 'img', title: '图标', align: 'center', templet: function (d) {
                        return '<img style="height:28px;width:28px;"  src=" ' + d.img + '">';
                    }
                },
                {
                    field: 'status', title: '状态', align: 'center', templet: function (d) {
                        if (d.status == 1) {
                            return '<span>已启用</span>';
                        } else {
                            return '<span>已禁用</span>';
                        }
                    }
                },
                {
                    field: 'zrZt', title: '转入状态', align: 'center', templet: function (d) {
                        if (d.zrZt == 1) {
                            return '<span>正常转入</span>';
                        } else {
                            return '<span>禁止转入</span>';
                        }
                    }
                },
                {
                    field: 'zcZt', title: '转出状态', align: 'center', width: '15%', templet: function (d) {
                        if (d.zcZt == 1) {
                            return '<span>正常转出</span>';
                        } else {
                            return '<span>禁止转出</span>';
                        }
                    }
                },
                {fixed: 'right', title: '操作', align: 'center', width: '10%', toolbar: '#toolBar'}
            ]],
        });
        table.on('tool(coinTable)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            if (layEvent === 'edit') {
                var url = SK.U('admin/coin/addCoin', 'id=' + data.id)
                SK.popupRight('LAY_PopupStaffs', url, '600px');
            } else if (layEvent === 'open') { //查看
                layer.confirm('确认启用', {title: '启用', skin: 'demo-class'},
                    function (index) {
                        updateStatus(data.id, 1);
                        layer.close(index);
                    });
            } else if (layEvent === 'close') {
                layer.confirm('确认禁用', {title: '禁用', skin: 'demo-class'},
                    function (index) {
                        updateStatus(data.id, -1);
                        layer.close(index);
                    });
            }
        });

        //更新状态
        function updateStatus(id, status) {
            $.post(SK.U('admin/coin/changeStatus'), {id: id, status: status}, function (data) {
                var json = SK.toJson(data);
                if (json.status == 1) {
                    layer.msg(json.msg, {
                        icon: 1, time: 2000, end: function () {
                            window.location.reload();
                        }
                    });
                } else {
                    layer.msg(json.msg, {icon: 5, time: 2000});
                }
            })
        }
    });
</script>

</body>
</html>