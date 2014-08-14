<?php

class PostController extends BaseController {
	
	
	/**
	 * index ,get all posts
	 * @return unknown
	 */
	public function index(){
		$posts = DB::table('posts')
			->join('users','users.ID','=','posts.post_author')
			->select('posts.ID as ID', 'post_title','post_content','post_date','users.user_login as post_author')
			->paginate(3);
		$terms = array();
		
		foreach($posts as $post):
			$terms = Term::getTermsByPostID($post->ID);
			$cat = Term::getCategory($terms);
			$tag = Term::getTag($terms);
			$post->category = !empty($cat)?$cat:null;
			$post->post_tag = !empty($tag)?$tag:null;
			$post->post_content = Post::get_adjust_post($post->post_content,200);
		endforeach;
		
		$sess_user_json = Session::get('user');
		if(! is_null($sess_user_json)){
			$sess_user = json_decode($sess_user_json);
			$username = $sess_user->username;
		}else{
			$username = null;
		}
		
		$view = View::make('posts/index',array('posts'=>$posts,'terms'=>$terms,'username'=>$username,'title'=>'Async Blog'));
		return $view;
	}
	
	/**
	 * single，get post by ID,get post's comments by comment_post
	 * @param unknown $post_id
	 * @return void|unknown
	 */
	public function single($post_id){
		Log::info("Single,post_id:".$post_id);
		$post = Post::getPostById($post_id);
		Log::info("post title ".$post->post_title);
		$comments = Comment::getCommentsByPostID($post_id);
		foreach($comments as $comm){
			$comm->parent = $comm->comment_parent;
			$comm->id= $comm->comment_ID;
		}
		Tree::init_child($comments);
		if(empty($post)){
			App::abort(404);
			return ;
		}
		
		$view = View::make('posts/single',array('post'=>$post,'comments'=>$comments,'title'=>$post->post_title));
		
		return $view;
		
	}
	
	public function create(){
		Post::create_();
	}
	
	public function delete_with_term_comment(){
		Post::delete_with_term_comment();
	}
	
	
	
}
