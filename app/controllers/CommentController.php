<?php

class CommentController extends BaseController {
	
	public function create(){
		$post_id = Input::get('post_id');
		Comment::create_in_post();
		
		return Redirect::action('PostController@single', array($post_id));
	}
	
	/**
	 * T:/comment/delete?cid=48
	 */
	public function delete(){
		$cid = Input::get('cid');
		$comm = Comment::find($cid);
		
		$comm->delete();
		//Comment::destroy($cid);
	}
	
	
	public function get_unread_comment_cnt($uid){
		$sess_user_json = Session::get('user');
		$response = array();
		if( is_null($sess_user_json) || is_null($uid) ){
			$response['msg']  = '';//no login
			return json_encode($response);
		}
		
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
	
// 	public function update_pre(){
// 		$cid = Input::get('comment_id');	
// 	}
	
	
	
	
	
	
}
