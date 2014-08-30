<?php

class PostController extends BaseController {
	
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
		$posts = Post::postAddTerm($posts);
		
		//get posts's term
		//$terms = array();
// 		foreach($posts as $post):
// 			$terms = Term::getTermsByPostID($post->post_id);
// 			$cat = Term::getCategory($terms);
// 			$tag = Term::getTag($terms);
// 			$post->category = !empty($cat)?$cat:null;
// 			$post->post_tag = !empty($tag)?$tag:null;
// 			$post->post_content = Post::get_adjust_post($post->post_content,200);
// 		endforeach;
		
// 		$queries = DB::getQueryLog();
// 		$last_query = end($queries);
// Log::info('post stat:'.$last_query['query']);
		//set user info
		$sidebar = PostController::get_sidebar();
		$username = User::getNameFromSession( Session::get('user') );

		$view = View::make('index',
			array('title'=>$term_id==0?'Async Blog':$term4title[0]->name.'|Async Blog','username'=>$username,
				'term4title'=>$term_id==0?null:$term4title,'date4title'=>null,
				'posts'=>$posts,//,'terms'=>$terms,
				'sidebar'=>$sidebar));
				//'term_stats'=>$term_stats,'post_stats'=>$post_stats,'latest_posts'=>$latest_posts));
		return $view;
	}
	
	
	
	public function post_by_date($date){
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
		
		$posts = Post::postAddTerm($posts);
		$sidebar = PostController::get_sidebar();
		$username = User::getNameFromSession( Session::get('user') );
		
		$view = View::make('index',
				array('title'=>$date.'|Async Blog','username'=>$username,
						'date4title'=>$date4title,
						'term4title'=>null,
						'posts'=>$posts,
						'sidebar'=>$sidebar));
		//'term_stats'=>$term_stats,'post_stats'=>$post_stats,'latest_posts'=>$latest_posts));
		return $view;
	}
	
	/**
	 * single，get post by ID,get post's comments by comment_post
	 * get pre post_id,get next post_id[order by date]
	 * @param unknown $post_id
	 * @return void|unknown
	 */
	public function single($post_id){
		Log::info("Single,post_id:".$post_id);
		$post = Post::getPostById($post_id);
		Log::info("post title ".$post->post_title);
		$comments = Comment::getCommentsByPostID($post_id);
		
		$sidebar = PostController::get_sidebar();
//init parameter,used for tree function
// 		foreach($comments as $comm){
// 			$comm->parent = $comm->comment_parent;
// 			$comm->id= $comm->comment_ID;
// 		}
//Tree::init_child($comments);
		if(empty($post)){
			App::abort(404);
			return ;
		}
		
		$view = View::make('posts/single_comm_r',array(
				'post'=>$post,'comments'=>$comments,
				'title'=>$post->post_title,
				'sidebar'=>$sidebar));
		
		return $view;
	}
	
	public function create(){
		Post::create_();
	}
	
	public function delete_with_term_comment(){
		Post::delete_with_term_comment();
	}
	
	
	
}
