@include('templates/header_navoff')

{{ HTML::style('css/user.css') }}

<div class="container" id="reg_div" >
	{{ Form::open(array('url' => '/reg', 'method' => 'post',
		'id'=> 'register_form' ,'class' => 'form-signin','role'=>'form')) }}
		<input type="hidden" name="method" value="action"/>
		<div class="form_head">
			<div class ="form_title" id="form_title"><h3>注册</h3></div>
			<div class ="change_link" id="change_link"><h3><a href="{{url('/login')}}" id="login_form_show">登录</a></h3></div>
		</div>
        <input type="email"    name="reg_email" id="reg_email" class="form-control" value="{{isset($reg_email_save)?$reg_email_save:''}}" placeholder="邮箱" required autofocus>
        <input type="username" name="reg_username" id="reg_username" class="form-control" value="{{isset($reg_username_save)?$reg_username_save:''}}" placeholder="用户名" required>
        <input type="password" name="reg_password" id="reg_password" class="form-control" placeholder="密码" required>
        
        @if( isset($msgs) && count($msgs ) > 0)
        <div class="alert alert-danger" role="alert">
   		   <ul>
   		   		@foreach ($msgs as $msg)
   		   			<li>{{ $msg }}</li>
   		   		@endforeach
   		   </ul>
    	</div>
    	@endif
    	
    	<button class="btn btn-lg btn-primary btn-block" type="submit">{{ Lang::get('user.REGISTER')  }}</button>
	{{ Form::close() }}
</div>
        

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{url()}}/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
