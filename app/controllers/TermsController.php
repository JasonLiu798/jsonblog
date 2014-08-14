<?php

class TermsController extends BaseController {
	
	/**
	 * 添加Post时，异步添加用
	 * www.lblog.com/term/ajax_create?post_id=3&term_name=fortest&term_taxonomy='category'&parent=0
	 */
	public function ajax_create(){
// 		date_default_timezone_set("Europe/London");
// 		$comment_date_gmt = date('Y-m-d H:i:s',time());
// 		date_default_timezone_set("Asia/Shanghai");
// 		$comment_date = date('Y-m-d H:i:s',time());
	
		Term::ajax_create();
		//return Redirect::action('PostController@single', array($post_id));
	}
	
	public function delete(){
		Term::delete_by_tid();
	}
	
	public function create(){
		
	}
	
	
	
}
