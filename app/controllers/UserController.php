<?php

class UserController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	
	public function processor($type,$param='page'){
		Log::info("UserCon,T:".$type.",P:".$param);
		Log::info(gettype($type));
		$controller = new UserController;
		switch( $type ){
			case 'login':
				if($param === 'page'){
					$controller->login_pre();
				}else if($param === 'action'){
					$controller->login();
				}
				break;
			case 'logout':
				$controller->logout();
				break;
			case 'reg':
				if($param === 'page'){
					Log::info('in reg page');
					UserController::register_pre();
				}else if ($param === 'action'){
					$controller->register();
				}else{
					return Redirect::action('PostController@index');
				}
				break;
			default:
				return Redirect::action('PostController@index');
		}
	}
	
	public static function register_pre(){
		$view = View::make( 'user/reg_login',
				array('title'=>Lang::get('user.REGISTER'),
				'hide_div'=>'login_div' ) );
		Log::info('view maked');
		return $view;
	}

	public function register(){
		$username =  Input::get('username');
		$password = Input::get('password');
		$email =  Input::get('email');
		$validator = Validator::make(
			array(
					'name' => $username,
					'password' => $password,
					'email' => $email),
			array(
				'name' => 'required|between:6,32|unique:users,user_login|alpha_num',
				'password' => 'required|between:6,16',
				'email'=> 'required|between:6,100|unique:users,user_email')
		);
		
		$user = new User;
		$user->user_login =$username;
		$user->user_email = $email;
		$user->user_pass = md5($password);
		
		date_default_timezone_set("Asia/Shanghai");
		$user->user_registered = date('Y-m-d H:i:s',time());
		$user->is_admin = 'f';

		/**
		 * retrun to the register page
		 */
		if( $validator->fails() ){
			$view = View::make( 'user/reg_login', 
					array('title'=>Lang::get('user.REGISTER'),
					'msgs'=>$validator->messages(),
					'hide_div'=>'login_div' ) );
			return $view;
		}
		/**
		 * check pass,reg user save
		 */
		if($user->save()>0){
			Session::put('username', $username );
			return Redirect::action('PostController@index');
		}else{
			//save failed 
			return Redirect::route('/error'.'服务器错误');
		}
	}
	
	/**
	 * ajax check: register parameter check
	 */
	public function chk_parameter(){
		$type = Input::get('type');
		$msg = new stdClass;
		if(!empty($type)){
			if($type === 'username'){
				if(User::username_exist(Input::get('username')) > 0){
					$msg->success = false;
					$msg->text = Lang::get('reminders.USERNAME_EXIST');
				}else{
					$msg->success = true;
				}
			}else if($type === 'email'){
				if(User::email_exist(Input::get('email')) >0){
					$msg->success = false;
					$msg->text = Lang::get('reminders.EMAIL_EXIST');
				}else{
					$msg->success = true;
				}
			}else{
				$msg->success = false;
				$msg->text = Lang::get('reminders.WRONG_TYPE');
			}
		}else{
			$msg->success=false;
			$msg->text = Lang::get('reminders.TYPE_NOTEXIST');
		}
		return json_encode($msg);
	}
	
	public function login_pre()
	{
		$view = View::make( 'user/login', array('title'=>Lang::get('user.LOGIN') ) );
		return $view;
	}
	
	public function login()
	{
		$email =  Input::get('email');
		$password = Input::get('password');
		$validator = Validator::make(
				array(
						'password' => $password,
						'email' => $email),
				array(
						'password' => 'required|between:6,16',
						'email'=> 'required|between:6,100|exists:users,user_email')
		);
		if( $validator->fails() ){
			$view = View::make( 'user/login', array('title'=>Lang::get('user.REGISTER'),
					'msgs'=>$validator->messages() ) );
			return $view;
		}
		User::login();
		
	}

}
