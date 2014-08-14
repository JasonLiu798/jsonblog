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
	
	protected $softDelete = true;
	
	public static function getCommentsByPostID($post_id){
		$comments = DB::table('comments')
		->select('comment_ID','comment_post_ID','comment_author','comment_author_email','comment_date','comment_content','comment_parent')
		->where('comment_post_ID','=',$post_id)->orderBy('comment_date')->get();
		return $comments;
	} 
	
	//public function 
	
	
	public static function create_in_post(){
		date_default_timezone_set("Europe/London");
		$comment_date_gmt = date('Y-m-d H:i:s',time());
		date_default_timezone_set("Asia/Shanghai");
		$comment_date = date('Y-m-d H:i:s',time());
		
		$comment = new Comment;
		
		$post_id = Input::get('post_id');
		$comment->comment_post_ID =$post_id;
		$comment->comment_parent = Input::get('comment_parent');
		$comment->comment_author = Input::get('comment_author');
		$comment->comment_author_email = Input::get('comment_author_email');
		$comment->comment_content = Input::get('comment_content');
		$comment->comment_date = $comment_date;//date('Y-m-d H:i:s',time());
		$comment->comment_date_gmt = $comment_date_gmt;//date('Y-m-d H:i:s',time());
		$comment->save();
	}
	
	
// 	public static function delete_by_cid($cid){
			
// 	}
	
}
