<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:39:"templates/home/default/index\index.html";i:1533172220;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>钱包网站</title>
	<!--<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta name="author" content="" />-->
	<meta name="msapplication-TileColor" content="#00a8ff">
	<meta name="theme-color" content="#ffffff">
	<link rel="icon" type="image/png" href="/static/images/home/footer-logo.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/static/images/home/footer-logo.png" sizes="16x16">
	<!-- Bootstrap -->
	<link rel="stylesheet" type="text/css" href="/static/css/home/bootstrap.css">
	<!-- Animate.css -->
	<link rel="stylesheet" type="text/css" href="/static/css/home/animate.css">
	<!-- Owl -->
	<link rel="stylesheet" type="text/css" href="/static/css/home/owl.css">
	<!-- Main style -->
	<link rel="stylesheet" type="text/css" href="/static/css/home/base.css">
	<link rel="stylesheet" type="text/css" href="/static/css/home/index.css">
	<script>
        window.conf = {
            "APP": ""
        };
	</script>
</head>

<body>
	<!-- 加载动画 -->
	<div class="preloader">
		<img src="/static/images/home/loader.gif" alt="Preloader image">
	</div>
	<!-- 导航菜单栏 -->
	<nav class="navbar">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo url('home/index/index'); ?>"><img src="/static/images/home/logo.png" data-active-url="/static/images/home/logo-active.png" alt=""></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<div class="navbar-right">
					<ul class="nav navbar-nav main-nav pull-left">
						<li><a href="#index"><?php echo $nav['home']; ?></a></li>
						<li><a href="#services"><?php echo $nav['project']; ?></a></li>
						<li><a href="#technical"><?php echo $nav['advantage']; ?></a></li>
						<li><a href="#team"><?php echo $nav['aboutUs']; ?></a></li>
					</ul>
					<div class="pull-left change-lang">
						<div class="islang"><span class="textlang"></span><span class="triangle_border_down"></span></div>
						<div class="lang-list">
							<dl>
								<!--<dd><a href="<?php echo url('home/index/index',['lang'=>'zh-cn']); ?>">简体中文</a></dd>
								<dd><a href="<?php echo url('home/index/index',['lang'=>'en-us']); ?>">English</a></dd>-->
								<dd lang="zh-cn" data-name="changeLang">简体中文</dd>
								<dd lang="en-us" data-name="changeLang">English</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	
	<!-- 头部内容 -->
	<header id="index">
		<div class="owl-twitter owl-carousel">
			<?php if(is_array($banner) || $banner instanceof \think\Collection || $banner instanceof \think\Paginator): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<div class="item text-center">
			    <img src="<?php echo $vo['adFile']; ?>" style="width: 100%;">
			</div>
			<?php endforeach; endif; else: echo "" ;endif; ?>
	</header>
	

	<?php if(count($notice)>0): ?>
	<!-- 公告平台 -->
	<section class="section advertise">
		<div class="container clearfix">
			<ul class="pull-left">
				<?php if(is_array($notice) || $notice instanceof \think\Collection || $notice instanceof \think\Paginator): $i = 0; $__LIST__ = $notice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
				<li><a class="v-ellipsis" href="<?php echo url('home/Index/info',['id'=>$vo['articleId']]); ?>" ><img src="/static/images/home/icons/voice.png" alt="" class="icon" style="margin-right: 10px;"><?php echo $vo['articleTitle']; ?></a></li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			&nbsp;&nbsp;&nbsp;&nbsp;<a class="blue" href="<?php echo url('home/index/list'); ?>" target="_blank"><u><?php echo $nav['more']; ?></u></a>
		</div>
	</section>
    <?php endif; ?>
	<section class="gray-bg" id="services">
		<div class="container">
			<!-- rows start -->
			<div class="row intro-tables">
				
				<div class="col-md-12">
					<div class="intro-table">
						
						<div class="subtitles">
							<h4 class="white heading hide-hover"><?php echo $lnmiTrait['introduction']; ?></h4>
						</div>
						<p class="col-md-8 col-sm-12 col-md-offset-2 text-center">
							<span style="color: #7ba8f1;">lnmi</span><?php echo $lnmiTrait['info']; ?><span style="color: #7ba8f1;">lnmi</span><?php echo $lnmiTrait['token']; ?></p>
					</div>
				</div>
				
				
			</div>
			<!-- rows end -->
		</div>
	</section>
	
	<!-- Services -->
	<section class="section section-padded">
		<div class="container">
			<div class="row text-center title">
				<h3><?php echo $lnmiTrait['trait']; ?></h3>
			</div><br/>
			<div class="row services">
				<div class="col-md-2 col-sm-6">
					<div class="service">
						<div class="icon-holder">
							<img src="/static/images/home/icons/trait-1.png" alt="" class="icon">
						</div>
						<h5 class="heading"><?php echo $lnmiTrait['centralization']; ?></h5>
						<p class="description"><?php echo $lnmiTrait['centralizationInfo']; ?></p>
					</div>
				</div>
				<div class="col-md-2 col-sm-6">
					<div class="service">
						<div class="icon-holder">
							<img src="/static/images/home/icons/trait-2.png" alt="" class="icon">
						</div>
						<h5 class="heading"><?php echo $lnmiTrait['Intelligence']; ?></h5>
						<p class="description"><?php echo $lnmiTrait['IntelligenceInfo']; ?></p>
					</div>
				</div>
				<div class="col-md-2 col-sm-6">
					<div class="service">
						<div class="icon-holder">
							<img src="/static/images/home/icons/trait-3.png" alt="" class="icon">
						</div>
						<h5 class="heading"><?php echo $lnmiTrait['speedUp']; ?></h5>
						<p class="description"><?php echo $lnmiTrait['speedUpInfo']; ?></p>
					</div>
				</div>
				<div class="col-md-2 col-sm-6">
					<div class="service">
						<div class="icon-holder">
							<img src="/static/images/home/icons/trait-4.png" alt="" class="icon">
						</div>
						<h5 class="heading"><?php echo $lnmiTrait['privacy']; ?></h5>
						<p class="description"><?php echo $lnmiTrait['privacyInfo']; ?></p>
					</div>
				</div>
				<div class="col-md-2 col-sm-6">
					<div class="service">
						<div class="icon-holder">
							<img src="/static/images/home/icons/trait-5.png" alt="" class="icon">
						</div>
						<h5 class="heading"><?php echo $lnmiTrait['transaction']; ?></h5>
						<p class="description"><?php echo $lnmiTrait['transactionInfo']; ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php if($lang == 'zh-cn'): ?>
	<div><img src="/static/images/home/big-img.jpg" alt="" class="icon" style="width: 100%;min-height:250px ;"></div>
	<?php else: ?>
	<div><img src="/static/images/home/big-img-1.jpg" alt="" class="icon" style="width: 100%;min-height:250px ;"></div>
	<?php endif; ?>
	<section style="padding-top: 20px;" id="technical">
		<div class="container">
			<?php if($lang == 'zh-cn'): ?>
			<div><a href="/upload/book/inmi钱包链白皮书.pdf" class="blue" style="font-size: 18px;"><u><?php echo $lnmiTrait['download']; ?></u></a></div>
			<div class="superiority" style="padding: 100px 0;"><img src="/static/images/home/technical-dvantage.png" alt="" class="icon"></div>
            <?php else: ?>
			<div><a href="/upload/book/Inmi Chain White paper.pdf" class="blue" style="font-size: 18px;"><u><?php echo $lnmiTrait['download']; ?></u></a></div>
            <div class="superiority" style="padding: 100px 0;"><img src="/static/images/home/technical-dvantage-1.png" alt="" class="icon"></div>
            <?php endif; ?>
		</div>	
	</section>
	<!-- team -->
	<section id="team" class="section gray-bg">
		<div class="container teams">
			<div class="row title text-center">
				<h4 class="white heading hide-hover"><?php echo $team['ourTeam']; ?></h4>
			</div>
			<div class="row">
				<div class="col-md-3 col-sm-6">
					<div class="team text-center">
						<div class="cover">
							<div class="overlay text-center">
								<div><img src="/static/images/home/team/team1.png" alt="" class="icon"></div>
								<h5><?php echo $team['ceo']; ?></h5>
								<div class="light light-white"><?php echo $team['ceoInfo']; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="team text-center">
						<div class="cover">
							<div class="overlay text-center">
								<div><img src="/static/images/home/team/team2.png" alt="" class="icon"></div>
								<h5><?php echo $team['coFounder']; ?></h5>
								<div class="light light-white"><?php echo $team['coFounderInfo']; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="team text-center">
						<div class="cover">
							<div class="overlay text-center">
								<div><img src="/static/images/home/team/team3.png" alt="" class="icon"></div>
								<h5><?php echo $team['legalAdviser']; ?></h5>
								<div class="light light-white"><?php echo $team['legalAdviserInfo']; ?></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="team text-center">
						<div class="cover">
							<div class="overlay text-center">
								<div><img src="/static/images/home/team/team4.png" alt="" class="icon"></div>
								<h5><?php echo $team['darrell']; ?></h5>
								<div class="light light-white"><?php echo $team['darrellInfo']; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- download app -->
	<section class="section">
		<div class="container download-app">
			<div class="row title text-center">
				<h4 class="white heading hide-hover"><?php echo $nav['download']; ?>APP</h4>
			</div>
			<div class="row">
				<div class="col-md-8 col-sm-12 col-md-offset-2 text-center clearfix">
					<div class="pull-left text-right" style="margin-top: 50px;">
						<div style="font-size: 20px;margin-bottom: 12px;"><?php echo lang('safe quick pay'); ?></div>
						<div class="color-999" style="font-size: 16px;"><?php echo lang('download inmc'); ?></div>
					</div>
					<img src="/static/images/home/download-code.png" alt="" class="icon pull-left" style="margin-left: 80px;">
				</div>
			</div>
			<div>
			</div>
		</div>
	</section>
	<!-- 底部  -->
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-sm-2 text-center-mobile">
					<img src="/static/images/home/footer-logo.png" alt="" class="icon">
				</div>
				<div class="col-sm-6 text-center-mobile">
					<div class="row opening-hours">
						<div class="text-center-mobile col-sm-6 col-md-4">
							<div><a class="light-999 light" href="<?php echo url('home/Index/info',['id'=>18]); ?>"><u><?php echo $nav['guide']; ?></u></a></div>
							<div><a class="light-999 light" href="<?php echo url('home/Index/info',['id'=>19]); ?>"><u><?php echo $nav['serviceAgreement']; ?></u></a></div>
						</div>
						<div class="text-center-mobile col-sm-6 col-md-4">
							<div class="light-999 light"><?php echo $nav['contactUs']; ?></div>
							<div class="light-333"><?php echo SysConfig('serviceTel'); ?></div>
							<div class="light-333"><?php echo SysConfig('serviceEmail'); ?></div>
							<div class="light-999 light"><?php echo $nav['workday']; ?>9:00:00~18:00:00</div>
						</div>
					</div>
				</div>
				<div class="col-sm-3 text-center-mobile">
					<img src="<?php echo SysConfig('weixinLogo'); ?>" width="203" height="203" alt="" class="icon">
				</div>
			</div>
		</div>
	</footer>
	
	<!-- 移动端导航 -->
	<div class="mobile-nav">
		<ul>
		</ul>
		<a href="#" class="close-link">关闭</a>
	</div>
	<!-- Scripts -->
	<script src="/static/js/jquery.min.js"></script>
	<script src="/static/js/home/owl.carousel.min.js"></script>
	<script src="/static/js/home/jquery.onepagenav.js"></script>
	<script src="/static/js/home/main.js"></script>
	<script>
	    $(function(){
	        $.load();
	        if(localStorage.getItem('lang')) {
	        	$('.islang').find('.textlang').html(localStorage.getItem('lang'));
	        }else {
	        	$('.islang').find('.textlang').html('简体中文');
	        }
	        
	    });
	    //切换中英文
		$('body').on('mouseover','.change-lang',function(){
			$('.lang-list').show();
		});
		$('body').on('mouseleave','.change-lang',function(){
			$('.lang-list').hide();
		});
        $('body').on('click','.lang-list dl dd',function(){
            var that = this;
//          $('.islang').find('.textlang').html($(that).text());
			localStorage.setItem('lang',$(that).text());
            var data={'lang':$(that).attr('lang')};
            $.get("<?php echo url('home/Index/lang'); ?>",data,function () {
                location.reload();
            })
        });
	</script>
</body>

</html>
