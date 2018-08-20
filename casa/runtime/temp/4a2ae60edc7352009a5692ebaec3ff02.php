<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"templates/admin/manualrecharge\manualrecharge.html";i:1534150886;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header">数字币手动充值</div>
        <div class="layui-card-body">
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">币种</label>
                    <div class="layui-input-block inputs">
                        <select id="coin" name="coin" lay-verify="required" class="ipt">
                            <!--<?php if(is_array($coin) || $coin instanceof \think\Collection || $coin instanceof \think\Paginator): $i = 0; $__LIST__ = $coin;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                            <!--<option value="<?php echo $vo['coin']; ?>"><?php echo $vo['coin']; ?></option>-->
                            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                            <option value="INMC">INMC</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-block inputs">
                        <select id="status" name="status" lay-verify="required" class="ipt">
                            <option value="1">增加</option>
                            <option value="-1">减少</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">手机号</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="userPhone" id="userPhone" value="" lay-verify="required"
                               placeholder="请填写手机号" autocomplete="off" class="layui-input">
                        <span id="right" style="display: none;color: green"></span>
                        <span id="wrong" style="display: none;color: red"></span>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">充值数量</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="num" id="num" value="" lay-verify="required"
                               placeholder="请填写充值数量" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" style="border-top:1px solid #DCDCDC;margin-top:20px;">
                    <div class="layui-input-block" style="margin-top:20px;">
                        <input type="hidden" id="check" value="">
                        <?php if(SKgrant('MR_001')): ?>
                        <button class="layui-btn" lay-submit lay-filter="coinForm" id="submitCoin">充值</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    layui.use(['upload', 'form', 'layer'], function () {
        var upload = layui.upload, form = layui.form, layer = layui.layer;

        //数字币手动充值
        form.on('submit(coinForm)', function (data) {
            var checkStatus = $("#check").val();
            if(checkStatus < 1){
                layer.msg("请填写正确的用户手机号", {
                    icon: 5,
                    time: 3000
                });
                return false;
            }
            $('#submitCoin').attr('disabled', true);
            var param = data.field;
            var loading = SK.msg('正在提交处理中，请稍后...', {
                time: 600000
            });
            $.post(SK.U('admin/Manualrecharge/manualRecharge'), param, function (data) {
                var json = SK.toJson(data);
                if (json.status == 1) {
                    layer.msg(json.msg, {
                        icon: 1,
                        time: 2000,
                        end: function () {
                            location.reload();
                        }
                    });
                } else {
                    layer.msg(json.msg, {
                        icon: 5,
                        time: 3000
                    });
                    $('#submitCoin').attr('disabled', false);
                }
                layer.close(loading);
            });
        });
        $('#userPhone').on('blur',function(){
            var userPhone = $(this).val();
            $.post(SK.U('admin/Manualrecharge/checkUser'), {userPhone:userPhone}, function (data) {
                var json = SK.toJson(data);
                if (json.status == 1) {
                    document.getElementById("right").style.display = "";
                    document.getElementById("wrong").style.display = "none";
                    $("#check").val('1');
                    document.getElementById('right').innerText=json.msg;
                } else {
                    document.getElementById("right").style.display = "none";
                    document.getElementById("wrong").style.display = "";
                    $("#check").val('-1');
                    document.getElementById('wrong').innerText=json.msg;
                }
            });
        });
    });
</script>

</body>
</html>