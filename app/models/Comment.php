<?php

class Comment extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';
	protected $primaryKey = 'comment_ID';
	public $timestamps = false;

	protected static $TS_COL = 'comment_date';
	public  static $TS_CONDITION = 1;
	protected static $CONDITION_COL = array(
		0=> "comment_post_ID !=0"
	);


	protected $softDelete = true;

	public function post() {
		return $this->belongs_to('Post');
	}

	public static function get_comments($pagesize) {
		$comments = DB::table('comments')
			->select('comments.comment_ID', 'users.user_login as comment_author_reg',
				'comments.comment_author', 'comment_post_ID', 'comments.comment_content', 'comments.comment_date',
				'posts.post_title')
			->leftJoin('posts', 'posts.ID', '=', 'comments.comment_post_ID')
			->leftJoin('users', 'users.ID', '=', 'comments.comment_author_id')
			//->where('comment_post_ID','in','')
			//->where('comment_post_ID', '!=', 0)
			->paginate($pagesize);

		return $comments;

	}

	public static function get_comments_uid($uid, $pagesize) {
/*
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
			->select('comments.comment_ID', 'users.user_login as comment_author_reg',
				'comments.comment_author', 'comments.comment_content', 'comments.comment_date',
				'posts.post_title')
			->leftJoin('posts', 'posts.ID', '=', 'comments.comment_post_ID')
			->leftJoin('users', 'users.ID', '=', 'comments.comment_author_id')
			//->where('comment_post_ID','in','')
			->whereExists(function ($query) use ($uid) {
				$query->select('ID')
				->from('posts')
				->where('post_author', '=', $uid);
			})->orderBy('comment_date', 'desc')
			->paginate($pagesize);

		return $comments;
	}

	public static function getCommentsByPostID($post_id) {
		$comments = DB::table('comments')
			->select('comment_ID', 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_date', 'comment_content', 'comment_parent')
			->where('comment_post_ID', '=', $post_id)->orderBy('comment_date')->get();
		return $comments;
	}

	//public function
	public static function get_latest_comments_db($count) {
		/*
		select comment_author,post_title from comments
		inner join posts on comments.comment_post_ID= posts.ID
		order by comment_date desc limit 5;
		 */
		$comments = DB::table('comments')->select('comment_ID as comment_id', 'comment_author', 'post_title', 'posts.ID as post_id')
		                                 ->join('posts', 'comments.comment_post_ID', '=', 'posts.ID')
		                                 ->orderBy('comment_date', 'desc')
		                                 ->take($count)->get();
		return $comments;
	}

	/**
	 * 获取最新评论
	 * @param $count
	 * @param null $redis
	 * @return array
	 */
	public function get_latest_comments($count,$redis = null) {
		/*
		select comment_author,post_title from comments
		inner join posts on comments.comment_post_ID= posts.ID
		order by comment_date desc limit 5;
		 */
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		$pks = $this->get_ts_pk_set(1,$count,self::$TS_CONDITION,$redis);
		if(is_array($pks)&&count($pks)>0){
			$comments = $this->get_modles_from_pkset($pks,$redis);
		}else{

		}
		if(is_array($comments) && count($comments)>0){
			$this->add_meta($comments,$redis);
		}
		return $comments;
	}

	public function add_meta(&$comments,$redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		if(is_array($comments) && count($comments)>0){
			foreach ($comments as $comm){
				$post = new Post;
				$post = $post->get_model( $comm->comment_post_ID,$redis );
				if(!is_null($post)){
					$comm->post_title = $post->post_title;
					$comm->post_id = $post->post_id;
				}else{
					$comm->post_title = null;
					$comm->post_id = null;
				}
			}
		}
	}


	public static function getNewCommentCountByPostAuthorID($post_author_id) {
		/*
		select count(*) commcnt from comments
		left join posts on comments.comment_post_ID= posts.ID
		where posts.post_author = 27 and comment_read=0;
		 */
		$comm_count = DB::table('comments')
			->leftJoin('posts', 'comments.comment_post_ID', '=', 'posts.ID')
			->where('posts.post_author', '=', $post_author_id)->where('comment_read', '=', Constant::$COMM_UNREAD)
			->count();
		return $comm_count;
	}

	public static function create_in_post() {
		date_default_timezone_set("Europe/London");
		$comment_date_gmt = date('Y-m-d H:i:s', time());
		date_default_timezone_set("Asia/Shanghai");
		$comment_date = date('Y-m-d H:i:s', time());

		$comment = new Comment;

		$post_id = Input::get('post_id');
		$comment->comment_post_ID = $post_id;
		$comment->comment_parent = Input::get('comment_parent');
		$comment->comment_author = Input::get('comment_author');
		$comment->comment_author_email = Input::get('comment_author_email');
		$comment->comment_content = Input::get('comment_content');
		$comment->comment_date = $comment_date; //date('Y-m-d H:i:s',time());
		$comment->comment_date_gmt = $comment_date_gmt; //date('Y-m-d H:i:s',time());
		$comment->save();
	}

	public static function delete_recursive($cid) {
		$comm = Comment::find($cid);
		if ($comm) {
			$childs = Comment::where('comment_parent', '=', $cid);
			if (count($childs) > 0) {
				foreach ($childs as $child) {
					self::delete_recursive($child->comment_ID);
				}
			} else {
				$comm->delete();
			}
		} else {
			throw new Exception("评论{$cid}不存在");
		}
		return;
	}

// 	public static function delete_by_cid($cid){

// 	}

}
