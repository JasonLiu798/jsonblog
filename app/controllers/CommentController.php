<?php

class CommentController extends BaseController {
	

	public function admin(){
		$sess_user = Session::get('user');
		$username = User::get_name_from_session( $sess_user);
		$user_id = User::get_userid_from_session( $sess_user );
		
		$comments = Comment::get_comments_uid($user_id, Constant::$ADMIN_PAGESIZE);

		$view = View::make('comments/comment_admin',
				array('title'=>'评论管理','username'=>$username, 'comments'=>$comments,'menu'=>'comment'
						));
		return $view;
	}
	
	public function delete($cid){
		$comm = Comment::find($cid);
		$comm->delete();
		//Comment::destroy($cid)
		return Redirect::action('CommentController@admin');
		// Response::json(array('success' => true));
	}
	
	public function store(){
		Comment::create(array(
			'comment_author' => Input::get('comment_author'),//1,//
			'comment_content' => Input::get('comment_content')
		));
		return Response::json(array('success' => true));
	}
	
	
	
	//public function 
	
	
	public function create(){
		$post_id = Input::get('post_id');
		Comment::create_in_post();
		$post_author_id = Input::get('post_author_id');
		$comm_cnt = Comment::getNewCommentCountByPostAuthorID($post_author_id);
		$url='http://localhost:3000/newcomm?uid='.$post_author_id.'&commcnt='.$comm_cnt;
Log::info('NEW COMM URL:'.$url);
		$res = file_get_contents($url);
		return Redirect::action('PostController@single', array($post_id));
	}
	
	/**
	 * T:/comment/delete?cid=48
	 */
// 	public function delete(){
// 		$cid = Input::get('cid');
// 		$comm = Comment::find($cid);
// 		$comm->delete();
// 	}
	
	
	public function get_unread_comment_cnt($uid){
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
			->where('posts.post_author',$uid)->where('comment_read',0)->count();
		
 		while ($cur_cnt <= $last_cnt){
			usleep(100*1000);
			$cur_cnt = DB::table('comments')->join('posts', 'posts.ID', '=', 'comments.comment_post_ID')
				->where('posts.post_author',$uid)->where('comment_read',0)->count();
		}
		
Log::info('User:'.$uid." got ".$cur_cnt." comments");
		
		$response['msg']       = $cur_cnt;
		$response['timestamp'] = time();
		return json_encode($response);
	}
	
	
	
}
