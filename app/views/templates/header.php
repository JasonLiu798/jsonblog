<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php if (! empty ( $next_url )) {?>
		<META HTTP-EQUIV="REFRESH" CONTENT="100;URL=<?=$next_url?>" />;
	<?php }?>
	
	<!-- jQuery -->
<script type="text/javascript" src="<?=url('js/jquery-1.11.1.js') ?>"></script>
<script type="text/javascript" src="<?=url('js/jquery-plugin.js') ?>"></script>

	
	<!-- Bootstrap -->
<link rel="stylesheet" href="<?=url('bootstrap/css/bootstrap.css')?>">
	<!-- CSS userdefine -->
<link type="text/css" rel="stylesheet" href="<?=url('css/style.css') ?>" />


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="<?=url('bootstrap/js/html5shiv.min.js')?>"></script>
      <script src="<?=url('bootstrap/js/respond.min.js')?>"></script>
    <![endif]-->

<script src="<?=url('bootstrap/js/bootstrap.js')?>"></script>

<title><?php echo $title ?></title>
</head>

<body>
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Async Blog</span> <span class="icon-bar"></span>
					<span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?=url() ?>">Async Blog</a>
			</div>
			<!-- end of navbar-header -->


			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="<?=url() ?>">主页</a></li>
					<li><a href="#">目录</a></li>
					<li><a href="#">关于</a></li>
				</ul>
				<!-- 
					<form class="navbar-form">
						<input class="span2" type="text" placeholder="搜一下">
						<button type="submit" class="btn">搜索</button>
					</form>
					 -->
				<ul class="nav navbar-nav navbar-right">
						<?php
						$user_name = null; // $this->session->userdata ( 'username' );
						$is_admin = null; // $this->session->userdata ( 'is_admin' );
						?>
						
						<?php if (empty ( $user_name )) { ?>
					<li><input type="button" class="btn btn-default navbar-btn"
						onclick="javascript:window.location.href='<?=url()?>users/signup';"
						value="注册" /></li>
					<li>&nbsp;&nbsp;&nbsp;</li>
						<?php } ?>
						
					<li
					<?php if (! empty ( $user_name )) { echo " class=\"dropdown\"";} ?>>
					<?php if (empty ( $user_name )) { ?>
						<input type="button" class="btn btn-default navbar-btn"
					onclick="javascript:window.location.href='<?=url()?>users/signin';"
					value="登录" />
					<?php } else { ?>
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$user_name ?><b
						class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="#">主页</a></li>
						<li><a href="#">设置</a></li>
						<li class="divider"></li>
						<li><a href="#">退出</a></li>
					</ul>
					<?php }?>
					</li>
					<!-- <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li> -->
				</ul>

			</div>
			<!-- collapse -->

		</div>
		<!-- container -->

	</div>
	<!-- navbar -->