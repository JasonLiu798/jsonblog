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
	 * http://www.lblog.com/category/api/create?new_catagory_name=cat&new_category_parent=23
	 */
	public function api_create_category() {
		$sess_user = Session::get('user');
		$err = '';
		$errcode = 0;
		if (is_null($sess_user)) {
			$err = '未登录';
			$errcode = Constant::$NOLOGIN;
		} else {
			$category_name = trim(urldecode(urldecode(Input::get('new_catagory_name'))));
			if (strlen($category_name) <= 0) {
				$err = '分类名为空';
			} else {
				if (Term::chk_category_name_exist($category_name) > 0) {
					$err = '分类名已经存在';
				} else {
					$new_cat_parent_id = Input::get('new_category_parent');
					if (Term::chk_cat_exist($new_cat_parent_id) > 0) {
						try {
							$term_id = Term::create_category_api($category_name, $new_cat_parent_id);
						} catch (Exception $e) {
							$err = '添加分类失败';
							Log::error('添加分类失败：' . $e->getMessage());
						}
						$resp = Response::json(array('success' => 'true', 'term_id' => $term_id));
					}
				}
			}
		}
		if (strlen($err) > 0) {
			$resp = Response::json(array('status' => 'fail', 'error' => $err, 'error' => $errcode));
		}
		return $resp;
	}

	/**
	 * new Post,dynamic add post tag
	 * http://www.lblog.com/tag/api/create?new_tag_name=cat
	 */
	public function create_tag_api() {
		$sess_user = Session::get('user');
		$err = '';
		$errcode = 0;
		if (is_null($sess_user)) {
			$err = '未登录';
			$errcode = Constant::$NOLOGIN;
		} else {
			$tag_name = urldecode(urldecode(Input::get('new_tag_name')));
			if (strlen(trim($tag_name)) <= 0) {
				$err = '标签名为空';
			} else {
				if ( Term::chk_tag_name_exist($tag_name) > 0) {
					try{
						$err = Term::get_tag_id_by_name($tag_name);
						if($err>0 ){
							$errcode = Constant::$TAG_EXIST;
						}else{
							$err = '获取已存在标签ID失败';
							Log::error("获取已存在标签ID失败 $tag_name ");
						}
					}catch(Exception $e){
						$err = '获取已存在标签ID失败';
						Log::error("获取已存在标签ID失败 $tag_name :" . $e->getMessage());
					}
				} else {
					try {
						$term_id = Term::create_tag_api($tag_name);
					} catch (Exception $e) {
						$err = '创建标签失败';
						Log::error("$err $tag_name :" . $e->getMessage());
					}
					$resp = Response::json(array('status' => true, 'term_id' => $term_id));
				}
			}
		}

		if (strlen($err) > 0) {
			$resp = Response::json(array('status' => false , 'error' => $err, 'errorcode' =>
				$errcode));
		}
		return $resp;
	}

	/**
	 * 分类名校验api
	 * @return [type] [description]
	 */
	public function api_chk_cat_name_exist() {
		$term_name = urldecode(urldecode(Input::get('term_name')));
		if (Term::chk_category_name_exist($term_name) > 0) {
			$resp = Response::json(array('status' => 'fail', 'error' => '标签名已经存在'));
		} else {
			$resp = Response::json(array('status' => 'success'));
		}
		return $resp;
	}

	/**
	 * 标签名校验api
	 * @return [type] [description]
	 */
	public function api_chk_tag_name_exist() {
		$term_name = urldecode(urldecode(Input::get('term_name')));
		if (Term::chk_category_name_exist($term_name) > 0) {
			$resp = Response::json(array('status' => 'fail', 'error' => '分类名已经存在'));
		} else {
			$resp = Response::json(array('status' => 'success'));
		}
		return $resp;
	}

	/**
	 * Admin,delete category by term_id
	 * @param  [type]
	 * @return [type]
	 */
	public function cat_delete($tid) {
		$err = '';
		if (Term::chk_cat_exist_tid($tid) <= 0) {
			$err = '分类不存在';
		} else {
			Log::info('DELETE TID:' . $tid);
			Term::delete_with_child($tid);
			//Term::delete_term_tid($tid);
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('cat_admin'); //,array($post_id));
		}
	}

	public function cat_batch_delete() {
		$err = '';
		$ids = Input::get('delete_ids');
		Log::info('Delete ids:' . $ids);
		if (is_null($ids) || strlen($ids) <= 0) {
			$err = '参数错误';
		} else {
			$id_arr = explode(',', $ids);
			if (count($id_arr) > 0) {
				foreach ($id_arr as $tid) {
					Term::delete_with_child($tid);
				}
			} else {
				$err = '参数错误';
			}
		}

		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('cat_admin'); //,array($post_id));
		}
		// return Redirect::route('cat_admin'); //,array($post_id));
	}

	public function cat_admin() {
//		$sess_user = Session::get('user');
		//		$user_id = User::get_userid_from_session($sess_user);

		$username = User::get_name_from_session();
		$term_model = new Term;
		$cats = $term_model->get_admin_category_db( Constant::$ADMIN_PAGESIZE );
//
		//$category = Term::get_all_categories();
//		$term = new Term();
//		$categories = $term->format_category2tree($cats, '——');

		$view = View::make('term/category_admin',
			array('title' => '分类管理',
				'username' => $username,
				'categories' => $cats,
				'nav' => Constant::$NAV_ADMIN,
				'menu' => 'category'));
		return $view;
	}

	public function tag_admin() {
		$sess_user = Session::get('user');
		$username = User::get_name_from_session($sess_user);
		$user_id = User::get_userid_from_session($sess_user);

		$tags = Term::get_admin_tag(); //Constant::$ADMIN_PAGESIZE);

		//$category = Term::get_all_categories();
		// $term = new Term();
		// $categories = $term->format_category2tree($cats, '——');

		$view = View::make('term/tag_admin',
			array('title' => '标签管理',
				'username' => $username,
				'nav' => Constant::$NAV_ADMIN,
				'tags' => $tags,
				'menu' => 'tag'));
		return $view;
	}

	public function tag_create() {
		$err = '';
		$tag_name = Input::get('new_tag_name'); //urldecode(urldecode(Input::get('new_catagory_name')));
		if (Term::chk_tag_name_exist($tag_name) > 0) {
			$err = '标签名已经存在';
			//return Response::make('分类名已经存在!', 500 );
		} else {
			//$new_category_parent = Input::get('new_category_parent');
			$term_id = Term::create_tag_api($tag_name);
			if ($term_id <= 0) {
				//add success
				$err = '分类添加失败';
			}
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('tag_admin');
		}
	}

	public function tag_delete($tid) {
		$err = '';
		if (Term::chk_tag_exist_tid($tid) <= 0) {
			$err = '标签不存在';
		} else {
			Log::info('DELETE TID:' . $tid);
			Term::delete_term_tid($tid);
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('tag_admin'); //,array($post_id));
		}
	}

	public function tag_batch_delete() {
		$err = '';
		$ids = Input::get('delete_ids');
		Log::info('Delete ids:' . $ids);
		if (is_null($ids) || strlen($ids) <= 0) {
			$err = '参数错误';
		} else {
			$id_arr = explode(',', $ids);
			if (count($id_arr) > 0) {
				foreach ($id_arr as $tid) {
					Term::delete_term_tid($tid);
				}
			} else {
				$err = '参数错误';
			}
		}

		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('tag_admin'); //,array($post_id));
		}
		// return Redirect::route('cat_admin'); //,array($post_id));
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
			return Redirect::route('cat_admin');
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

			if (Term::chk_cat_exist_tid($tid) != 1) {
				$err = '分类不存在';
			} else {
				if (is_null($method)) {
					//update page,show pre update page
					$cat = Term::get_cat_tid($tid);

					$queries = DB::getQueryLog();
					$last_query = end($queries);
					Log::info('Term:' . $last_query['query']);

					$cats = Term::get_admin_category(); //Constant::$ADMIN_PAGESIZE);
					$term = new Term();
					$categories = $term->format_category2tree($cats, '——');
					foreach ($categories as $cate) {
						if ($cate->term_id == $cat->parent) {
							$cate->select = 1;
						} else {
							$cate->select = 0;
						}
						if ($cate->term_id == $cat->term_id) {
							$cate->delete = 1;
						} else {
							$cate->delete = 0;
						}
					}
					$resp = View::make('term/category_update',
						array('title' => '分类修改',
							'username' => $username,
							'cat' => $cat,
							'nav' => Constant::$NAV_ADMIN,
							'categories' => $categories,
							'menu' => 'category'));
				} else if ($method === 'update') {
					// $tid = Input::get('term_id');
					$pid = Input::get('category_parent');
					$name = Input::get('category_name');

					Log::info('Update Catid:' . $tid . ',pid:' . $pid . ',name:' . $name);
					$old_name = Term::get_cat_name($tid);
					if ($old_name !== $name && Term::chk_category_name_exist($name) > 0) {
						$err = '修改的分类名已经存在';
					} else if ($old_name === $name || Term::chk_category_name_exist($name) <= 0) {
						$cnt = Term::chk_cat_exist_tid($pid);
						if ($pid === $tid) {
							$err = '父分类不能为自己';
						} else {
							if ($pid != 0 && $cnt <= 0) {
								$err = '父分类不存在';
							} else {
								Term::update_term($tid, $name, $pid);
								$resp = Redirect::route('cat_admin');
							}
						}
					} else {
						$err = '参数错误';
					}
				} else {
					$err = '无效操作';
				}
			}
		}

		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return $resp;
		}
	}

	/**
	 * [cat_update description]
	 * @param  [type] $tid [description]
	 * @return [type]      [description]
	 */
	public function tag_update($tid) {
		$err = '';
		$method = Input::get('method');
		$resp = null;
		$sess_user = Session::get('user');
		if (is_null($sess_user)) {
			$err = '未登录';
		} else {
			$username = User::get_name_from_session($sess_user);
			$user_id = User::get_userid_from_session($sess_user);

			if (Term::chk_tag_exist_tid($tid) != 1) {
				$err = '标签不存在';
			} else {
				if (is_null($method)) {
					//update page,show pre update page
					$tag = Term::get_tag_tid($tid);

					// $queries = DB::getQueryLog();
					// $last_query = end($queries);
					// Log::info('Term:' . $last_query['query']);

					// $cats = Term::get_admin_category(); //Constant::$ADMIN_PAGESIZE);
					// $term = new Term();
					// $categories = $term->format_category2tree($cats, '——');
					// foreach ($categories as $cate) {
					// 	if ($cate->term_id == $cat->parent) {
					// 		$cate->select = 1;
					// 	} else {
					// 		$cate->select = 0;
					// 	}
					// }
					$resp = View::make('term/tag_update',
						array('title' => '标签修改',
							'username' => $username,
							'nav' => Constant::$NAV_ADMIN,
							'tag' => $tag,
							// 'categories' => $categories,
							'menu' => 'tag'));
				} else if ($method === 'update') {
					// $tid = Input::get('term_id');
					// $pid = Input::get('category_parent');
					$name = Input::get('tag_name');
					if (Term::chk_tag_name_exist($name) > 0) {
						$err = '修改的标签名已经存在';
					} else {
						Log::info('Update Catid:' . $tid . ',name:' . $name);
						Term::update_tag($tid, $name);
						$resp = Redirect::route('tag_admin');
					}
				} else {
					$err = '无效操作';
				}
			}
		}

		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return $resp;
		}
	}

}
