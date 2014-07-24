<?php

class Comment extends Eloquent  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';
	protected $primaryKey = 'comment_ID';
	public $timestamps = false;
	
	
	public static function getCommentsByPostID($post_id){
		$comments = DB::table('comments')
		->select('comment_ID','comment_post_ID','comment_author','comment_author_email','comment_date','comment_content')
		->where('comment_post_ID','=',$post_id)->get();
		return $comments;
	} 
	
	/*
	public static function create(){
		$comment_author = Input::get('comment_author');
		$comment_email = Input::get('comment_email');
		$comment_content = Input::get('comment_content');
		
		
	}*/
	
}
