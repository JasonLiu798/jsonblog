@include('templates/header_navoff')

{{ HTML::style('css/user.css') }}

<script type="text/javascript">
$().ready(function(){
	$("#{{ $hide_div }}").hide();//default: login_div,reg_div
	$('#login_form_show').bind("click",function(){
		 //alert("click");
		 $("#login_div").show();//.attr("display","block");
		 $("#reg_div").hide();//attr("display","none");
	});
	$('#reg_form_show').bind("click",function(){
		 $("#login_div").hide();//.attr("display","none");
		 $("#reg_div").show();//.attr("display","block");
	});
});

</script>
<div class="container" id="reg_div" >
	{{ Form::open(array('url' => 'user/reg/action', 'method' => 'post',
		'id'=> 'register_form' ,'class' => 'form-signin','role'=>'form')) }}
		<div class="form_head">
			<div class ="form_title" id="form_title"><h3>注册</h3></div>
			<div class ="change_link" id="change_link"><h3><a href="#login" id="login_form_show">登录</a></h3></div>
		</div>
        <input type="email"    name="reg_email" id="reg_email" class="form-control" value="{{isset($reg_email_save)?$reg_email_save:''}}" placeholder="邮箱" required autofocus>
        <input type="username" name="reg_username" id="reg_username" class="form-control" value="{{isset($reg_username_save)?$reg_username_save:''}}" placeholder="用户名" required>
        <input type="password" name="reg_password" id="reg_password" class="form-control" placeholder="密码" required>
        
        @if( isset($msgs) && count($msgs->all() ) > 0)
        <div class="alert alert-danger" role="alert">
   		   <ul>
   		   		@foreach ($msgs->all() as $msg)
   		   			<li>{{ $msg }}</li>
   		   		@endforeach
   		   </ul>
    	</div>
    	@endif
    	
    	<button class="btn btn-lg btn-primary btn-block" type="submit">{{ Lang::get('user.REGISTER')  }}</button>
	{{ Form::close() }}

</div> <!-- /container -->

<div class="container" id="login_div">
	{{ Form::open(array('url' => 'user/login/action', 'method' => 'post',
		'id'=> 'login_form' ,'class' => 'form-signin','role'=>'form')) }}
		<div class="form_head">
			<div class ="form_title"><h3>登录</h3></div>
			<div class ="change_link"><h3><a href="#register" id="reg_form_show">注册</a></h3></div>
		</div>
        <input type="email"     name="login_email" id="login_email" class="form-control" value="{{isset($login_email_save)?$login_email_save:''}}" placeholder="邮箱" required autofocus>
        <input type="password"  name="login_password" id="login_password" class="form-control" value="{{isset($login_pass_save)?$login_pass_save:''}}" placeholder="密码" required>
        <div class="form_bottom">
	        <div class="bottom_left">
	          <label>
	            <input type="checkbox" name="remember" value="remember"
	            @if($checked===Lang::get('tools.YES') )
	            	checked="checked"
	            @endif
	            >&nbsp;记住我
	          </label>
	        </div>
	        <div class="bottom_right"><a href="#">忘记密码？</a></div>
        </div>
        @if( (isset($msgs) && count($msgs->all() ) > 0)|| isset($user_msgs)  )
        <div class="alert alert-danger err_box" role="alert">
   		   <ul>
   		   @if(isset($msgs) && count($msgs->all() ) > 0)
   		   		@foreach ($msgs->all() as $msg)
   		   			<li>{{ $msg }}</li>
   		   		@endforeach
   		   @endif
   		   @if(isset($user_msgs))
   		   		@foreach ($user_msgs as $msg)
   		   			<li>{{ $msg }}</li>
   		   		@endforeach
   		   @endif
   		   </ul>
    	</div>
    	@endif
    	<button class="btn btn-lg btn-primary btn-block" type="submit">{{ Lang::get('user.LOGIN')  }}</button>
	{{ Form::close() }}
</div> <!-- /container -->

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{url()}}/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
