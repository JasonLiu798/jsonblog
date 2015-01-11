<?php

class TermsController extends BaseController {

	public function create($param = 'page') {
		if ($param === 'page') {

		} else if ($param === 'do') {

		} else if ($param === 'asyncadd') {
			$cat_name = Input::get('new_catagory_name');
			$cat_parent_id = Input::get('new_category_parent');
			$validator = Validator::make(
				array(
					'标签名' => $cat_name,
					'父标签编号' => $cat_parent_id),
				array(
					'标签名' => 'required|unique:terms,name|between:1,32',
					'父标签编号' => 'required|exists:terms,term_id')
			);
			Term::add_category();

			if ($validator->fails()) {
				$res = array();
				$msgs = $validator->messages();
				$i = 0;
				foreach ($msgs->all() as $msg) {
					$res['msg' . $i] = $msg;
					Log::info($msg);
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
	public function create_category_api() {
		$sess_user = Session::get('user');
		$user_id = User::get_userid_from_session($sess_user);
		if (is_null($sess_user)) {
			return Rresponse::json(array('success' => 'false', 'msg' => 'nologin'));
		}
		$category_name = urldecode(urldecode(Input::get('new_catagory_name')));
		if (Term::chk_term_name_exist($category_name) > 0) {
			return Response::make('分类名已经存在!', 500);
		}

		$new_category_parent = Input::get('new_category_parent');
		$term_id = Term::create_category_api($category_name, $new_category_parent);
		if ($term_id <= 0) {
			return Rresponse::json(array('success' => 'false', 'msg' => 'failed'));
		}
		return Response::json(array('success' => 'true', 'term_id' => $term_id));
	}

	public function chk_term_name_exist() {
		$term_name = urldecode(urldecode(Input::get('term_name')));
		if (Term::chk_term_name_exist($term_name) > 0) {
			return Response::make('标签名已经存在!', 500); //$statusCode);
			//return Response::json(array('success' => 'false', 'msg' => '标签名已经存在!'));
		}
	}

	/**
	 * new Post,dynamic add post tag
	 * http://www.lblog.com/tag/api/create?new_tag_name=cat
	 */
	public function create_tag_api() {
		// $sess_user = Session::get('user');
		// $user_id = User::get_userid_from_session( $sess_user );
		// if(is_null($sess_user )) {
		// 	//return Response::json(array('success' => 'false', 'msg' => 'nologin'));
		// 	return Response::make('未登录', 500 );//$statusCode);
		// }
		$tag_name = urldecode(urldecode(Input::get('new_tag_name')));
		if (Term::chk_term_name_exist($tag_name) > 0) {
			return Response::make('标签名已经存在!', 500); //$statusCode);
			//return Response::json(array('success' => 'false', 'msg' => '标签名已经存在!'));
		}
		$term_id = Term::create_tag_api($tag_name);
		if ($term_id <= 0) {
			return Response::json(array('success' => 'false', 'msg' => 'failed'));
		}
		return Response::json(array('success' => 'true', 'term_id' => $term_id));
	}

	/**
	 * Admin,delete category by term_id
	 * @param  [type]
	 * @return [type]
	 */
	public function cat_delete($tid) {
		$err = '';
		if (Term::chk_category_name_exist($category_name) <= 0) {
			$err = '分类名不存在';
		} else {
			Term::delete_cat_tid($tid);
		}

	}

	public function cat_admin() {
		$sess_user = Session::get('user');
		$username = User::get_name_from_session($sess_user);
		$user_id = User::get_userid_from_session($sess_user);

		$cats = Term::get_admin_category(); //Constant::$ADMIN_PAGESIZE);

		//$category = Term::get_all_categories();
		$term = new Term();
		$categories = $term->format_category2tree($cats, '——');

		$view = View::make('term/category_admin',
			array('title' => '分类管理',
				'username' => $username,
				'categories' => $categories,
				'menu' => 'category'));
		return $view;
	}

	public function cat_create() {
		// $sess_user = Session::get('user');
		// $user_id = User::get_userid_from_session( $sess_user );
		// if(is_null($sess_user )) {
		// 	//return Response::json(array('success' => 'false', 'msg' => 'nologin'));
		// 	return Response::make('未登录', 500 );//$statusCode);
		// }
		$err = '';
		$category_name = Input::get('new_category_name'); //urldecode(urldecode(Input::get('new_catagory_name')));
		if (Term::chk_category_name_exist($category_name) > 0) {
			$err = '分类名已经存在';
			//return Response::make('分类名已经存在!', 500 );
		} else {
			$new_category_parent = Input::get('new_category_parent');
			$term_id = Term::create_category_api($category_name, $new_category_parent);
			if ($term_id <= 0) {
//add success
				$err = '分类添加失败';
				// Redirect::route('cat_admin');//,array($post_id));
				//return Rresponse::json(array('success' => 'false', 'msg' =>'添加分类失败'));
			}
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('cat_admin'); //,array($post_id));
		}
		// return Response::json(array('success' => 'true', 'term_id' => $term_id));
	}

	public function cat_update($tid) {
		$err = '';
		$method = Input::get('method');
		$resp = null;
		$sess_user = Session::get('user');
		if (is_null($sess_user)) {
			$err = '未登录';
		} else {
			$username = User::get_name_from_session($sess_user);
			$user_id = User::get_userid_from_session($sess_user);

			if (is_null($method)) {
				if (Term::chk_cat_exist_tid($tid) != 1) {
					$err = '分类不存在';
				}
				//show pre update page
				$cat = Term::get_cat_tid($tid);

				$queries = DB::getQueryLog();
				$last_query = end($queries);
				Log::info('Term:' . $last_query['query']);

				$cats = Term::get_admin_category(); //Constant::$ADMIN_PAGESIZE);
				$term = new Term();
				$categories = $term->format_category2tree($cats, '——');
				foreach ($categories as $cate) {
					if ($cate->parent == $cat->term_id) {
						$cate->select = 1;
					} else {
						$cate->select = 0;
					}
				}
				$resp = View::make('term/category_update',
					array('title' => '分类修改',
						'username' => $username,
						'cat' => $cat,
						'categories' => $categories,
						'menu' => 'category'));
			} else if ($method === 'update') {

			} else {
				$err = '无效操作';
			}

			// $category_name = Input::get('new_category_name');//urldecode(urldecode(Input::get('new_catagory_name')));
			// if(Term::chk_category_name_exist($category_name) > 0){
			// 	$err = '分类名已经存在';
			// 	//return Response::make('分类名已经存在!', 500 );
			// }else{
			// 	$new_category_parent = Input::get('new_category_parent');
			// 	$term_id = Term::create_category_api($category_name,$new_category_parent);
			// 	if($term_id<=0){//add success
			// 		$err = '分类添加失败';
			// 		// Redirect::route('cat_admin');//,array($post_id));
			// 		//return Rresponse::json(array('success' => 'false', 'msg' =>'添加分类失败'));
			// 	}
			// }
		}

		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return $resp;
		}
	}

}
