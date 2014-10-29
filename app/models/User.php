<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

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
	
	
	public static function login($username,$password){
		$user = DB::table('users')->select('ID','user_login','is_admin')
			->where('user_email',$username)->where('user_pass',md5($password))->get();
		return $user;
	}
	
	public static function get_name_from_session($sess_user_json){
		//$sess_user_json = Session::get('user');
		if(! is_null($sess_user_json)){
			$sess_user = json_decode($sess_user_json);
			$username = $sess_user->username;
		}else{
			$username = null;
		}
		return $username;
	}
	
	public static function getUserIDFromSession($sess_user_json){
		if(! is_null($sess_user_json)){
			$sess_user = json_decode($sess_user_json);
			$user_id = $sess_user->uid;
		}else{
			$user_id = null;
		}
		return $user_id;
	}
	
	public static function register(){
		
			
	}
	
	public static function username_exist($username){
		return DB::table('users')->where('user_login',$username)->count();
	}
	
	public static function email_exist($email){
		return DB::table('users')->where('user_email',$email)->count();
	}
	
	
	
	
	
}
