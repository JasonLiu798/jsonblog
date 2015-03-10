@include('templates/header_navoff')
{{ HTML::style('css/user.css') }}

<div class="container" id="login_div">
	{{ Form::open(array('url' => '/login', 'method' => 'post',
		'id'=> 'login_form' ,'class' => 'form-signin','role'=>'form')) }}
		<input type="hidden" name="method" value="action">
		<input type="hidden" name="resp" value="view">
		<div class="form_head">
			<div class ="form_title"><h3>登录</h3></div>
			<div class ="change_link"><h3><a href="{{url('/reg')}}" id="reg_form_show">注册</a></h3></div>
		</div>
        <input type="email"     name="login_email" id="login_email" class="form-control" value="{{isset($login_email_save)?$login_email_save:''}}" placeholder="邮箱" required autofocus> 
        <input type="password"  name="login_password" id="login_password" class="form-control" value="{{isset($login_pass_save)?$login_pass_save:''}}" placeholder="密码" required>
        <div class="form_bottom">
	        <div class="bottom_left">
	          <label>
	            <input type="checkbox" name="remember" value="remember" @if( $checked===Lang::get('tools.YES') ) checked="checked" @endif />&nbsp;记住我
	          </label>
	        </div>
	        <div class="bottom_right"><a href="#">忘记密码？</a></div>
        </div>
        @if( isset($msgs) && count($msgs ) > 0)
        <div class="alert alert-danger err_box" role="alert">
   		   <ul>
   		   		@foreach ($msgs as $msg)
   		   			<li>{{ $msg }}</li>
   		   		@endforeach
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
