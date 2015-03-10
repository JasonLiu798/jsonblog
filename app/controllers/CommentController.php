<?php

class CommentController extends BaseController {

	public function messages() {
		$comments = Comment::getCommentsByPostID(Constant::$MESSAGE_POST_ID);
		$sidebar = PostController::get_sidebar();
		$username = User::get_name_from_session();
		$resp = View::make('message/messages',
			array(
				'comments' => $comments,
				'username' => $username,
				'title' => '留言',
				'sidebar' => $sidebar));
		return $resp;
	}

	public function admin() {

		$username = User::get_name_from_session();
		$comm_model = new Comment;
		$comments = $comm_model->get_comments(Constant::$ADMIN_PAGESIZE);

		$view = View::make('comments/comment_admin',
			array('title' => '评论管理',
				'nav' => Constant::$NAV_ADMIN,
				'username' => $username,
				'comments' => $comments,
				'menu' => 'comment',
			));
		return $view;
	}


	/**
	 * 删除评论
	 * @param $cid
	 * @return mixed
	 */
	public function delete($cid) {
		$err = '';
		$page = Input::get('page');
		try {
			Log::info('Comment delete page:'.$page);
			Comment::delete_recursive($cid);
			if($page!=null){
				$resp =  Redirect::route('comment_admin',array('page'=>$page));
			}else{
				$resp = Redirect::route('comment_admin');
			}
		} catch (Exception $e) {
			$err = $e->getMessage();
		}
		if (strlen($err) > 0) {
			return Redirect::action('ErrorController@show', array($err));
		} else {
			return $resp;
		}
	}

	public function batch_delete() {
		$err = '';
		$ids = Input::get('delete_ids');
		Log::info('Delete comments:' . $ids);
		if (is_null($ids) || strlen($ids) <= 0) {
			$err = '参数错误';
		} else {
			$id_arr = explode(',', $ids);
			if (count($id_arr) > 0) {
				foreach ($id_arr as $cid) {
					$comm = Comment::find($cid);
					$comm->delete();
				}
			} else {
				$err = '参数错误';
			}
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('comment_admin'); //,array($post_id));
		}
	}

	public function store() {
		Comment::create(array(
			'comment_author' => Input::get('comment_author'), //1,//
			'comment_content' => Input::get('comment_content'),
		));
		return Response::json(array('success' => true));
	}

	//public function


	/**
	 * 增加评论/留言
	 * @return mixed
	 */
	public function create() {
//		Input::all();
//		var_dump();

		$post_id = (int) Input::get('post_id');
		$post_author_id = Input::get('post_author_id');

		$comment_replay = Input::get('comment_replay');
		$child_comment_replay = Input::get('child_comment_replay');
		//登录用户
		$author_id = Input::get('comment_author_id');
		//未登录用户
		$author = Input::get('comment_author');
		$email = Input::get('comment_author_email');
		//评论内容
		$content = Input::get('comment_content');
		Log::info("pid: $post_id ,a $post_author_id ,r $comment_replay ,child_r
		$child_comment_replay ,$author_id ,$author");
		$comm_model = new Comment;
		$res = $comm_model->create_comment($post_id,$post_author_id,
			$comment_replay,$child_comment_replay,
			$content,
			$author_id,$author,$email,$redis=null);

		if( !$res->status ){
			//验证或创建失败
			//$resp = //Redirect::action('PostController@single', array($post_id));
			$resp = Response::json(array('status' => false , 'msg' => json_encode($res->data)) );
		}else{
			if( $comment_replay ==='0'){
				$comment_id = $res->comment_id;
			}else{
				$comment_id = $comment_replay;
			}
//			$resp =  Redirect::to("post/single/{$post_id}#comment-{$comment_id}");
			$resp = Response::json(array(
				'status' => true,'post_id'=>$post_id,'comment_id'=>$comment_id
			));
		}
		return $resp;

	}

	/**
	 * T:/comment/delete?cid=48
	 */
// 	public function delete(){
	// 		$cid = Input::get('cid');
	// 		$comm = Comment::find($cid);
	// 		$comm->delete();
	// 	}

	public function get_unread_comment_cnt($uid) {
		$sess_user_json = Session::get('user');
		$response = array();
		/*
		//no login,return null
		if( is_null($sess_user_json) || is_null($uid) ){
		$response['msg']  = '';//no login
		$response['timestamp'] = time();
		return json_encode($response);
		}*/

		$cur_cnt = $last_cnt = DB::table('comments')->join('posts', 'posts.ID', '=', 'comments.comment_post_ID')
		                                            ->where('posts.post_author', $uid)->where('comment_read', 0)->count();

		while ($cur_cnt <= $last_cnt) {
			usleep(100 * 1000);
			$cur_cnt = DB::table('comments')->join('posts', 'posts.ID', '=', 'comments.comment_post_ID')
			                                ->where('posts.post_author', $uid)->where('comment_read', 0)->count();
		}

		Log::info('User:' . $uid . " got " . $cur_cnt . " comments");

		$response['msg'] = $cur_cnt;
		$response['timestamp'] = time();
		return json_encode($response);
	}


	// comment/get?page=1&post_id=72
	public function postcomments(){
		$post_id = Input::get('post_id');
		$post_exist = Post::chk_pid_exist($post_id);
		if($post_exist){
			$comm_model = new Comment;
			$comments = $comm_model->get_post_comments($post_id,Constant::$PAGESIZE);
//			var_dump($comments);
			$resp = Response::json(array('status' => true , 'msg' => json_encode
			($comments->getItems()) ));
		}else{
			$comments = null;
			$resp = Response::json(array('status' => false , 'msg' => '所属文章不存在' ));
		}
		return $resp;

	}
}
