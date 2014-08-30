<?php
use Illuminate\Redis\Database as Redis;
class TestController extends BaseController {
	
	
	public function put(){
		//Session::put('key1', 'put a value');
		//$redis = Redis::db();
		$redis = LRedis::connection();
		//$redis = Redis::connection();
		/*$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);*/
		$redis->set('key1', 'fuck');
		//$redis->set('name', 'Taylor');
		//$last_cnt = DB::table('comments')->join('posts', 'posts.ID', '=', 'comments.comment_post_ID')->where('posts.post_author',1)->where('comment_read',0)->count();
		//return //$last_cnt;
	}
	
	public function get(){
		//$value = Session::get('key1');
		//$redis = new Redis();
		//$redis->connect('127.0.0.1', 6379);
		
		/*$redis = new Redis();
		$redis->connect('127.0.0.1', 6379);*/
		$redis = LRedis::connection();
		$value = $redis->get('key1');
		return 'get:'.$value;
	}
	public function push(){
		$view = View::make('test/push',array('title'=>'push'));
		return $view;
	}
	
	public function sendmail(){
		Mail::send('mail.test', array('a'=>'a'), function($message)
		{
			$message->to('jasondliu@qq.com', 'John Smith')->subject('Welcome!');
		});
		return 1;
	}
	
}
