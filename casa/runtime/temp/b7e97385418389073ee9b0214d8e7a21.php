<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:36:"templates/admin/users\userscoin.html";i:1533172220;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533172220;}*/ ?>
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
        <div class="layui-card-header"><span>用户币种列表</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" id="userPhone" name="userPhone" placeholder="请输入手机号" class="layui-input">
                </div>
                <!--<div class="layui-input-inline">-->
                    <!--<select id="coin" name="coin" lay-verify="required" class="layui-input">-->
                        <!--<option value="">请选择币种</option>-->
                        <!--<?php if(is_array($coinList) || $coinList instanceof \think\Collection || $coinList instanceof \think\Paginator): $i = 0; $__LIST__ = $coinList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                        <!--<option value="<?php echo $vo['coin']; ?>"><?php echo $vo['coin']; ?></option>-->
                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                    <!--</select>-->
                <!--</div>-->
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
            <table class="layui-hide" id="usersTable"></table>
        </div>
    </div>
</div>


<script type="text/html" id="toolBar">
    <span class="layui-btn layui-btn-xs" onclick="info({{d.id}});">详情</span>
    <?php if(SKgrant('COIN_14')): ?>
    <span class="layui-btn layui-btn-xs" onclick="changeType({{d.id}},'{{d.userName}}','{{d.coin}}',{{d.drawType}});">
        {{# if(d.drawType == 1){ }}锁定{{# }else{ }}解锁{{# } }}
    </span>
    <?php endif; ?>
</script>
<script>
    layui.use([ 'laydate', 'layer', 'table'], function () {
        var laydate = layui.laydate, layer = layui.layer, table = layui.table;
        laydate.render({elem: '#startDate'});
        laydate.render({elem: '#endDate'});
        //渲染数据表格
        table.render({
            elem: '#usersTable',
            id: 'tables',
            url: SK.U('admin/Users/usersCoin'),
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'userId', title: '用户ID', align: 'center'},
                {field: 'userPhone', title: '手机号', align: 'center'},
                {field: 'coin', title: '币种', align: 'center'},
                {field: 'black', title: '可用币数量', align: 'center'},
                {field: 'locker', title: '锁定币数量', align: 'center'},
                {field: 'forzen', title: '冻结币数量', align: 'center'},
                {field: 'rechargeTotal', title: '累计获得', align: 'center'},
                {field: 'drawType', title: '提现状态', align: 'center',templet:function (d) {
                    if(d.drawType == 1){
                        return '正常';
                    }else {
                        return '锁定';
                    }
                }},
                {fixed: 'right', title: '操作', align: 'center', toolbar: '#toolBar'}
            ]],
        });
        var $ = layui.$, active = {
            reload: function () {
                var where = SK.getParams('.layui-input');
                console.log(where);
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
        SK.popupRight('LAY_PopupUsers', SK.U('admin/Users/coinInfo', 'id=' + id), '600px');
    }

    function changeType(id,userName,coin,drawType) {
        var content = '';
        if (drawType == 1) {
            content = '您确认要锁定 ' + userName + ' 的'+ coin +'账户吗？';
        } else {
            content = '您确认要解锁 ' + userName + ' 的'+ coin + '账户吗？';
        }
        layer.confirm(content, {title: '锁定解锁'}, function (index) {
            var mask = layer.load(1, {shade: [0.8, '#393D49']});
            $.post(SK.U('admin/Users/changeType'), {id: id, status: drawType}, function (data) {
                layer.close(mask);
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