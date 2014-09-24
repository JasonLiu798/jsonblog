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
	
	
	public static function getCommentsByUserID($uid,$pagesize){
/**
select comments.comment_ID,
	users.user_login comment_author_reg,comments.comment_author,
	comments.comment_content,comments.comment_date,
	posts.post_title
from comments 
left join posts on posts.ID = comments.comment_post_ID 
left join users on users.ID = comments.comment_author_id 
where  comment_post_ID in (select ID from posts where post_author = 1)
order by comment_date desc;
*/
		$comments = DB::table('comments')
			->select('comments.comment_ID','users.user_login as comment_author_reg',
			'comments.comment_author','comments.comment_content','comments.comment_date',
			'posts.post_title')
			->leftJoin('posts','posts.ID','=','comments.comment_post_ID')
			->leftJoin('users','users.ID','=','comments.comment_author_id')
			//->where('comment_post_ID','in','')
			->whereExists(function($query)use($uid)
			{
				$query->select('ID')
				->from('posts')
				->where('post_author','=',$uid);
			})->orderBy('comment_date','desc')
			->paginate($pagesize);
			
		
		return $comments;
	}
	
	
	
	public static function getCommentsByPostID($post_id){
		$comments = DB::table('comments')
		->select('comment_ID','comment_post_ID','comment_author','comment_author_email','comment_date','comment_content','comment_parent')
		->where('comment_post_ID','=',$post_id)->orderBy('comment_date')->get();
		return $comments;
	} 
	
	//public function 
	public static function getLatestComments($count){
		/*
		  select comment_author,post_title from comments 
			inner join posts on comments.comment_post_ID= posts.ID
 			order by comment_date desc limit 5;
		 */
		$comments = DB::table('comments')->select('comment_ID as comment_id','comment_author','post_title','posts.ID as post_id')
			->join('posts','comments.comment_post_ID','=','posts.ID')
			->orderBy('comment_date','desc')
			->take($count)->get();
		return $comments;
	}
	
	public static function getNewCommentCountByPostAuthorID($post_author_id){
		/*
		 select count(*) commcnt from comments
		 	left join posts on comments.comment_post_ID= posts.ID
		 where posts.post_author = 27 and comment_read=0;
		*/
		$comm_count = DB::table('comments')
		->leftJoin('posts','comments.comment_post_ID','=','posts.ID')
		->where('posts.post_author','=',$post_author_id)->where('comment_read','=',Constant::$COMM_UNREAD )
		->count();
		return $comm_count;
	}
	
	
	
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
