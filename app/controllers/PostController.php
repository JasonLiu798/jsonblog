<?php

class PostController extends BaseController {
	/**
	 * index ,get all posts
	 * @return unknown
	 */
	public function index(){
		$posts = DB::table('posts')->select('ID', 'post_title','post_content','post_date')->get();
		$view = View::make('posts/index',array('posts'=>$posts))
			->nest('header', 'templates/header',array('title'=>'主页'))
			->nest('footer','templates/footer')
			->nest('logo','templates/logo')
			->nest('sidebar','templates/sidebar');
		return $view;
	}
	
	/**
	 * single，get post by ID,get post's comments by comment_post
	 * @param unknown $post_id
	 * @return void|unknown
	 */
	public function single($post_id){
		Log::info("PostId ".$post_id);
		$post = DB::table('posts')->select('ID', 'post_title','post_content','post_date')
			->where('ID', '=', $post_id)->get();
		//Log::info('This is some useful information.');
		$comments = DB::table('comments')->select('comment_post_ID','comment_author','comment_author_email','comment_date','comment_content')
			->where('comment_post_ID','=',$post_id)->get();
		if(empty($post)){
			App::abort(404);
			return ;
		}
		Log::info("post title ".$post[0]->post_title);
		$view = View::make('posts/single',array('post'=>$post,'comments'=>$comments))
			->nest('header', 'templates/header',array('title'=>$post[0]->post_title))
			->nest('footer','templates/footer');
		return $view;
	}
	
	
	
}
