<?php
/**
 * User register,login,logout,forget pass,change info
 * @author liujianlong
 *
 */
class UserController extends BaseController {
	/*
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
					return UserController::register_pre();
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
	*/
	
	/**
	 * User register
	 * @param unknown $param page|action
	 * @return unknown
	 */
	public function register($param){
		if($param === 'page'){
			Log::info('Register show page');
			$checked = Lang::get('tools.NO');
			$view = View::make( 'user/reg_login',
					array('title'=>Lang::get('user.REGISTER'),
							'hide_div'=>'login_div','checked'=>$checked ) );
			return $view;
		}else if($param === 'action'){
			$username =  Input::get('reg_username');
			$password = Input::get('reg_password');
			$email =  Input::get('reg_email');
			$validator = Validator::make(
					array(
							'姓名' => $username,
							'密码' => $password,
							'Email' => $email),
					array(
							'姓名' => 'required|between:6,32|unique:users,user_login|alpha_num',
							'密码' => 'required|between:6,16',
							'Email'=> 'required|between:6,100|unique:users,user_email')
			);
			
			$user = new User;
			$user->user_login =$username;
			$user->user_email = $email;
			$user->user_pass = md5($password);
			
			date_default_timezone_set("Asia/Shanghai");
			$user->user_registered = date('Y-m-d H:i:s',time());
			$user->is_admin = 'f';
			
			$checked = Lang::get('tools.NO');
			/**
			 * retrun to the register page
			 */
			if( $validator->fails() ){
				$view = View::make( 'user/reg_login',
						array('title'=>Lang::get('user.REGISTER'),
								'msgs'=>$validator->messages(),
								'reg_email_save'=>$email,
								'reg_username_save'=>$username,
								'hide_div'=>'login_div',
								'checked'=>$checked ) );
				return $view;
			}
			
			/**
			 * check pass,reg user save
			 */
			if($user->save()>0){
				$get_last_user_id_sql = "SELECT LAST_INSERT_ID() ID";
				$uid = DB::select($get_last_user_id_sql);
Log::info('Uid:'.$uid[0]->ID);
				$sess_user = new stdClass();
				$sess_user->uid=$uid[0]->ID;
				$sess_user->is_admin=$user->is_admin;
				$sess_user->username=$username;
				Session::put('user', json_encode($sess_user) );
// 				Session::put('is_admin',$user->is_admin);
// 				Session::put('user',$username );
				return Redirect::action('PostController@index');
			}else{
				//user save failed
				return Redirect::route('/error'.'服务器错误');
			}
		}else{
			App::abort(404);
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
	
	/**
	 * USER LOGIN
	 * @param unknown $param
	 * @return unknown
	 */
	public function login($param)
	{
		$sess_user_json = Session::get('user');
		if(! is_null($sess_user_json)){
			Redirect::action('PostController@index');
		}
		
		if($param === 'page'){
			
			$cookie_user_json = Cookie::get('user');
			Log::info('LOGIN PAGE,Cooke get:'.$cookie_user_json);
			if(!is_null($cookie_user_json)){
				
				$cookie_user = json_decode($cookie_user_json);
				$email = $cookie_user->email;
				$pass = $cookie_user->pass;
				$checked = Lang::get('tools.YES');
			}else{
				$email = null;
				$pass = null;
				$checked = Lang::get('tools.NO');
			}
			$view = View::make( 'user/reg_login',
					array('title'=>Lang::get('user.LOGIN') ,
							'hide_div'=>'reg_div',
							'checked'=>$checked,
							'login_email_save'=>$email ,
							'login_pass_save'=>$pass
				) );
			return $view;
		}else if($param ==='action'){
			
			$email =  Input::get('login_email');
			$password = Input::get('login_password');
			$remeber = Input::get('remember');
			
			$cookie_user_json = Cookie::get('user');
			Log::info('LOGIN ACTION,Cooke get:'.$cookie_user_json);
			if(!is_null($cookie_user_json)){
				$checked = Lang::get('tools.YES');
			}else{
				$checked = Lang::get('tools.NO');
			}
			//Server side param check
			$validator = Validator::make(
					array(
							'密码' => $password,
							'Email' => $email),
					array(
							'密码' => 'required|between:6,16',
							'Email'=> 'required|between:6,100|exists:users,user_email')
			);
			if( $validator->fails() ){
				$view = View::make( 'user/reg_login',
						array('title'=>Lang::get('user.LOGIN'),
								'msgs'=>$validator->messages(),
								'login_email_save'=>$email,
								'checked'=>$checked,
								'hide_div'=>'reg_div' ) ); 
				return $view;
			}
			if($remeber === 'remember'){
				//$minutes = 60*24*7;// 7 days
				$minutes = 10;//测试  10minutes
				$cookie_user = new stdClass;
				$cookie_user->email = $email;
				$cookie_user->pass = $password;
				$cookie_user_json = json_encode($cookie_user);
				$cookie = Cookie::make('user', $cookie_user_json, $minutes);
Log::info('LOGIN-COOKIE MAKED'.$cookie_user_json);
			}else{
				$cookie = null;
			}
			$users = User::login($email,$password);
			if(!empty($users) && count($users)==1 ){
				$user = $users[0];
				$sess_user = new stdClass();
				$sess_user->uid=$user->ID;
				$sess_user->is_admin=$user->is_admin;
				$sess_user->username=$user->user_login;
				Session::put('user', json_encode($sess_user) );
				$post_controller = new PostController();
				if(is_null($cookie)){
					return Redirect::route('index');//->withCookie($cookie);
				}
				return Redirect::route('index')->withCookie($cookie);
				
			}else{//pass wrong
				$user_msgs = array();
				array_push($user_msgs,Lang::get('validation.PASS_WRONG'));
				$view = View::make( 'user/reg_login',
						array('title'=>Lang::get('user.LOGIN'),
								'user_msgs'=>$user_msgs,
								'login_email_save'=>$email,
								'hide_div'=>'reg_div' ) );
				return $view;
			}
		}else{
			App::abort(404);
		}
	}
	
	public function logout()
	{
		//Log::info('Logout user:'.$uid);
		Session::forget('user');
		//$sess_user_json = Session::get($uid);
		return Redirect::action('PostController@index');
		//$sess_user_json = json_decode($sess_user);
	}

}
