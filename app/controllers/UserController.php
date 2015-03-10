<?php
/**
 * User register,login,logout,forget pass,change info
 * @author liujianlong
 *
 */



class UserController extends BaseController {




	public function admin(){
		
	}
	
	/**
	 * User register
	 * @param unknown $param page|action
	 * @return unknown
	 */
	public function register(){
		$msg = '';
		$method = Input::get('method');
		$resp = null;
		$error = '';

		if(is_null($method) ){
			//register page
			$resp = View::make( 'user/regist',
				array('title'=>Lang::get('user.REGISTER') ));//'checked'=>$checked));//'hide_div'='login_div', ) );//return $view;
		}else if( $method  === 'action'){
			//register action
			$username =  Input::get('reg_username');
			$password = Input::get('reg_password');
			$email =  Input::get('reg_email');

			Log::info("$username $email $password");

			$user_model = new User;
			$res = $user_model->register($email,$username,$password);
			if( !$res->status ) {
				//验证注册失败
				$resp = View::make( 'user/regist',
					array('title'=>Lang::get('user.REGISTER'),
						'msgs'=>$res->data,
						'reg_email_save'=>$email,
						'reg_username_save'=>$username));
			}else{
				//验证注册成功
				$resp = Redirect::action('PostController@index');
				$this->pagecache->update_index();
			}
		}else{
			$error = '未定义操作';//App::abort(404);
		}
		if(strlen($error)>0){
			return Redirect::route('error', array($error));
		}else{
			return $resp;
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
	public function login()
	{
		$INFO_ST = microtime(1);
		$error = '';
		$method = Input::get('method');
		$resp = null;

		if(is_null($method) ){
			//show login page
			$cookie_user_json = Cookie::get('user');
Log::info('LOGIN PAGE,Cooke get:'.$cookie_user_json);
			if(!is_null($cookie_user_json)){
				$cookie_user = json_decode($cookie_user_json);
				$email = $cookie_user->user_email;
//				$pass = $cookie_user->user_pass;
				$checked = Lang::get('tools.YES');
			}else{
				$email = null;
				$pass = null;
				$checked = Lang::get('tools.NO');
			}
			$resp = View::make( 'user/login',
					array('title'=>Lang::get('user.LOGIN') ,
							'hide_div'=>'reg_div',
							'checked'=>$checked,
							'login_email_save'=>$email
//							'login_pass_save'=>$pass
				) );
		}else if($method ==='action'){
			//login action
			$email =  Input::get('login_email');
			$password = Input::get('login_password');
			$remeber = Input::get('remember');
			$resp_method = Input::get('resp');
			
			$cookie_user_json = Cookie::get('user');
//Log::info('LOGIN ACTION,Cooke get:'.$cookie_user_json);
			if(!is_null($cookie_user_json)){
				$checked = Lang::get('tools.YES');
			}else{
				$checked = Lang::get('tools.NO');
			}
			//Server side param check
			$user_model = new User;
			$res = $user_model->login($email,$password);

			if( !$res->status ) {
				if($resp_method === 'json'){
					//验证fail / 登录失败
					$resp = Response::json(array(
						'status' => false ,
						'msg' => json_encode($res->data)
					));
				}else if($resp_method === 'view'){
					$resp = View::make( 'user/login',
						array('title'=>Lang::get('user.LOGIN') ,
							'msgs'=>$res->data ,
							'login_email_save'=>$email,
							'checked'=>$checked ) );
				}else{
					$error = "返回类型未指定";
				}
			}else{
				//验证成功，登录成功
				$user = $res->data;
				if($remeber === 'remember'){
					//$minutes = 60*24*7;// 7 days$
					$cookie = $user_model->save_cookie($user);
				}else{
					$cookie = null;
				}
				$user_model->save_session($user);

				if($resp_method === 'json'){
					//登录成功
					$resp = Response::json(array('status' => true , 'msg' => $res->data ));
				}else if($resp_method === 'view'){
					$resp = Redirect::route('index');
					$this->pagecache->update_index();
				}else{
					$error = "返回类型未指定";
				}
				if(!is_null($cookie)){
//					$resp = Redirect::route('index');//->withCookie($cookie);
//				}else{
//					$resp = Redirect::route('index')->withCookie($cookie);
					$resp = $resp->withCookie($cookie);
				}
			}
		}else{
			$error = '未定义操作';
		}

		$INFO_RUNTIME = round(1000*(microtime(1)-$INFO_ST),5);
		$method = __METHOD__;
		Log::info("{$method},Runtime:{$INFO_RUNTIME}");

		if(strlen($error )>0){
			return Redirect::route('error', array($error));
		}else{
			return $resp;
		}
	}
	
	public function logout(){
		Session::forget('user');
//		$view_cache = new PageCache;
		$this->pagecache->update_index();
		return Redirect::route('index');
	}

}
