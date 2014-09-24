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
	 * 
	 * http://www.lblog.com/category/api/create?new_catagory_name=cat&new_category_parent=23
	 */
	public function create_category_api(){
		$category_name = Input::get('new_catagory_name');
		$new_category_parent = Input::get('new_category_parent');
		$term_id = Term::create_category_api($category_name,$new_category_parent);
		return Response::json(array('success' => 'true', 'data' => $term_id));
	}
	
	
	
	public function delete(){
		Term::delete_by_tid();
	}

	
	
	
}
