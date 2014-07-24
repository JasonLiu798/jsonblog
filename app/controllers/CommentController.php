<?php

class CommentController extends BaseController {
	
	public function create(){
		
		date_default_timezone_set("Europe/London");
		$comment_date_gmt = date('Y-m-d H:i:s',time());
		date_default_timezone_set("Asia/Shanghai");
		$comment_date = date('Y-m-d H:i:s',time());
		
		$comment = new Comment;
		
		$post_id = Input::get('post_id');
		$comment->comment_post_ID =$post_id;
		$comment->comment_author = Input::get('comment_author');
		$comment->comment_author_email = Input::get('comment_email');
		$comment->comment_content = Input::get('comment_content');
		$comment->comment_date = $comment_date;//date('Y-m-d H:i:s',time());
		$comment->comment_date_gmt = $comment_date_gmt;//date('Y-m-d H:i:s',time());
		$comment->save();
		
		return Redirect::action('PostController@single', array($post_id));
	}
	
	
	
	
	
	
}
