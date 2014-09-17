<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	@section('title')
	<title>{{$title}}</title>
	@show
	{{ HTML::script('js/jquery-1.11.1.js') }}
	{{ HTML::script('js/jquery-plugin.js') }}
	{{ HTML::script('bootstrap/js/bootstrap.js') }}
	{{ HTML::script('js/tool.js') }}
	{{ HTML::style('bootstrap/css/bootstrap.css') }}
    {{ HTML::style('css/style.css') }}
	@if (! empty ( $next_url ))
		<META HTTP-EQUIV="REFRESH" CONTENT="100;URL={{$next_url}}?>" />
	@endif

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		{{ HTML::script('bootstrap/js/html5shiv.min.js') }}
		{{ HTML::script('bootstrap/js/respond.min.js') }}
	<![endif]-->
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
				<a class="navbar-brand" href="{{url()}}">Async Blog</a>
			</div>
			<!-- end of navbar-header -->
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="{{url()}}">主页</a></li>
					<li><a href="#">关于</a></li>
					
					<li><form class="navbar-form">
					<input class="span2" type="text" placeholder="搜一下">
					<button type="submit" class="btn">搜索</button>
				</form></li>
				</ul>
				
				<ul class="nav navbar-nav navbar-right">
					<li><input type="button" class="btn btn-default navbar-btn"
						onclick="javascript:window.location.href='{{url()}}/user/reg/page';"
						value="注册" /></li>
					<li>&nbsp;&nbsp;&nbsp;</li>
					<li>
						<input type="button" class="btn btn-default navbar-btn" onclick="javascript:window.location.href='{{url()}}/user/login/page';" value="登录" />
					</li>
				</ul>
			</div><!-- collapse -->
		</div><!-- container -->
	</div><!-- navbar -->