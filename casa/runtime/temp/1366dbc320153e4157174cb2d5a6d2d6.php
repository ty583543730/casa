<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:36:"templates/admin/privileges\edit.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><?php if(isset($info['privilegeId'])): ?>权限修改<?php else: ?>权限新增<?php endif; ?></div>
        <div class="layui-card-body">
            <div class="layui-row">
                <form class="layui-form">
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">权限名称</label>
                            <div class="layui-input-block">
                                <input type="text" name="privilegeName" id="privilegeName" required
                                       lay-verify="required" placeholder="权限名称" value="<?php echo $info['privilegeName']; ?>"
                                       autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">权限代码</label>
                            <div class="layui-input-block">
                                <input type="text" name="privilegeCode" id="privilegeCode" required
                                       lay-verify="required" placeholder="权限代码" value="<?php echo $info['privilegeCode']; ?>"
                                       autocomplete="off"
                                       class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">是否菜单权限</label>
                            <div class="layui-input-block">
                                <input type="text" name="isMenuPrivilege" id="isMenuPrivilege" required
                                       lay-verify="required" placeholder="是否菜单权限 1是 0否" value="<?php echo $info['isMenuPrivilege']; ?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <label class="layui-form-label">权限资源</label>
                            <div class="layui-input-block">
                                <input type="text" name="privilegeUrl" id="privilegeUrl"  placeholder="权限资源" value="<?php echo $info['privilegeUrl']; ?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item layui-form-text">
                            <label class="layui-form-label">关联资源</label>
                            <div class="layui-input-block">
                                <input type="text" name="otherPrivilegeUrl" id="otherPrivilegeUrl" placeholder="关联资源,以逗号，隔开" value="<?php echo $info['otherPrivilegeUrl']; ?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <input type="hidden" name="privilegeId" id="privilegeId"
                                       value="<?php echo $info['privilegeId']; ?>" class="layui-input">
                                <?php if(!empty($info['menuId'])): ?>
                                <input type="hidden" name="menuId" id="menuId" value="<?php echo $info['menuId']; ?>"
                                       class="layui-input">
                                <?php else: ?>
                                <input type="hidden" name="menuId" id="menuId" value="<?php echo $menuid; ?>"
                                       class="layui-input">
                                <?php endif; ?>
                                <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    layui.use('form', function () {
        var form = layui.form;

        //监听提交
        form.on('submit(formDemo)', function (data) {
            //console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
            $.post(SK.U('admin/privileges/' + ((data.field.privilegeId > 0) ? "edit" : "add")), data.field, function (res) {
                layer.msg(res.msg, {time: 2000});
                if (res.status == 1) {
                    setTimeout('location.reload();', 2000);
                }
            }, 'json');
            return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
        });
    });
</script>


</body>
</html>