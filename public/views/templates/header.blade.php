<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	@section('title')
	<title>{{$title}}</title>
	@show
	{{ HTML::script('bower_components/jquery/dist/jquery.js') }}
	{{ HTML::script('js/lib/jquery-plugin.js') }}
	{{ HTML::script('bower_components/bootstrap/dist/js/bootstrap.js') }}
	{{ HTML::script('js/lib/tool.js') }}

	{{ HTML::style('bower_components/bootstrap/dist/css/bootstrap.css') }}
    {{ HTML::style('css/style.css') }}

	{{ HTML::script('js/lib/jquery-form.js') }}
	{{ HTML::script('js/user/ajaxlogin.js') }}


	{{ HTML::script('js/lib/tool.js') }}

    <script src="{{url()}}:3000/socket.io/socket.io.js"></script>
	@if (! empty ( $next_url ))
		<META HTTP-EQUIV="REFRESH" CONTENT="100;URL={{$next_url}}?>" />
	@endif

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		{{ HTML::script('bootstrap/js/html5shiv.min.js') }}
		{{ HTML::script('bootstrap/js/respond.min.js') }}
	<![endif]-->
<script type="text/javascript">
$(document).ready(function(){

	$('#msg_box').popover();

	var node_url = "http://"+window.location.hostname+":3000";
// console.log(node_url);
	var socket = io(node_url);

	var user = {{ is_null(Session::get('user'))?'null':Session::get('user') }};
	init_socket_io(user);

	function init_socket_io(user){
		if(user != null){
			console.log('user:'+ user.uid +',name:'+user.username );
			socket.emit('reguser', user );
			socket.on('newcomm',function(data){
				console.log('NewComm:'+data.cnt );
				$('#msgs_count_badge').text(data.cnt);
				$('#msg_box').attr('data-content','新评论'+data.cnt+'条');
			});
		}
	}

	$('#msg_box').click(function(){
		if( $('#msgs_count_badge').text().length>0){
			$('#msgs_count_badge').text('');
		}
	});

});

</script>
</head>

<body>
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		{{--<button type="button" class="btn btn-default navbar-btn" data-toggle="modal" data-target="#logindiag">--}}
			{{--登录--}}
		{{--</button>--}}




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
					<li @if( isset($nav) && $nav === Constant::$NAV_IDX ) class="active" @endif><a href="{{url()}}">主页</a></li>
					<li @if( isset($nav) && $nav === Constant::$NAV_MSG ) class="active" @endif><a href="{{url()}}/message">留言</a></li>
					<li @if( isset($nav) && $nav === Constant::$NAV_ABOUT ) class="active" @endif><a href="{{url()}}/about">关于</a></li>

					@if( isset( $username ) && !is_null($username) )
						<li @if( isset($nav) && $nav === Constant::$NAV_ADMIN ) class="active" @endif>
                			<a href="{{url()}}/admin/post">管理</a>
                		</li>
						<li>
                			<a href="#" id="msg_box" data-container="body" data-animation="true" rel="popover" data-placement="bottom" data-html="true" data-trigger="click" data-content="暂无消息">消息<span class="badge" id="msgs_count_badge"></span></a>
                		</li>
					@endif

					<li>
						<form class="navbar-form" method="post" action="{{url()}}/post/search" accept-charset="utf-8" role="form" id="search_form">
							<input name="page" type="hidden" value="1"/>
							<input name="searchtext" class="span2" type="text" placeholder="搜一下" />
							<button type="submit" class="btn btn-default">搜索</button>
						</form>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right" id="navright_nologin"
				@if( isset($username) || !is_null($username) ) style="display:none" @endif>
					<li><input type="button" class="btn btn-default navbar-btn"
							   onclick="javascript:window.location.href='{{url()}}/reg';"
							   value="注册" /></li>
					<li>&nbsp;&nbsp;&nbsp;</li>
					<li>
						<input type="button" class="btn btn-default navbar-btn" onclick="javascript:window.location.href='{{url()}}/login';" value="登录" />
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right" id="navright_login"
				@if( !isset($username) && is_null($username) ) style="display:none" @endif>
					<li class="dropdown">
						<a id="navright_user_profile_name" href="#" class="dropdown-toggle"
						   data-toggle="dropdown">{{ !is_null($username)?$username:'' }}
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="{{url()}}/admin/post">管理</a></li>
							<li><a href="#">设置</a></li>
							<li class="divider"></li>
							<li><a href="{{url()}}/logout">退出</a></li>
						</ul>
					</li>
				</ul>

					<!-- <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li> -->
				</ul>
			</div><!-- collapse -->
		</div><!-- container -->
	</div><!-- navbar -->

	<!--登录框-->
	<div class="modal fade" id="logindiag" tabindex="-1" role="dialog"
		 aria-labelledby="登录" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">登录</h4>
				</div>
				<div class="modal-body">
					{{ Form::open(array('url' => '/login', 'method' => 'post',
    'id'=> 'ajax_login_form' ,'class' => 'form-signin','role'=>'form')) }}
					<input type="hidden" name="method" value="action">
					<input type="hidden" name="resp" value="json">
					<input type="email"     name="login_email" id="login_email" class="form-control" value="{{isset($login_email_save)?$login_email_save:''}}" placeholder="邮箱" required autofocus>
					<input type="password"  name="login_password" id="login_password" class="form-control" value="{{isset($login_pass_save)?$login_pass_save:''}}" placeholder="密码" required>
					<div class="form_bottom">
						<div class="bottom_left"><a href="#">忘记密码？</a></div>
					</div>

					<div id="login_diag_alert" class="alert alert-danger alert-dismissible"
						 role="alert"
						 style="display:none">
						<ul id="login_diag_alert_ul"></ul>
					</div>
					<button type="button" class="btn btn-lg btn-primary btn-block"
							id="login_diag_submit">{{
							Lang::get('user.LOGIN')  }}</button>
					{{ Form::close() }}
				</div>
				{{--<div class="modal-footer">--}}
					{{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
					{{--<button type="button" class="btn btn-primary">登录</button>--}}
				{{--</div>--}}
			</div>
		</div>
	</div>