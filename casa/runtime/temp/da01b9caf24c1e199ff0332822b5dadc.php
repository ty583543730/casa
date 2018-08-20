<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:33:"templates/admin/coin\addCoin.html";i:1533609385;s:45:"E:\casa\public\templates\admin\base\base.html";i:1533609385;}*/ ?>
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
        <div class="layui-card-header"><?php if(empty($object['catId'])): ?>新增<?php else: ?>修改<?php endif; ?>币种</div>
        <div class="layui-card-body">
            <form class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">英文简称</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="coin" id="coin" value="<?php echo $coin['coin']; ?>" <?php if($coin !=''): ?>disabled<?php endif; ?>
                        lay-verify="required" placeholder="请填写英文简称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <?php if($coin['coin'] == 'INMC'): ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">提现手续费</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="zcFee" id="fee" value="<?php echo $coin['zcFee']; ?>" lay-verify="required"
                               placeholder="提现手续费" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <?php endif; ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">中文名</label>
                    <div class="layui-input-block inputs">
                        <input type="text" name="title" id="title" value="<?php echo $coin['title']; ?>" lay-verify="required"
                               placeholder="请填写中文名" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否拥有服务器</label>
                    <div class="layui-input-block inputs">
                        <input type="radio" name="ownHost" value="1" title="拥有" <?php if($coin['ownHost'] !=0): ?>checked<?php endif; ?>>
                        <input type="radio" name="ownHost" value="0" title="没有" <?php if($coin['ownHost']== 0): ?>checked<?php endif; ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否有上级货币</label>
                    <div class="layui-input-block inputs">
                        <select id="superiorCoin" name="superiorCoin" lay-verify="required" class="layui-input">
                            <option value="">请选择币种</option>
                            <option value="NULL">无</option>
                            <option value="ETH" <?php if($coin['isCurrency'] ==2 && $coin['superiorCoin']=="ETH"): ?>selected="selected"<?php endif; ?>>ETH</option>
                            <option value="BTC" <?php if($coin['isCurrency'] ==2 && $coin['superiorCoin']=="BTC"): ?>selected="selected"<?php endif; ?>>BTC</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">转入状态</label>
                    <div class="layui-input-block inputs">
                        <input type="radio" name="zrZt" value="1" title="正常转入" <?php if($coin['zrZt'] !=0): ?>checked<?php endif; ?>>
                        <input type="radio" name="zrZt" value="0" title="禁止转入" <?php if($coin['zrZt']== 0): ?>checked<?php endif; ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">转出状态</label>
                    <div class="layui-input-block inputs">
                        <input type="radio" name="zcZt" value="1" title="正常转出" <?php if($coin['zcZt'] !=0): ?>checked<?php endif; ?>>
                        <input type="radio" name="zcZt" value="0" title="禁止转出" <?php if($coin['zcZt']== 0): ?>checked<?php endif; ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">图标</label>
                    <div class="layui-input-block clearfix">
                        <div class="code-file-box clearfix" id="cardImg">
                            <div class="coinImgBox fl">
                                <img src="<?php echo $coin['img']; ?>" alt="" width="100">
                            </div>
                            <div class="upload-code fl">
                                <input type="hidden" name="img" id="img" lay-verify="required" value="<?php echo $coin['img']; ?>">
                                <span class="layui-btn" id="sendCode">
                                上传图标
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item" style="border-top:1px solid #DCDCDC;margin-top:20px;">
                    <div class="layui-input-block" style="margin-top:20px;">
                        <input type="hidden" name="id" id="id" value="<?php echo $coin['id']; ?>">
                        <button class="layui-btn" lay-submit lay-filter="coinForm" id="submitCoin">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    layui.use(['upload', 'form', 'layer'], function () {
        var upload = layui.upload, form = layui.form, layer = layui.layer;

        var uploadInst = upload.render({
            elem: '#sendCode', //绑定元素
            accept: {
                extensions: 'gif,jpg,jpeg,png',
                mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'
            },
            auto:false,
            data: {dir: 'coin'},
            url: '/admin/index/uploadPic', //上传接口
            choose:function(obj){
                var flag = true;
                obj.preview(function(index, file, result){
                    if(file.size < 5*1024*1024){
                        obj.upload(index, file);
                    }else{
                        flag = false;
                        layer.msg("您上传的图片大小不能超过5M！", {
                            icon: 5,
                            time: 2000
                        });
                        uploadInst.config.elem.next()[0].value = '';
                        return false;
                    }
                    return flag;
                });
            },
            before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                layer.load(1,{shade: [0.3, '#000000']}); //上传loading
            },
            done: function (json){
                var html = '<img width="100" height="100" src="' + json.data.src + '"/>';
                $(".coinImgBox").html(html);
                $("#img").val(json.data.src);
                layer.closeAll('loading'); //关闭loading
            },
            error: function () {
                //请求异常回调
                layer.closeAll('loading'); //关闭loading
            }
        });
        //新增、编辑用户钱包地址
        form.on('submit(coinForm)', function (data) {
            $('#submitCoin').attr('disabled', true);
            var param = data.field;
            var loading = SK.msg('正在提交处理中，请稍后...', {
                time: 600000
            });
            $.post(SK.U('admin/coin/addCoin'), param, function (data) {
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
    });
</script>

</body>
</html>