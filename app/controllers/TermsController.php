<?php

class TermsController extends BaseController {
	
	
	public function create($param='page'){
		if($param === 'page'){
			
		}else if ($param === 'do'){
			
		}else if ($param === 'asyncadd'){
			$cat_name = Input::get('new_catagory_name');
			$cat_parent_id = Input::get('new_category_parent');
			$validator = Validator::make(
				array(
						'标签名' => $cat_name,
						'父标签编号' => $cat_parent_id),
				array(
						'标签名' => 'required|unique:terms,name|between:1,32',
						'父标签编号'=> 'required|exists:terms,term_id')
			);
			Term::add_category();
			
			
			if( $validator->fails() ){
				$res = array();
				$msgs = $validator->messages();
				$i=0;
				foreach ($msgs->all() as $msg){
					$res['msg'.$i] = $msg;
					Log::info( $msg );
					$i++;
				}
				
				$json = Response::json($res);
				return $json;
			}
		}
	}
	
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

	
	
	
}
