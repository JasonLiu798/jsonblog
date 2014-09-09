<?php

class PostController extends BaseController {
	
	/**
	 * get sidebar infos
	 * @return stdClass
	 */
	public static function get_sidebar(){
		$res = new stdClass;
		//get terms
		$term_stats = Term::getTermsAndStat();
		$res->term_stats = $term_stats;
		//get post archive
		$post_stats = Post::getPostsStat();
		$res->post_stats = $post_stats;
		//get latest posts
		$latest_posts = Post::getNewstPost(5);
		$res->latest_posts = $latest_posts;
		//get latest comments
		$latest_comments = Comment::getLatestComments(5);
		$res->latest_comments = $latest_comments;
		return $res;
	}
	
	/**
	 * index ,get all posts
	 * $term_id:0,all;1,unclassify;>1,classified
	 * @return unknown
	 */
	public function index($term_id=0){
Log::info('IndexAction');
		//get posts
		if($term_id>0){
			$posts = Post::getPostsByTerm($term_id,Constant::$PAGESIZE);
$queries = DB::getQueryLog();
$last_query = end($queries);
Log::info('post date:'.$last_query['query']);			
			$term4title = Term::getTermNameTaxonomy($term_id);
			
			//var_dump( $term4title );
			Log::info($term4title[0]->name );//gettype($term4title));
			if( is_null($term4title[0]->name ) ){
				$err_msg = '分类/标签不存在';
				App::abort(404);
			}
		}else{
			$posts = Post::getPosts(Constant::$PAGESIZE);
		}
		$posts = Post::postAddMeta($posts);
		$sidebar = PostController::get_sidebar();
		$username = User::getNameFromSession( Session::get('user') );
		$view = View::make('index',
			array('title'=>$term_id==0?'Async Blog':$term4title[0]->name.'|Async Blog','username'=>$username,
				'term4title'=>$term_id==0?null:$term4title,'date4title'=>null,'user4title'=>null,
				'posts'=>$posts,
				'sidebar'=>$sidebar));
		return $view;
	}
	
	
	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function posts_by_date($date){
		if(!preg_match(Constant::$REG_YEAR_MONTH,$date,$m)){
			$err_msg = '参数格式错误';
			App::abort(404);
		}
		$date_arr = explode("-",$date);
		$date4title = $date_arr[0].'年'.$date_arr[1].'月';
		$posts = Post::getPostByDate($date,Constant::$PAGESIZE);
		
$queries = DB::getQueryLog();
$last_query = end($queries);
Log::info('post date:'.$last_query['query']);
		
		$posts = Post::postAddMeta($posts);
		$sidebar = PostController::get_sidebar();
		$username = User::getNameFromSession( Session::get('user') );
		
		$view = View::make('index',
				array('title'=>$date.'|Async Blog','username'=>$username,
						'date4title'=>$date4title,
						'term4title'=>null,
						'user4title'=>null,
						'posts'=>$posts,
						'sidebar'=>$sidebar));
		return $view;
	}
	
	/**
	 * get post by user_id
	 * @param unknown $user_id
	 * @return unknown
	 */
	public function posts_by_author($user_id){
		if(!preg_match('/[0-9]+/',$user_id)){
			$err_msg = '参数格式错误';
			App::abort(404);
		}
		$posts = Post::getPostByUser($user_id,Constant::$PAGESIZE);
		$posts = Post::postAddMeta($posts);
		$sidebar = PostController::get_sidebar();
		$user4title = User::find($user_id);
		$username = User::getNameFromSession( Session::get('user') );
		$view = View::make('index',
				array('title'=>$user4title->user_login.'|Async Blog','username'=>$username,
						'date4title'=>null,
						'term4title'=>null,
						'user4title'=>$user4title,
						'posts'=>$posts,
						'sidebar'=>$sidebar));
		return $view;
	}
	
	
	/**
	 * single，get post by ID,get post's comments by comment_post
	 * get pre post_id,get next post_id[order by date]
	 * INDEX: $post_id,$term_id=0 
	 * POSTS BY TERM :$post_id,$term_id
	 * POSTS BY DATE :$post_id,$term_id=0,$date
	 * @param unknown $post_id,term_id=0,date=1990-01
	 * @return void|unknown
	 */
	public function single($post_id){//,$term_id,$post_date='1900-01'){
//Log::info("Single,post_id:".$post_id);
		$post = Post::getPostById($post_id);
		if(empty($post)){
			App::abort(404);
			return ;
		}
		
		$comments = Comment::getCommentsByPostID($post_id);
		$sidebar = PostController::get_sidebar();
		$pre_next_post = Post::getPreNextPost( $post_id );
		$view = View::make('posts/single_comm_r',array(
				'post'=>$post,'comments'=>$comments,
				'title'=>$post->post_title.'|'.Lang::get('posts.TITLE'),
				'pre_next_post'=>$pre_next_post,
				'sidebar'=>$sidebar));
		return $view;
	}
	
	public function create($param='page'){
		$sess_user_json = Session::get('user');
		//login can create post
		if( is_null($sess_user_json)){
			// to error page 
			Redirect::action('PostController@index');
		}
		$user = json_decode($sess_user_json);
		$sidebar = PostController::get_sidebar();
		if($param === 'page'){
			
			$terms = Term::getTermsByUserID($user->uid);
			$category = Term::getCategory($terms);
			$post_tag = Term::getTag($terms);
			$view = View::make('posts/create_post',array(
					'title'=>Lang::get('posts.TITLE'),'username'=>$user->username,
					'category'=>$category,
					'post_tag'=>$post_tag,
					'sidebar'=>$sidebar));
			return $view;
		}else if($param==='draft_page'){
			
		}else if($param==='do'){
			Post::create_();
			
		}else{
			App::abort(404);
		}
	}
	
	public function delete_with_term_comment(){
		Post::delete_with_term_comment();
	}
	
	
	
}
