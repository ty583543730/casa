<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:32:"templates/admin/index\index.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1534468899;s:47:"E:\casa\public\templates\admin\base\header.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\menu.html";i:1533609385;s:49:"E:\casa\public\templates\admin\base\pagetabs.html";i:1533609385;}*/ ?>
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
    <link rel="icon" type="image/png" href="<?php echo SysConfig('websiteLogo'); ?>" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo SysConfig('websiteLogo'); ?>" sizes="16x16">
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

<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <!-- 头部区域 -->
        <!-- 头部区域 -->
<div class="layui-header">
    <ul class="layui-nav layui-layout-left">
        <li class="layui-nav-item layadmin-flexible" lay-unselect>
            <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="/" target="_blank" title="前台">
                <i class="layui-icon layui-icon-website"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="refresh" title="刷新">
                <i class="layui-icon layui-icon-refresh-3"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;" layadmin-event="back" title="返回上一页">
                <!--<i class="layui-icon layui-icon-refresh-3"></i>-->
                <i class="layui-icon" style="f333">&#xe65c;</i>
            </a>
        </li>
    </ul>
    <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="theme">
                <i class="layui-icon layui-icon-theme"></i>
            </a>
        </li>
        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="note">
                <i class="layui-icon layui-icon-note"></i>
            </a>
        </li>
        <li class="layui-nav-item" lay-unselect>
            <a href="javascript:;">
                <cite><?php echo session('sk_staff.staffName'); ?></cite>
            </a>
            <dl class="layui-nav-child">
                <dd><a lay-href="<?php echo url('admin/staffs/editMyPass'); ?>">修改密码</a></dd>
                <hr>
                <dd style="text-align: center;"><a href="<?php echo url('admin/login/logout'); ?>">退出</a>
                </dd>
            </dl>
        </li>

        <li class="layui-nav-item layui-hide-xs" lay-unselect>
            <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>
        </li>
        <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
            <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
        </li>
    </ul>
</div>
        <!-- 侧边菜单 -->
        <!-- 侧边菜单 -->
<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="home/console.html">
            <span><?php echo SysConfig("websiteName"); ?></span>
        </div>

        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu"
            lay-filter="layadmin-system-side-menu">
            <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): $i = 0; $__LIST__ = $menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?> <!--第一层目录 基础功能-->
            <li data-name="<?php echo $menu['menuId']; ?>" class="layui-nav-item">
                <a href="javascript:;" lay-tips="<?php echo $menu['menuName']; ?>" lay-direction="2">
                    <i class="layui-icon layui-icon-<?php echo $menu['alias']; ?>"></i>
                    <cite><?php echo $menu['menuName']; ?></cite>
                </a>
                <?php if(isset($menu['sub'])): ?>
                    <dl class="layui-nav-child">
                        <?php if(is_array($menu['sub']) || $menu['sub'] instanceof \think\Collection || $menu['sub'] instanceof \think\Paginator): $i = 0; $__LIST__ = $menu['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$submenu): $mod = ($i % 2 );++$i;?> <!--第二层目录 权限管理 系统日志等-->
                            <dd data-name="<?php echo $submenu['menuId']; ?>">
                                <?php if(isset($submenu['sub'])): ?>
                                    <a href="javascript:;"><?php echo $submenu['menuName']; ?></a>
                                    <dl class="layui-nav-child">
                                        <?php if(is_array($submenu['sub']) || $submenu['sub'] instanceof \think\Collection || $submenu['sub'] instanceof \think\Paginator): $i = 0; $__LIST__ = $submenu['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$bottom): $mod = ($i % 2 );++$i;?>
                                        <dd data-name="<?php echo $bottom['menuId']; ?>"><a lay-href="/<?php echo $bottom['privilegeUrl']; ?>"><?php echo $bottom['menuName']; ?></a></dd>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </dl>
                                <?php else: ?>
                                    <a lay-href="/<?php echo $submenu['privilegeUrl']; ?>"><?php echo $submenu['menuName']; ?></a>
                                <?php endif; ?>
                            </dd>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                <?php endif; ?>
            </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>
        <!-- 页面标签 -->
        <!-- 页面标签 -->
<div class="layadmin-pagetabs" id="LAY_app_tabs">
    <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
    <div class="layui-icon layadmin-tabs-control layui-icon-down">
        <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
            <li class="layui-nav-item" lay-unselect>
                <a href="javascript:;"></a>
                <dl class="layui-nav-child layui-anim-fadein">
                    <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                    <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                    <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                </dl>
            </li>
        </ul>
    </div>
    <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
        <ul class="layui-tab-title" id="LAY_app_tabsheader">
            <li lay-id="home/console.html" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
        </ul>
    </div>
</div>
        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="<?php echo url('admin/index/console'); ?>" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>


<script>
    layui.config({
        base: '/static/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');
</script>

</body>
</html>