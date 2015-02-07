<?php
use Illuminate\Redis\Database as Redis;
class TestController extends BaseController {
	
	public function test(){


		$redis = LRedis::connection();
		$post_model = new Post;
		$comm_model = new Comment;

		$start = microtime(1);

		$res = $post_model->init_ts_pk_set(1);

		if(!$res ){
			echo "error:".$post_model->error;
		}else{
			echo "success";
		}

//		PageCache::admin_update();
//		$b = 2;
//		$a = 9;
//		echo floor(log(16)/log(2))+1;
//		echo is_int($a);

//		while($a!=0 ){
//			echo $a;
//			$a = $a >> 1;
//			$d = $a % $b;
//			echo "/ $a,% $d\n";
//		}



		$time = 1000*(microtime(1) - $start);
		echo "<br/>time:".$time;




//
//

//
////		$latest_posts = $post_model->get_latest_count_post(5,$redis);
////		var_dump($latest_posts);
//
//		$latest_comments = $comm_model->get_latest_comments(5,$redis);
//		var_dump($latest_comments);

//		$pks = $comm_model->get_ts_pk_set(1,5,$redis);
//		var_dump($pks);

//		$p = Post::find(28);
//		var_dump($p);

//		$q = DB::table('posts')->where('post_id','=',28)->get();
//		$q = (Post)$q;
//		var_dump($q);

		//		$res = $p->get_posts_onepage_db(1,5);
//		print_r($res);
//		$p = new Post;
//		$posts =  $p->get_posts_onepage_with_meta(1,4,$redis);

//		$pk_set = $p->get_ts_pk_set(1,Constant::$PAGESIZE,$redis);
//		if(!is_null($pk_set) && count($pk_set >0 )){
//			$res = $p->get_post_from_pkset($pk_set);
//		}
//		$res = $p->get_posts_with_meta(1,Constant::$PAGESIZE);

//		$p->add_meta($posts);
//		foreach($posts as $post){
//			var_dump($post);
//		}




		//		echo $res;
//		$p = new Post;
//		$p->clear_model_cache();
//		$pk_set = $p->get_ts_pk_set(1,Constant::$PAGESIZE,$redis);
//		var_dump($pk_set);
//		$img = new PostImage;


//		$res = User::find(1);
//		var_dump( $res );


//		$img = PostImage::find(20);
//		$js = serialize($img);
////		$js = json_encode($img);
//		var_dump($js);
//		$img = unserialize($js);
////		$img = json_decode($js);
////		var_dump($img);
//
//
////
////		$img = new PostImage;
////		$img = $img->get_model(20);
//
//
////		$img = PostImage::find(20);
//		var_dump($img);

//		$res = $p->get_modles();
//		foreach($res as $re){
//			echo "POST:\n";
//			var_dump($re);
//		}

//		$res = Post::find(37)->postimage->filename;
//		$res = Post::find(37)->postauthor->user_login;

//		$res = $p->get_posts_nocontent(1,5,$redis);

//		$pk_set = $p->get_ts_pk_set(1,5,$redis);
//		$res = $p->get_post_from_pkset($pk_set);


//		$res = $p->get_posts_db(1,5);



//		$total = $p->get_ts_pk_set_size($redis);
//		echo $total;
//		$p = new Post;
//		$post = $p->get_one_post_nocontent(28);
//		$res = $p->get_post_from_pkset($pk_set);
//		echo "BF----------------------------------------<br/>";
//		var_dump($res);
////		usort($res , function($p1, $p2) {
////			$p1_date = strtotime($p1->post_date);
////			$p2_date = strtotime($p2->post_date);
////			if($p1_date == $p2_date){
////				return 0;
////			}else{
////				return $p1_date >$p2_date ?-1:1;
////			}
////		});
//		echo "AF----------------------------------------<br/>";
//		var_dump($res);


//		var_dump($res);




//		$p = new Post;
//		$res = $p->get_post_with_meta_db(2,3);
//		var_dump($res);

//		$res = Post::all()->skip(1)->take(2);
//		var_dump($res);

//		$post_model = new Post;
//		$total = $post_model->get_ts_pk_set(100,5);
//
//		print_r( $total);


//		$view = View::make('test');
//		return $view;
	}


	public function performance(){
		///性能测试用
//		$start = microtime(1);
//		for($i=0;$i<99999;$i++){
//			$res = $img->get_model( 20 ,$redis);
//		}
//		$time = 1000*(microtime(1) - $start);
//		echo "<br/>time:".$time;


//		$start = microtime(1);
//		for($i=0;$i<99999;$i++){
//			$res = $img->get_model( 20);
//		}
//		$time = 1000*(microtime(1) - $start);
//		echo "<br/>time:".$time;

	}

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
