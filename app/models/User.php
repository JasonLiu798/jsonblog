<?php

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $primaryKey = 'ID';
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

	public static function login($username, $password) {
		$user = DB::table('users')->select('ID', 'user_login', 'is_admin')
		                          ->where('user_email', $username)->where('user_pass', md5($password))->get();
		return $user;
	}

	public static function get_name_from_session($sess_user_json = null) {
		if (is_null($sess_user_json)) {
			$sess_user_json = Session::get('user');
		}
		if (!is_null($sess_user_json)) {
			$sess_user = json_decode($sess_user_json);
			$username = $sess_user->username;
		} else {
			$username = null;
		}
		return $username;
	}

	public static function get_userid_from_session($sess_user_json = null) {
		if (is_null($sess_user_json)) {
			$sess_user_json = Session::get('user');
		}
		if (!is_null($sess_user_json)) {
			$sess_user = json_decode($sess_user_json);
			$user_id = $sess_user->uid;
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
