<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:32:"templates/admin/users\index.html";i:1534140181;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
                    <input type="text" id="userId" name="userId" placeholder="请输入用户ID" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userName" name="userName" placeholder="请输入用户名" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="userPhone" name="userPhone" placeholder="请输入用户手机号" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="startDate" name="startDate" placeholder="开始时间" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <input type="text" id="endDate" name="endDate" placeholder="结束时间" class="layui-input">
                </div>
            </div>
            <div class="layui-form layui-row" style="margin-bottom: 10px;">
                <div class=" layui-input-inline">
                    <select name="isRealname" id="isRealname" class="layui-input">
                        <option value="">实名认证</option>
                        <option value="1">已实名</option>
                        <option value="2">未实名</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="userStatus" id="userStatus" class="layui-input">
                        <option value="">用户状态</option>
                        <option value="1">正常</option>
                        <option value="-1">不能登录</option>
                    </select>
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
                    <select name="userType" id="userType" class="layui-input">
                        <option value="">用户类型</option>
                        <option value="1">普通用户</option>
                        <option value="2">经销商</option>
                        <option value="3">代理商</option>
                        <option value="4">区域代理商</option>
                    </select>
                </div>
                <button class="layui-btn query" data-type="reload">查询</button>
            </div>
            <table class="layui-hide" id="usersTable"></table>
        </div>
    </div>
</div>


<script type="text/html" id="toolBar">
    <span class="layui-btn layui-btn-xs" onclick="info({{d.userId}});">详情</span>
    <span class="layui-btn layui-btn-xs" onclick="referrals({{d.userId}});">被推荐人</span>
    <?php if(SKgrant('YHGL_07')): ?>
    <span class="layui-btn layui-btn-danger layui-btn-xs" onclick="javascript:changeStatus({{d.userId}},'{{d.userName}}',{{d.userStatus}});">
        {{# if(d.userStatus == -1){ }}解锁{{# }else{ }}锁定{{# } }}
    </span>
    <?php endif; if(SKgrant('YHGL_06')): ?>
    {{# if(d.userType == '普通用户' && d.userStatus != -1){ }}
    <span class="layui-btn layui-btn-xs" onclick="userUpgrade({{d.userId}},2);">升级为区长</span>
    {{# } }}
    {{# if(d.userType == '区长' && d.userStatus != -1){ }}
    <span class="layui-btn layui-btn-xs" onclick="userUpgrade({{d.userId}},3);">升级为系统管理员</span>
    {{# } }}
    <?php endif; ?>
    {{# if(d.userType != '普通用户' && d.userStatus != -1){ }}
    <?php if(SKgrant('YHGL_08')): ?>
    <span class="layui-btn layui-btn-xs" onclick="changeRewards({{d.userId}},'{{d.userName}}',{{d.userStatus}});">
        {{# if(d.userStatus == 2){ }}解锁奖励{{# }else{ }}锁定奖励{{# } }}
    </span>
    <?php endif; ?>
    {{# } }}
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
            url: SK.U('admin/Users/pageQuery'),
            limit: 20,
            limits: [20, 40, 60, 80, 100, 120],
            loading: true,
            page: true,
            text: {
                none: '暂无相关数据'
            },
            cols: [[ //标题栏
                {field: 'userId', title: 'ID', align: 'center'},
                {field: 'userName', title: '用户名称', align: 'center'},
                {field: 'userPhone', title: '手机号', align: 'center'},
                {field: 'userType', title: '用户类型', align: 'center'},
                {field: 'parentId', title: '邀请人ID', align: 'center'},
                {field: 'trueName', title: '真是姓名', align: 'center'},
                {
                    field: 'userStatus', title: '用户状态', align: 'center', templet: function (d) {
                        if (d.userStatus == 1) {
                            return '正常';
                        } else if (d.userStatus == -1) {
                            return '账户锁定';
                        } else {
                            return '奖励锁定';
                        }
                    }
                },
                {field: 'createTime', title: '时间', align: 'center'},
                {fixed: 'right', title: '操作', align: 'center',width:360, toolbar: '#toolBar'}
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
        SK.popupRight('LAY_PopupUsers', SK.U('admin/Users/info', 'id=' + id), '600px');
    }

    /*锁定解锁*/
    function changeStatus(id, name, status) {
        var content = '';
        if (status == 1) {
            content = '您确认要锁定 ' + name + ' 帐号吗？';
        } else {
            content = '您确认要解锁 ' + name + ' 帐号吗？';
        }
        layer.confirm(content, {title: '锁定解锁'}, function (index) {
            var mask = layer.load(1, {shade: [0.8, '#393D49']});
            $.post(SK.U('admin/Users/changeStatus'), {id: id, status: status}, function (data) {
                layer.close(mask);
                layer.msg(data.msg);
                if (data.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            })
        });
    }

    /*锁定解锁*/
    function changeRewards(id, name, status) {
        var content = '';
        if (status == 1) {
            content = '您确认要锁定 ' + name + ' 帐号的分佣奖励吗？';
        } else {
            content = '您确认要解锁 ' + name + ' 帐号的分佣奖励吗？';
        }
        layer.confirm(content, {title: '锁定解锁'}, function (index) {
            var mask = layer.load(1, {shade: [0.8, '#393D49']});
            $.post(SK.U('admin/Users/changeRewards'), {id: id, status: status}, function (data) {
                layer.close(mask);
                layer.msg(data.msg);
                if (data.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            })
        });
    }

    function userUpgrade(userId, role) {

        var content = '';
        if (role == 2) {
            content = '您确认要升级该用户为 区长 吗？';
        } else {
            content = '您确认要升级该用户为 系统管理员 吗？';
        }
        layer.confirm(content, {title: '升级'}, function (index) {
            var mask = layer.load(1, {shade: [0.8, '#393D49']});
            $.post(SK.U('admin/Users/userUpgrade'), {userId: userId, role: role}, function (data) {
                layer.close(mask);
                layer.msg(data.msg);
                if (data.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            })
        });
    }

    /*下线列表*/
    function referrals(id) {
        SK.popupOpen('LAY_PopupReferrals', SK.U('admin/Users/referrals', 'id=' + id), '1000px');
    }
</script>

</body>
</html>