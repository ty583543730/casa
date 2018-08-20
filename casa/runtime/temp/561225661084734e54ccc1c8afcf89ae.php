<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"templates/admin/usersrealname\index.html";i:1533609386;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>用户管理</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class="layui-input-inline">
                    <input type="text" id="useId" name="useId" placeholder="请输入用户ID" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userPhone" name="userPhone" placeholder="请输入用户手机号" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userName" name="userName" placeholder="请输入用户名" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <select name="phoneArea" id="phoneArea" class="layui-input">
                        <option value="">国别</option>
                        <?php $_result=getCountry();if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <option value="<?php echo $vo['number']; ?>"><?php echo $vo['cnName']; ?></option>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="checkStatus" id="checkStatus" class="layui-input">
                        <option value="">审核状态</option>
                        <option value="0">审核中</option>
                        <option value="1">审核通过</option>
                        <option value="2">审核不通过</option>
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


<script type="text/html" id="toolBar">
    <span class="layui-btn layui-btn-xs" onclick="handle({{d.id}});">审核</span>
</script>
<script>
    layui.use(['form', 'laydate', 'layer', 'table'], function () {
        var laydate = layui.laydate, layer = layui.layer, table = layui.table, form = layui.form;
        laydate.render({elem: '#startDate'});
        laydate.render({elem: '#endDate'});

        //渲染数据表格
        table.render({
            elem: '#artTable',
            id: 'tables',
            url: SK.U('admin/Usersrealname/pageQuery'),
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
                {field: 'userName', title: '用户名', align: 'center'},
                {field: 'cardType', title: '证件类型', align: 'center'},
                {field: 'checkStatus', title: '审核状态', align: 'center',templet:function (d) {
                    if(d.checkStatus == 1){
                        return '审核通过';
                    }else if(d.checkStatus == 2){
                        return '审核不通过';
                    }else {
                        return '审核中';
                    }
                }},
                {field: 'trueName', title: '真实姓名', align: 'center'},
                {field: 'createTime', title: '申请时间', align: 'center'},
                {fixed: 'right', title: '操作', align: 'center',width:180, templet: function (d) {
                        var h = '';
                        if(d.checkStatus == 0){
                            if(SK.GRANT.SMRZ_02)h += '<span class="layui-btn layui-btn-xs" onclick="javascript:handle(' + d.id + ');">审核</span>';
                        } else {
                            h += '————'
                        }
                        return h;
                    }
                }
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

    /*审核处理页面*/
    function handle(id) {
        SK.popupRight('LAY_PopupUsers', SK.U('admin/Usersrealname/handle', 'id=' + id), '600px');
    }
</script>

</body>
</html>