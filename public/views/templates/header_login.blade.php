<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	@section('title')
	<title>{{$title}}</title>
	@show
	{{ HTML::script('js/lib/jquery-1.11.1.js') }}
	{{ HTML::script('js/lib/jquery-plugin.js') }}
	{{ HTML::script('bootstrap/js/bootstrap.js') }}
	{{ HTML::script('js/lib/tool.js') }}
	{{ HTML::style('bootstrap/css/bootstrap.css') }}
    {{ HTML::style('css/style.css') }}
    
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
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
		@if( !is_null(Session::get('user')) )
			var msgs_x = 0;//$('#msgs_item').offset().top;
			var msgs_y = $('#msgs_item').offset().left;
			var msgs_height = $('#msgs_item').outerHeight();
			var msgs_width = $('#msgs_item').outerWidth();
			console.log("x:"+msgs_x+",y:"+msgs_y);
			console.log("oh:"+msgs_height+",ow:"+msgs_width);
			var msgbox_width = $('#msgbox').outerWidth();
			//msgbox位置
			var msgbox_X = msgs_y+msgs_width*5/8-msgbox_width/2;
			var msgbox_Y = msgs_height*7/8;
			$('#msgbox').css("left",msgbox_X);
			$('#msgbox').css("top",msgbox_Y);
			console.log('box x:'+msgbox_X+',y:'+msgbox_Y);
			
			var msgbox_head_height = $('#msgbox_head').outerHeight();
			var msgbox_head_width = $('#msgbox_head').outerWidth();
			
			var msgbox_head_X = msgbox_X+ msgbox_width/2 - msgbox_head_width ;
			var msgbox_head_Y = msgbox_Y - msgbox_head_height + 5;
			$('#msgbox_head').css('left', msgbox_head_X);
			$('#msgbox_head').css("top",msgbox_head_Y);
	
			//init hide
			$('#msgbox').hide();
			$('#msgbox_head').hide();
			//click msgs_item,show comment diag
			$('#msgs_item').bind('click',function(e){
				stopPropagation(e);//stop bubble
				$('#msgbox').toggle();
				$('#msgbox_head').toggle();
				$('#msgs_count_badge').text('');
				//console.log( $('#msgbox').is(":hidden") );
			});
			//hide comment diag
			$(document).click(function(){
				if($("#msgbox").is(":visible")){
					$('#msgbox').hide();
					$('#msgbox_head').hide();
				}
			});
			//msgbox click stop bubble
			$('#msgbox').bind('click',function(e){
	            stopPropagation(e);
	        });

			var socket = io('http://localhost:3000');
console.log('user:'+{{Session::get('user')}});
			socket.emit('reguser', {{ Session::get('user') }});
			socket.on('newcomm',function(data){
				console.log('NewComm:'+data.cnt );
				$('#msgs_count_badge').text(data.cnt);
				$('#msgbox').text('新评论'+data.cnt+'条');
			});
			
		@endif
		
	});

	function stopPropagation(e) {
        if (e.stopPropagation) 
            e.stopPropagation();
        else 
            e.cancelBubble = true;
    }
    
// 	$(window).resize(function(){
// 		$('#msgbox').css("left",msgs_count_y+msgs_count_width/2-msgbox_width/2);
// 		$('#msgbox').css("top",msgs_count_x+msgs_count_height);
// 	});
	
</script>
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
					
					@if( !empty( $username ) )
						<li><a href="#" id="msgs_item">消息<span class="badge" id="msgs_count_badge"></span></a></li>
						<div id="msgbox_head"><img id="msgbox_head_img" src="{{url()}}/img/tri.png" /></div>
						<div id="msgbox">
						新评论x条
							<!--<ul class="list-group">
							  <li class="list-group-item"></li>
							   <li class="list-group-item">Dapibus ac facilisis in</li> 
							</ul>-->
						</div>
					@endif
					
					
					<li><form class="navbar-form">
					<input class="span2" type="text" placeholder="搜一下">
					<button type="submit" class="btn">搜索</button>
				</form></li>
				</ul>
				
				<ul class="nav navbar-nav navbar-right">
						
					@if( empty( $username ) ) 
					<li><input type="button" class="btn btn-default navbar-btn"
						onclick="javascript:window.location.href='{{url()}}/user/register';"
						value="注册" /></li>
					<li>&nbsp;&nbsp;&nbsp;</li>
					@endif
						
					@if( !empty( $username ) )
						<li class="dropdown">
					@else
						<li>
					@endif
					
					@if( empty( $username ) ) 
						<input type="button" class="btn btn-default navbar-btn" onclick="javascript:window.location.href='{{url()}}/user/login/page';" value="登录" />
					@else
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $username }}<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{url()}}">主页</a></li>
							<li><a href="#">设置</a></li>
							<li class="divider"></li>
							<li><a href="{{url()}}/user/logout">退出</a></li>
						</ul>
					@endif
						</li>
					
					<!-- <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li> -->
				</ul>
			</div><!-- collapse -->
		</div><!-- container -->
	</div><!-- navbar -->