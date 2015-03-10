<?php

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class User extends BaseModel implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $primaryKey = 'user_id';
	public $timestamps = false;
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 * 	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

// 	public static function getUserNameById($user_id){
	// 		$username = DB::table('users')
	// 			->select('')
	// 	}

	public static function get_all_user() {
		$user = DB::table('users')->select('ID', 'user_login', 'is_admin', 'user_email', 'is_admin');
		return $user;
	}


	/**
	 * 保存登录 cookie
	 * @param $user
	 * @return mixed
	 */
	public function save_cookie($user){
		$minutes = 60*24*7;//测试  10minutes
		$cookie_user = new stdClass;
//		$cookie_user->email = $user->user_email;
//		$cookie_user->pass = $user->user_pass;
		$cookie_user_json = json_encode($user);
		$cookie = Cookie::make('user', $cookie_user_json, $minutes);
//		Log::info('LOGIN-COOKIE MAKED'.$cookie_user_json);
		return $cookie;
	}

	public function save_session($user){
//		$sess_user = new stdClass();
//		$sess_user->uid=$user->ID;
//		$sess_user->is_admin=$user->is_admin;
//		$sess_user->username=$user->user_login;
		Log::info("save session:".json_encode($user));
		Session::put('user', json_encode($user) );
	}


	/**
	 * 登录
	 * @param $email
	 * @param $password
	 * @return stdClass
	 */
	public function login($email,$password){
		$validator = Validator::make(
			array(
				'密码' => $password,
				'Email' => $email),
			array(
				'密码' => 'required|between:6,16',
				'Email'=> 'required|between:6,100|exists:users,user_email')
		);
		$res = new stdClass;
		if( $validator->fails() ){
			$res->data = $validator->messages()->all();
			$res->status = false;
		}else{
			try{
				$user = User::where('user_email', $email )->where('user_pass', md5($password))->firstOrFail();
				$res->status = true;
				$res->data = $user;
			}catch(ModelNotFoundException $e){
//				echo $e->getMessage();
				$res->status = false;
				$res->data = array('密码错误');
			}catch(Exception $e){
				$method = __METHOD__;
				$errmsg = $e->getMessage();
				Log::error("$method $errmsg");
			}
		}
		return $res;
	}

	/**
	 * 注册
	 * @param $email
	 * @param $username
	 * @param $password
	 */
	public function register($email,$username,$password){
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
		$res = new stdClass();
		if( $validator->fails() ){
			$res->data = $validator->messages()->all();
			$res->status = false;
		}else{
			$user = new User;
			$user->user_login = $username;
			$user->user_email = $email;
			$user->user_pass = md5($password);
			date_default_timezone_set("Asia/Shanghai");
			$user->user_registered = date('Y-m-d H:i:s',time());
			$user->is_admin = 'f';
			$user->user_id = $user->get_new_pk();
			if($user->save()){
				$this->save_session($user);
				$res->status = true;
			}else{
				$res->status = false;
				$res->data = array("注册失败");
			}
		}
		return $res;
	}


	/**
	 * 从
	 * @param null $sess_user_json
	 * @return null
	 */
	public static function get_name_from_session($sess_user_json = null) {
		if (is_null($sess_user_json)) {
			$sess_user_json = Session::get('user');
		}
		if (!is_null($sess_user_json)) {
			$sess_user = json_decode($sess_user_json);
			$username = $sess_user->user_login;
		} else {
			$username = null;
		}
		return $username;
	}

	public static function get_user_from_session() {
		$sess_user_json = Session::get('user');

		if (!is_null($sess_user_json)) {
			$user = json_decode($sess_user_json);
		} else {
			$user = null;
		}
		return $user;
	}



	public static function get_userid_from_session($sess_user_json = null) {
		if (is_null($sess_user_json)) {
			$sess_user_json = Session::get('user');
		}
		if (!is_null($sess_user_json)) {
			$sess_user = json_decode($sess_user_json);
			$user_id = $sess_user->user_id;
		} else {
			$user_id = null;
		}
		return $user_id;
	}

	public static function username_exist($username) {
		return DB::table('users')->where('user_login', $username)->count();
	}

	public static function email_exist($email) {
		return DB::table('users')->where('user_email', $email)->count();
	}

}
