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
		$sess_user = Session::get('user');
		$username = User::get_name_from_session($sess_user);
		$user_id = User::get_userid_from_session($sess_user);

		$comments = Comment::get_comments(Constant::$ADMIN_PAGESIZE);

		$view = View::make('comments/comment_admin',
			array('title' => '评论管理',
				'nav' => Constant::$NAV_ADMIN,
				'username' => $username,
				'comments' => $comments,
				'menu' => 'comment',
			));
		return $view;
	}

	public function delete($cid) {
		$err = '';
		try {
			Comment::delete_recursive($cid);
		} catch (Exception $e) {
			$err = $e->getMessage();
		}
		if (strlen($err) > 0) {
			return Redirect::action('ErrorController@show', array($err));
		} else {
			return Redirect::route('comment_admin');
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

	public function create() {
		$post_id = (int) Input::get('post_id');

		Comment::create_in_post();
		$post_author_id = Input::get('post_author_id');
		$comm_cnt = Comment::getNewCommentCountByPostAuthorID($post_author_id);
		$url = 'http://localhost:3000/newcomm?uid=' . $post_author_id . '&commcnt=' . $comm_cnt;
		Log::info('NEW COMM URL:' . $url);

		$res = file_get_contents($url);
		if ($post_id == Constant::$MESSAGE_POST_ID) {
			$resp = Redirect::route('messages');
		} else {
			$resp = Redirect::action('PostController@single', array($post_id));
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

}
