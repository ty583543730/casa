<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:34:"templates/admin/index\console.html";i:1534142957;s:45:"E:\casa\public\templates\admin\base\base.html";i:1534468899;}*/ ?>
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

<link rel="stylesheet" href="/static/css/admin/console.css" media="all">
<div class="layui-fluid">
	<fieldset class="layui-elem-field layui-field-title magt30">
		<legend>待办事项</legend>
	</fieldset>
    <div class="layui-row">
        <div class="layui-col-md4 layui-col-xs4 layui-col-lg3">
        	<div class="panel">
				<a href="<?php echo url('admin/Usersrealname/index'); ?>">
					<div class="panel_word">
						<i class="layui-icon" style="vertical-align: middle;font-size: 24px;">&#xe770;</i>&nbsp;&nbsp;用户认证审核：
					</div>
					<div class="panel_icon">
						<?php echo $data['waitRealName']; ?>
					</div>
				</a>
			</div>
        </div>
        <div class="layui-col-md8 layui-col-xs8 layui-col-lg9">
        	<div class="panel">
				<a href="<?php echo url('admin/Assets/changeOut'); ?>">
					<div class="panel_word">
						<i class="layui-icon" style="vertical-align: middle;font-size: 24px;">&#xe66e;</i>&nbsp;&nbsp;提现审核：
					</div>
					<div class="panel_icon">
						<?php echo $data['waitDraw']; ?>
					</div>
				</a>
			</div>
        </div>
    </div>
    <fieldset class="layui-elem-field layui-field-title magt30">
		<legend>用户统计</legend>
	</fieldset>
	<div class="layui-row">
        <div class="layui-col-md4 layui-col-xs4 layui-col-lg3">
        	
        	<div class="panel">
				<a href="javascript:;">
					<div class="panel_word">
						<i class="layui-icon" style="vertical-align: middle;font-size: 24px;">&#xe679;</i>&nbsp;&nbsp;总注册人数：
					</div>
					<div class="panel_icon">
						<?php echo $data['userNumTotal']; ?>
					</div>
				</a>
			</div>
			<div style="padding: 0 5px;">今日注册人数：<span><?php echo $data['registerToday']; ?></span></div><br>
			<div style="padding: 0 5px;">本月注册人数：<span><?php echo $data['registerMonth']; ?></span></div><br><br>
			<div class="panel">
				<a href="javascript:;">
					<div class="panel_word">
						<i class="layui-icon" style="vertical-align: middle;font-size: 24px;">&#xe66a;</i>&nbsp;&nbsp;总区长数量：
					</div>
				</a>
			</div>
			<div class="panel">
				<a href="javascript:;">
					<div class="panel_word">
						<i class="layui-icon" style="vertical-align: middle;font-size: 24px;">&#xe66f;</i>&nbsp;&nbsp;总系统管理人：
					</div>
					<div class="panel_icon">
						<?php echo $data['xitongTotal']; ?>
					</div>
				</a>
			</div>
			<div style="padding: 0 5px;">今日系统管理人增量：<span><?php echo $data['adminToday']; ?></span></div><br>
			<div style="padding: 0 5px;">本月系统管理人增量：<span><?php echo $data['adminMonth']; ?></span></div><br><br>
        </div>
        <!-- 报表 start -->
        <div class="layui-col-md8 layui-col-xs8 layui-col-lg9">
        	<div id="regUserToday" style="width:100%;height:300px;margin-left: -40px;"></div>
        	<div id="regUserMonth"  style="width:100%;height:300px;margin-left: -40px;"></div>
        </div>
        <!-- 报表end -->
    </div>
</div>


<script src="/static/plugins/layuiadmin/lib/extend/echarts.js"></script>
<script>
$(function(){
	$.post(SK.U('admin/index/registerCount'), {}, function (data) {
		var json = SK.toJson(data);
		var todayChart = {
           tooltip: {trigger: "axis", formatter: "{b}<br>数量：{c}"},
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: [
                    {
                        type: "category",
                        name:'日',
                        splitLine:{show:false},
                       /* data:function(){
                            var list = ["1", "2", "3", "4", "5", "6", "7"];
                            for (var i = 1; i <= list.length; i++) {
                                list.push(list[i]+'月');
                            }
                            return list;
                        }*/
                       data:json.dayCount.date
                    }
                ],
            yAxis: [
                   {
                       type: "value",
                       name:'今日注册数量',
                       nameTextStyle: {
							color: '#333',
							fontSize: 14
						}
                   }
                ],
            series: [
                {
                    name: '今日注册数量',
                    type: 'bar',
                    splitLine:{show:false},
                    label: {
                        normal: {
                            show: true,
                            position: 'top',
                            formatter:function(param){
                                if(param.data == 0){
                                    return '';
                                }
                            }
                        }
                    },
                    data: json.dayCount.userNum}
                ]
        };
        var monthChart = {
            tooltip: {trigger: "axis", formatter: "{b}<br>数量：{c}"},
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: [
                    {
                        type: "category",
                        name:'月',
                        splitLine:{show:false},
                       /* data:function(){
                            var list = ["1", "2", "3", "4", "5", "6", "7"];
                            for (var i = 1; i <= list.length; i++) {
                                list.push(list[i]+'月');
                            }
                            return list;
                        }*/
                       data:json.monCount.date
                    }
                ],
            yAxis: [
                   {
                       type: "value",
                       name:'本月注册数量',
                       nameTextStyle: {
							color: '#333',
							fontSize: 14
						}
                   }
                ],
            series: [
                {
                    name: '本月注册数量',
                    type: 'bar',
                    splitLine:{show:false},
                    label: {
                        normal: {
                            show: true,
                            position: 'top',
                            formatter:function(param){
                                if(param.data == 0){
                                    return '';
								}
                            }
                        }
                    },
                    data: json.monCount.monCount}
                ]
        };
        var regUserToday = document.getElementById('regUserToday');
        var regUserMonth =  document.getElementById('regUserMonth');
        
        var todayCharts = echarts.init(regUserToday);
        todayCharts.setOption(todayChart);
        
        var monthCharts =  echarts.init(regUserMonth);
        monthCharts.setOption(monthChart);
  });
});

</script>

</body>
</html>