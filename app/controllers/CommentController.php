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
	
// 	public function update_pre(){
// 		$cid = Input::get('comment_id');	
// 	}
	
	
	
	
	
	
}
