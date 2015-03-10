<?php

class Comment extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';
	protected $primaryKey = 'comment_id';
	public $timestamps = false;

	protected static $TS_COL = 'comment_date';
	public  static $TS_CONDITION = 1;
	protected static $CONDITION_COL = array(
		0=> "comment_post_id !=0"
	);



	protected $softDelete = true;

	/**
	 * 多对一
	 * @return mixed
	 */
	public function post() {
		return $this->belongs_to('Post');
	}


	/**
	 * @param $pagesize
	 * @return null
	 */
	public function get_comments($pagesize)
	{
		try {
			$res = Comment::paginate($pagesize);
			if (is_array($res) && count($res) > 0) {
				$this->add_meta($res);
			}
		} catch (Exception $e) {
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}|获取评论失败");
			$res = null;
		}
		return $res;
	}
//		$comments = DB::table('comments')
//			->select('comments.comment_ID', 'users.user_login as comment_author_reg',
//				'comments.comment_author', 'comment_post_ID', 'comments.comment_content', 'comments.comment_date',
//				'posts.post_title')
//			->leftJoin('posts', 'posts.ID', '=', 'comments.comment_post_ID')
//			->leftJoin('users', 'users.ID', '=', 'comments.comment_author_id')
//			//->where('comment_post_ID','in','')
//			//->where('comment_post_ID', '!=', 0)
//			->paginate($pagesize);


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

	/**
	 * @param $post_id
	 * @param $pagesize
	 * @return mixed
	 */
	public function get_post_comments($post_id,$pagesize) {
		try{
			$res = Comment::where('comment_post_id', $post_id)->where('comment_replay',0)->orderBy
			('comment_date','desc')->paginate($pagesize);
			if( !is_null($res) && count($res)>0 ){
				foreach($res as $comm){
					$comm->child_comments = $this->get_child_comments( $comm->comment_id );
				}
			}
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}|获取子评论失败");
			$res = null;
		}
		return $res;
//		$comments = DB::table('comments')
//			->select('comment_ID', 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_date', 'comment_content', 'comment_parent')
//			->where('comment_post_ID', '=', $post_id)->orderBy('comment_date')->get();

	}

	/**
	 * v1,recursive get child comments
	 * v2,none-recursive,get childs
	 * @param $comment
	 * @return null
	 */
	public function get_child_comments($comment_id){
		try{
			$res = self::where('comment_replay', $comment_id )->orderBy('comment_date','desc')->get();
//			var_dump($res);
//			echo "child:$res->comment_id \n";
//			print_r($res);
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}|获取子评论失败");
			$res = null;
		}
		return $res;

