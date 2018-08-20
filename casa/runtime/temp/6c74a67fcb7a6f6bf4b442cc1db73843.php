<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:32:"templates/admin/menus\index.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><span>后台菜单</span>
            <button class="layui-btn  layui-btn-sm" onclick="menu(1)" style="float: right;margin-top: 8px;">
                新增目录
            </button>
        </div>
        <div class="layui-card-body">
            <input type="hidden" id="menuid" value="0">
            <div class="layui-row">
                <div class="layui-col-md2">
                    <ul id="menusTree"></ul>
                </div>
                <div class="layui-col-md10">
                    <div class="layui-row">
                        <span>当前目录：</span><span id="menuName">空，请点击左侧目录</span>
                        <button class="layui-hide layui-btn layui-btn-sm" onclick="privilege(0)"
                                style="float: right">新增权限
                        </button>
                        <button class="layui-hide layui-btn   layui-btn-sm " onclick="menu(2)"
                                style="float: right;margin-right: 10px">新增子目录
                        </button>
                        <button class="layui-hide layui-btn   layui-btn-sm" onclick="menu(0)"
                                style="float: right;margin-right: 10px">编辑该目录
                        </button>
                        <button class="layui-hide layui-btn  layui-btn-danger  layui-btn-sm" onclick="delmenu()"
                                style="float: right;margin-right: 10px">删除该目录
                        </button>
                    </div>
                    <div class="layui-row" style="clear: both;margin-top: 5px;">
                        <table class="layui-hide" id="menusTable"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" onclick="privilege({{d.privilegeId}})">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs"
       onclick="delprivilege({{d.privilegeId}},'{{d.privilegeName}}')">删除</a>
</script>
<script>
    layui.use(['tree', 'layer', 'table'], function () {
        var layer = layui.layer, table = layui.table, $ = layui.jquery, nodes = <?php echo $menus; ?>;
        layui.tree({
            elem: '#menusTree' //指定元素
            , click: function (item) {
                //点击节点回调
                $('#menuName').text(item.name);
                $('.layui-hide').removeClass('layui-hide');
                $('#menuid').val(item.id);
                table.render({
                    elem: '#menusTable'
                    , url: SK.U('admin/privileges/listQuery', 'id=' + item.id)
                    , cellMinWidth: 80
                    , cols: [[
                        {field: 'privilegeId', width: 40, title: 'ID', sort: true}
                        , {field: 'privilegeName', title: '权限名称'}
                        , {field: 'privilegeCode', title: '权限代码'}
                        , {
                            field: 'isMenuPrivilege', title: '是否菜单权限', align: 'center', templet: function (d) {
                                if (d.isMenuPrivilege == 1) {
                                    return '是';
                                } else {
                                    return '否';
                                }
                            }
                        }
                        , {field: 'privilegeUrl', title: '权限资源'}
                        , {field: 'otherPrivilegeUrl', title: '关联资源'}
                        , {fixed: 'right', width: 120, title: '操作', align: 'center', toolbar: '#barDemo'}
                    ]]
                });
            }
            , nodes: nodes //节点
        });
    });

    /*新增编辑权限页面*/
    function privilege(id) {
        var url = '';
        if (id == 0) {
            var menuid = $('#menuid').val();
            url = SK.U('admin/privileges/index', 'menuid=' + menuid);
        } else {
            url = SK.U('admin/privileges/index', 'id=' + id);
        }
        SK.popupRight('LAY_PopupPrivilege', url, '600px');
    }

    /*删除权限*/
    function delprivilege(id, name) {
        layer.confirm('您确认删除 ' + name + ' 权限吗？', {title: '删除权限'}, function (index) {
            $.post(SK.U('admin/privileges/del'), {id: id}, function (data) {
                layer.msg(data.msg);
                if (data.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            })
        });
    }

    /*0编辑 1新增目录 2新增子目录页面*/
    function menu(flag) {
        var url = '';
        var menuid = $('#menuid').val();
        switch (flag) {
            case 0://0编辑
                url = SK.U('admin/menus/info', 'menuid=' + menuid);
                break;
            case 1://1新增目录
                url = SK.U('admin/menus/info');
                break;
            case 2://2新增子目录
                url = SK.U('admin/menus/info', 'ischild=1&menuid=' + menuid);
                break;
        }
        SK.popupRight('LAY_PopupMenu', url, '600px');
    }

    /*删除目录*/
    function delmenu() {
        var menuName = $('#menuName').text();
        var menuid = $('#menuid').val();
        layer.confirm('您确认删除  ' + menuName + '  目录吗？', {title: '删除目录'}, function (index) {
            $.post(SK.U('admin/menus/del'), {id: menuid}, function (data) {
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