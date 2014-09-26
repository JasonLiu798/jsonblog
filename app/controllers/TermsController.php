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
	 * new Post,dynamic add category
	 * 
	 * http://www.lblog.com/category/api/create?new_catagory_name=cat&new_category_parent=23
	 */
	public function create_category_api(){
		$sess_user = Session::get('user');
		$user_id = User::getUserIDFromSession( $sess_user );
		if(is_null($sess_user )) {
			return Rresponse::json(array('success' => 'false', 'msg' => 'nologin'));
		}
		$category_name = urldecode(urldecode(Input::get('new_catagory_name')));
		if(Term::chk_term_name_exist($category_name) > 0){
			return Response::make('分类名已经存在!', 500 );
		}
		
		$new_category_parent = Input::get('new_category_parent');
		$term_id = Term::create_category_api($user_id,$category_name,$new_category_parent);
		if($term_id<=0){
			return Rresponse::json(array('success' => 'false', 'msg' =>'failed'));
		}
		return Response::json(array('success' => 'true', 'term_id' => $term_id));
	}
	
	public function chk_term_name_exist(){
		$term_name = urldecode(urldecode(Input::get('term_name')));
		if(Term::chk_term_name_exist($term_name) > 0){
			return Response::make('标签名已经存在!', 500 );//$statusCode);
			//return Response::json(array('success' => 'false', 'msg' => '标签名已经存在!'));
		}
	}
	
	/**
	 * new Post,dynamic add post tag
	 * http://www.lblog.com/tag/api/create?new_tag_name=cat
	 */
	public function create_tag_api(){
		$sess_user = Session::get('user');
		$user_id = User::getUserIDFromSession( $sess_user );
		if(is_null($sess_user )) {
			//return Response::json(array('success' => 'false', 'msg' => 'nologin'));
			return Response::make('未登录', 500 );//$statusCode);
		}
		$tag_name = urldecode(urldecode(Input::get('new_tag_name')));
		if(Term::chk_term_name_exist($tag_name) > 0){
			return Response::make('标签名已经存在!', 500 );//$statusCode);
			//return Response::json(array('success' => 'false', 'msg' => '标签名已经存在!'));
		}
		$term_id = Term::create_tag_api($user_id,$tag_name);
		if($term_id<=0){
			return Response::json(array('success' => 'false', 'msg' =>'failed'));
		}
		return Response::json(array('success' => 'true', 'term_id' => $term_id));
	}
	
	
	
	public function delete(){
		Term::delete_by_tid();
	}

	
	
	
}