//		if( is_array($child_comments) && count($child_comments)>0){
//			$childs = array_merge($childs,$child_comments );
//			foreach($child_comments as $child){
//				$this->get_child_comments($child,$childs);
////				$child_comments = Comment::where('comment_parent',$child->comment_ID)->get();
////				if( !is_null($child_comments ) ){
////					$this->get_child_comments( $child_comments, $childs );
////					array_merge($childs,$child_comments);
////				}
//			}
//		}
//		return;
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
		}
		if(is_array($comments) && count($comments)>0){
			$this->add_meta($comments,$redis);
			$this->add_page($comments);
		}else{
			$comments = null;
		}
		return $comments;
	}

	public function add_page(&$comms){
		if(is_array($comms) && count($comms)>0) {
			foreach ($comms as $comm) {
				$comm->page = $this->in_which_page($comm);
			}
		}
	}

	public function in_which_page($comment){
//		echo 'cid:'.$comment->comment_id;
//		echo 'psot:'.$comment->comment_post_id;
		$totalcnt = Comment::where('comment_post_id', $comment->comment_post_id);
		$comments = Comment::where('comment_post_id', $comment->comment_post_id)
			->where('comment_replay',0)
			->orderBy('comment_date','desc')->get();
		$i = 1;
		$idx = 0;
		$got = false;
		foreach($comments as $comm){
			if($got){
				break;
			}
			$childs = $this->get_child_comments( $comm->comment_id );
			if(!is_null($childs) && count($childs)>0){
				foreach($childs as $child){
					if($child->comment_id == $comment->comment_id){
						$idx = $i;
						$got = true;
					}
				}
			}
			if($comm->comment_id == $comment->comment_id){
				$idx = $i;
				$got = true;
			}
			$i++;
		}
		$page = ceil($idx/Constant::$PAGESIZE);
		//		echo "page: $page idx: $idx";
		if($page<=0){
			$page = 1;
		}
		return $page;
	}




	public function add_meta(&$comments,$redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		if(is_array($comments) && count($comments)>0){
			foreach ($comments as $comm){
				$post = new Post;
				$post = $post->get_model( $comm->comment_post_id,$redis );
//				if( !is_null($comm->comment_author_id) || $comm->comment_author_id!=0
//					||$comm->comment_author_id!=='0'){
//
//				}
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

	/**
	 *
	 * @param $post_author
	 * @return null
	 */
	public static function get_user_unread_comment_cnt($post_author){
		try{
			$res = DB::table('comments')
				->leftJoin('posts', 'comments.comment_post_ID', '=', 'posts.ID')
				->where('posts.post_author', $post_author)
				->where('comment_read', '=', Constant::$COMM_UNREAD)
				->count();
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}|获取未读取comment数量失败");
			$res = null;
		}
		return $res;
	}



	public function create_message($post_id,$author,$email,$content,$redis=null){

	}

	/**
	 * @param $post_id
	 * @param $author
	 * @param $email
	 * @param $content
	 * @param null $redis
	 */
	public function create_comment($post_id,$post_author_id,
								   $comment_replay,$child_comment_replay,
								   $content,
								   $author_id,$author,$email,$redis=null){
		//create_comment($post_id,$post_author_id,$comment_replay,$content,$author_id,$author,$email,$redis=null);
		$v = Validator::make(
			array(
				'所属博文' => $post_id,
				'博文作者'=> $post_author_id,

				'姓名' => $author,
				'Email' => $email,
				'评论内容'=>$content,

			),
			array(
				'所属博文' => 'required|exists:posts,post_id',
				'博文作者'=> 'required|exists:users,user_id',

				'姓名' => 'between:6,16',
				'Email'=> 'between:6,512|unique:users,user_email',
				'评论内容' => 'between:0,1000',

			)
		);
		$msg_parent = null;
		$res = new stdClass();
		$res->status = true;
		$res->data = array();

		if( (int)$comment_replay != 0 ){
			$v_parent = Validator::make(
				array(
					'上级评论'=> $comment_replay
				),
				array(
					'上级评论' => 'required|exists:comments,comment_ID'
				)
			);
			if($v_parent->fails()){
				$res->data = $v_parent->messages()->all();
				$res->status = false;
			}
		}
		if( (int)$child_comment_replay != 0){
			$v_direct_parent = Validator::make(
				array(
					'直接上级评论'=> $child_comment_replay
				),
				array(
					'直接上级评论' => 'required|exists:comments,comment_ID'
				)
			);
			if($v_direct_parent->fails()){
				$res->data = $v_direct_parent->messages()->all();
				$res->status = false;
			}
		}
		$author_exist = false;
		//登录用户验证是否存在
		if( (int)$author_id !=0 ){
			$v_author_id = Validator::make(
				array(
					'评论人'=> $author_id
				),
				array(
					'评论人' => 'required|exists:users,user_id'
				)
			);
			if($v_author_id->fails()){
				$res->data = array_merge( $res->data, $v_author_id->messages()->all());
				$res->status = false;
			}else{
				$author_exist = true;
			}
			//是否本人评论
//			$user_id = User::get_userid_from_session();
//			if($user_id != $v_author_id){
//				array_push($res->data,"未登录或非本人评论");
//				$res->status = false;
//			}
		}


		if( $v->fails() ) {
			$res->data = array_merge( $res->data, $v->messages()->all());
			$res->status = false;
		}
		if($res->status){
			//验证成功
			if(is_null($redis)){
				$redis = LRedis::connection();
			}
			date_default_timezone_set("Europe/London");
			$comment_date_gmt = date('Y-m-d H:i:s', time());
			date_default_timezone_set("Asia/Shanghai");
			$comment_date = date('Y-m-d H:i:s', time());
			$pk = $this->get_new_pk($redis);
			if($pk){
				$this->comment_id =$pk;
				$this->comment_post_id = $post_id;
				$this->comment_replay = $comment_replay;
				if( $author_exist ){
					$user = User::find($author_id);
					$this->comment_author_id = $author_id;
					$this->comment_author = $user->user_login;
					$this->comment_author_email = $user->user_email;
				}else{
					$this->comment_author = is_null($author)||strlen($author)==0?'匿名用户':$author;
					$this->comment_author_email = $email;
				}
				$this->comment_content = $content;
				$this->comment_date = $comment_date; //date('Y-m-d H:i:s',time());
				$this->comment_date_gmt = $comment_date_gmt; //date('Y-m-d H:i:s',time());
				try{
					if($this->save()){
						$res->status = true;
						$res->comment_id = $pk;
						$post_model = new Post;
						//评论计数增加
						$post_model->incr_relate_count('comment',$post_id,$redis);

						//通知作者新评论
						$response = Event::fire('comment.create', array( $post_author_id ));
						if((int)$child_comment_replay!=0){
							//
						}
						//通知被回复的用户
						$reply_comm = Comment::find($comment_replay);
						if($reply_comm->comment_author_id!=0){
							$response = Event::fire('comment.create', array( $post_author_id ));
						}
					}else{
						$res->status = false;
						array_push($res->data,"创建评论失败");
					}
				}catch(Exception $e){
					$error_msg = $e->getMessage();
					$method = __METHOD__;
					Log::error("{$method}|MSG:{$error_msg}|创建comment失败");
					$res->status = false;
					array_push($res->data,"创建评论失败");
				}
			}else{
				$res->status = false;
				array_push($res->data,"主键生成失败");
			}
		}
		return $res;
	}


	/*
	public static function create_in_post() {




		$post_id = Input::get('post_id');

	}
	*/

	public static function delete_recursive($cid) {
		$comm = Comment::find($cid);
		if ($comm) {
			$childs = Comment::where('comment_replay', '=', $cid);
			if (count($childs) > 0) {
				foreach ($childs as $child) {
					self::delete_recursive($child->comment_id);
				}
			}
			Comment::destroy($cid);//$comm->delete();
		} else {
			throw new Exception("评论{$cid}不存在");
		}
	}



// 	public static function delete_by_cid($cid){

// 	}

}
