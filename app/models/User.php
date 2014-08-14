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
	
	
	public static function login(){
		$username = Input::get('username');
		$password = md5(Input::get('password'));
		$user = DB::table('users')->select('user_login','is_admin')
			->where('user_login',$username)->andWhere('user_pass',md5($password))->get();
		
		return $user;
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
