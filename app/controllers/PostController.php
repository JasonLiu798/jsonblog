<?php
class PostController extends BaseController {

	public function index() {
		$INFO_ST = microtime(1);
		$posts = Post::get_posts(Constant::$PAGESIZE);
		if ($posts) {
			Post::add_meta($posts);
		} else {
			$posts = null;
		}
		$sidebar = PostController::get_sidebar();
		$username = User::get_name_from_session(Session::get('user'));
		$view = View::make('index',
			array('title' => 'AsyncBlog',
				'username' => $username,
				'nav' => Constant::$NAV_IDX,
				'term4title' => null,
				'date4title' => null,
				'posts' => $posts,
				'sidebar' => $sidebar));
		$INFO_RUNTIME = microtime(1)-$INFO_ST;
		Log::info("Class:{get_class()},Method:{get_class_methods(PostController)},Runtime:{$INFO_RUNTIME}");
		return $view;
	}

	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function term_achive($term_id) {
		$msg = '';
		if (!preg_match(Constant::$DIGIT, $term_id)) {
			$msg = '分类编号格式错误';
		} else {
			$posts = Post::get_posts_by_term($term_id, Constant::$PAGESIZE);
			Post::add_meta($posts);
// 			$queries = DB::getQueryLog();
			// 			$last_query = end($queries);
			// 			Log::info('post date:'.$last_query['query']);
			$term4title = Term::get_name_taxonomy($term_id);
			if (is_null($term4title[0]->name)) {
				$msg = '分类/标签不存在';
			}
		}
		if (strlen($msg) > 0) {
			Redirect::action('ErrorController@show', array($msg));
			//App::abort(404);
		} else {
			$username = User::get_name_from_session();
			$sidebar = PostController::get_sidebar();
			$view = View::make('index',
				array('title' => $term4title[0]->name . '|Async Blog', 'username' => $username,
					'date4title' => null,
					'term4title' => $term4title, //'user4title'=>null,
					'posts' => $posts,
					'sidebar' => $sidebar));
			return $view;
		}
	}

	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function date_achive($date) {
		$msg = '';
		if (!preg_match(Constant::$REG_YEAR_MONTH, $date, $m)) {
			$msg = '日期格式错误';
		} else {
			$date_arr = explode("-", $date);
			$date4title['title'] = $date_arr[0] . '年' . $date_arr[1] . '月';
			$date4title['link'] = $date;
			$posts = Post::get_post_by_date($date, Constant::$PAGESIZE);
			Post::add_meta($posts);
// 			$queries = DB::getQueryLog();
			// 			$last_query = end($queries);
			// 			Log::info('post date:'.$last_query['query']);
		}
		if (strlen($msg) > 0) {
			return Redirect::route('error', array($msg));
		} else {
			$username = User::get_name_from_session(Session::get('user'));
			$sidebar = PostController::get_sidebar();
			$view = View::make('index',
				array('title' => $date . '|Async Blog', 'username' => $username,
					'date4title' => $date4title,
					'term4title' => null, //'user4title'=>null,
					'posts' => $posts,
					'sidebar' => $sidebar));
			return $view;
		}
	}

	/**
	 * get sidebar infos
	 * @return stdClass
	 */
	public static function get_sidebar() {
		$res = new stdClass;
		//get terms
		$term_stats = Term::getTermsAndStat();
		$res->cat_stats = Term::get_category($term_stats);
		$res->tag_stats = Term::get_tag($term_stats);
		//get post archive
		$post_stats = Post::getPostsStat();
		$res->post_stats = $post_stats;
		//get latest posts
		$latest_posts = Post::getNewstPost(5);
		$res->latest_posts = $latest_posts;
		//get latest comments
		$latest_comments = Comment::getLatestComments(5);
		$res->latest_comments = $latest_comments;
		return $res;
	}

	/**
	 * single，get post by ID,get post's comments by comment_post
	 * get pre post_id,get next post_id[order by date]
	 * INDEX: $post_id,$term_id=0
	 * POSTS BY TERM :$post_id,$term_id
	 * POSTS BY DATE :$post_id,$term_id=0,$date
	 * @param unknown $post_id,term_id=0,date=1990-01
	 * @return void|unknown
	 */
	public function single($post_id) {
//Log::info("Single,post_id:".$post_id);
		$posts = Post::get_post_by_id($post_id);
		$err = '';
		if (is_null($posts)) {
			$err = '博文不存在';
		} else {
			Post::add_meta($posts);
			$post = end($posts);
			if ($post->post_cover_img > 0) {
				$post->cover_img_url = PostImage::get_img_url_by_name($post->post_img_name);
			} else {
				$post->cover_img_url = null;
			}

			$comments = Comment::getCommentsByPostID($post_id);
			$pre_next_post = Post::get_pre_next_post($post_id);

			$username = User::get_name_from_session(Session::get('user'));
			$sidebar = PostController::get_sidebar();
			$resp = View::make('posts/single_comm_r',
				array('post' => $post,
					'comments' => $comments,
					'username' => $username,
					'title' => $post->post_title,
					'pre_next_post' => $pre_next_post,
					'sidebar' => $sidebar));
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err));
		} else {
			return $resp;
		}
	}


	/**
	 * 新建博文
	 * @return [type] [description]
	 */
	public function create()
	{
		$sess_user_json = Session::get('user', 'default');
		$user = json_decode($sess_user_json);
		$err = '';
		$errcode = 0;
		if (is_null($user)) {
			$err = "未登录";
			$errcode = Constant::$NOLOGIN;
		} else {
//			$method = Input::get('method');
			/**
			 * create post page
			 */
			$category = Term::get_all_categories();
			$term = new Term();
			$category_tree = $term->format_category2tree($category, '&nbsp;&nbsp;');
			$top5post_tag = Term::get_top5_post_tag();
			$sidebar = self::get_sidebar();

			$post_model = new Post;
			$post_id = $post_model->get_new_pk();
			$post = null;
			$resp = View::make('posts/edit_post', array(
				'title' => Lang::get('post.TITLE'), 'username' => $user->username,
				'category' => $category_tree,
				'post_tag' => $top5post_tag,
				'post' => $post,
				'nav' => Constant::$NAV_ADMIN,
				'post_id' => $post_id,
				'sidebar' => $sidebar));

		}
		if (strlen($err) > 0) {
			$resp = Response::json(array('status' => false , 'error' => $err, 'errorcode' =>
				$errcode));
		}
		return $resp;
	}

	/**
	 * Update POST|show edit post page
	 * @param $post_id
	 * @return mixed
	 */
	public function update($post_id) {
		$sess_user_json = Session::get('user', 'default');
		$user = json_decode($sess_user_json);
		$err = '';
		$errcode = Constant::$NOLOGIN;
		$method = Input::get('method');
		if (is_null($user)) {
			$err = "未登录";
		} else {
			try{
				$post = Post::get_post_by_id($post_id);
			}catch(Exception $e){
				$err = '获取博文失败';
				Log::error("Post Update|Get post error| {$e->getMessage()}");
			}
			if (empty($post) || strlen($err)>0 ) {
				$err = "博文不存在";
			} else {
				/**
				 * show update page
				 */
				try{
					$sidebar = self::get_sidebar();
					Post::add_meta($post);
					$post = $post[0];
					Term::get_post_tag_id_str($post);
					PostImage::post_img_name2url($post);
					$category_tree = Term::get_category_tree($post);
					$top5post_tag = Term::get_top5_post_tag();
				}catch(Exception $e){
					$err = '获取博文附加信息失败';
					Log::error("Post Update|Get post meta error| {$e->getMessage()}");
				}

				$resp = View::make('posts/edit_post', array(
					'title' => '修改博文', 'username' => $user->username,
					'post' => $post,
					'post_id' => $post->post_id,
					'category' => $category_tree,
					'post_tag' => $top5post_tag,
					'nav' => Constant::$NAV_ADMIN,
					'sidebar' => $sidebar));
			}
		}
		if (strlen($err) > 0) {

		}
		return $resp;
	}

	/**
	 * Post exist:SAVE
	 * Post not exist:UPDATE
	 * @param $post_id
	 */
	public function save($post_id){
		$sess_user_json = Session::get('user', 'default');
		$user = json_decode($sess_user_json);
		$err = '';
		$errcode = 0;
		if (is_null($user)) {
			$err = "未登录";
			$errcode = Constant::$NOLOGIN;
		} else {
			$post_id = Input::get('post_id');
			if (is_null($post_id)) {
				$err = '参数错误';
				Log::error("SAVE POST|{$err}|无post_id");
			} else {
				$post_model = new Post;
				if (!$post_model->chk_pk_format($post_id)) {
					$err = "博文ID格式错误 $post_id";
				} else {
					Log::info("Save Post {$post_id}");
					date_default_timezone_set("Europe/London");
					$now_gmt = date('Y-m-d H:i:s', time());
					date_default_timezone_set("Asia/Shanghai");
					$now = date('Y-m-d H:i:s', time());
					if( Post::chk_pid_exist($post_id) ){//已经存在，更新
						$post_model = Post::find($post_id);
						Log::info("Update POST[{$post_id}]");
						$post_model->post_modified = $now;
						$post_model->post_modified_gmt = $now_gmt;
					} else {
						//不存在，新建
						Log::info("CREATE POST[{$post_id}]");
						$post_model->ID = $post_id;
						$post_model->post_date = $now;
						$post_model->post_date_gmt = $now_gmt;
						$post_model->post_modified = $now;
						$post_model->post_modified_gmt = $now_gmt;
					}
					//POST_TITLE
					$post_model->post_title = Input::get('post_title'); //urldecode(urldecode());
					//POST_COVER_IMG
					$set_cover = (string)Input::get('set_cover');
					$cover_img_id = Input::get('cover_img_id');
					if ($set_cover === 'true' && PostImage::chk_exist($cover_img_id) > 0) {
						$post_model->post_cover_img = $cover_img_id;
					} else {
						$post_model->post_cover_img = 0;//图片不存在，或未设定，则为0
					}
					//POST_STATUS
					$is_draft = Input::get('$is_draft');
					if ($is_draft == true) {
						$post_model->post_status = Constant::$POST_DRAFT;
					} else {
						$post_model->post_status = Constant::$POST_PUBLISH;
					}
					//POST_CONTENT SUMMARY
//					$post_content = htmlentities( Input::get('post_content') , ENT_QUOTES);
//					$post_content = htmlspecialchars(Input::get('post_content'), ENT_QUOTES);
					$htmlFilter = new HtmlFilter;
					$start = microtime(1);
					$htmlFilter->addValues('a', 'href', array('#'));
					var_dump($htmlFilter->filter( Input::get('post_content') ));
					echo PHP_EOL, (microtime(1) - $start);

					$post_content = htmlspecialchars();
					//urldecode
					Log::info('Post Content:'.$post_content);
					if (strlen($post_content) > Constant::$POST_INDEX_CUT_SIZE) {
						$post_model->post_summary = Post::get_summary($post_content,
							Constant::$POST_INDEX_CUT_SIZE);
					} else {
						$post_model->post_summary = $post_content;
					}
					$post_model->post_content = $post_content;
					$post_model->post_author = $user->uid;

					Log::info('Post Save:set_cover' . $set_cover . ' ,iid:' .
						$cover_img_id . ',draft:' . $is_draft);

					try {
						$post_model->save();
					} catch (Exception $e) {
						$err = "保存博文失败";
						Log::error("Save Post error:{$e->getMessage()}");
					}
					if (strlen($err) == 0) {
						/**
						 * 处理 保存文章 分类
						 */
						$category_id = Input::get('category');
						$term_id_arr = array();
						if (Term::chk_cat_exist($category_id) > 0) {
							array_push($term_id_arr, $category_id);
						} else {
							//默认为未分类
							array_push($term_id_arr, Constant::$NO_CATEGORY_ID);
						}
						/**
						 * 处理 新建文章 标签
						 */
						$post_tag_ids = Input::get('post_tag_ids');
						if (strlen($post_tag_ids) > 0) {
							if (Term::chk_tag_ids_str($post_tag_ids)) {
								$cat_ids = Term::process_idstr($post_tag_ids);
								$term_id_arr = array_merge($term_id_arr, $cat_ids);
							} else {
								$err = '标签串格式异常';
							}
						}//else无标签
						//过滤已经存在的 POST-标签/分类 关系
						Term::delete_exist_post_term_relation($post_id, $term_id_arr);
						try {
							Log::info('new post terms:' . Tool::ARR2STR($term_id_arr));
							Post::create_post_term($post_id, $term_id_arr);
						} catch (Exception $e) {
							$err = "添加博客分类标签失败";
							Log::error("$err :" . $e->getMessage());
						}
					}//end of if $err , else post create fail
				}//end of else 博文ID格式错误
			}//end of 空博文id
		}
		if(strlen($err)>0){
			$resp = Response::json(array('status' => false , 'error' => $err, 'errorcode' =>
				$errcode));
		}else{
			$resp = Response::json(array('status' => true,'post_id'=>$post_id ));
		}
		return $resp;
	}

	// public function update() {
	// 	$sess_user_json = Session::get('user', 'default');
	// 	$user = json_decode($sess_user_json);
	// 	$msg = '';
	// 	if (is_null($user)) {
	// 		$msg = "未登录";
	// 	} else {

	// 	}
	// 	if (strlen($msg) > 0) {
	// 		return Redirect::route('error', array($msg));
	// 	} else {
	// 		return $view;
	// 	}
	// }

	/**
	 * delete post relate comments,terms,post_image
	 * @param unknown $post_id
	 */
	public function delete_all($post_id) {
		Post::delete_all($post_id);
		return Redirect::action('PostController@admin');
	}
	/**
	 *
	 * @param unknown $post_id
	 */
	public function delete_post($post_id) {
		Post::delete_all($post_id);
		return Redirect::action('PostController@admin');
	}

	/**
	 * batch delete posts
	 * @return [type] [description]
	 */
	public function batch_delete() {
		$err = '';
		$ids = Input::get('delete_ids');
		Log::info('Delete posts:' . $ids);
		if (is_null($ids) || strlen($ids) <= 0) {
			$err = '参数错误';
		} else {
			$id_arr = explode(',', $ids);
			if (count($id_arr) > 0) {
				foreach ($id_arr as $pid) {
					Post::delete_all($pid);
					// $comm->delete();
				}
			} else {
				$err = '参数错误';
			}
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return Redirect::route('post_admin'); //,array($post_id));
		}
	}

	/**
	 * 博文管理
	 * @return unknown
	 */
	public function admin() {
		$sess_user = Session::get('user');
		if (is_null($sess_user)) {
			return Redirect::action('PostController@index');
		}
		$username = User::get_name_from_session($sess_user);
		$user_id = User::get_userid_from_session($sess_user);

		$posts = Post::get_posts_by_userid($user_id, Constant::$ADMIN_PAGESIZE);
		if (count($posts) <= 0) {
			$posts = null;
		} else {
			Post::add_meta($posts);
		}
		$view = View::make('posts/post_admin',
			array('title' => '博文管理',
				'username' => $username,
				'posts' => $posts,
				'menu' => 'post',
				'nav' => Constant::$NAV_ADMIN)
		);
		return $view;
	}

	public function search() {
		$INFO_ST = microtime(1);
		$page = Input::get('page');
		$search_text_raw = Input::get('searchtext');

		$username = User::get_name_from_session();

		$search_text_nodomma = str_replace(",", " ", trim($search_text_raw));
		Log::info('search:' . $search_text_raw . ',page:' . $page);
		// $page = 1;
		$pagesize = Constant::$PAGESIZE;
		// $posts = Post::search_name_content($search_text_nodomma, $page, $per_page);
		$res = Post::search_name_content($search_text_nodomma, $page, $pagesize);
		$err = '';
		if ($res[0]) {
			$sidebar = PostController::get_sidebar();

			$posts = $res[1];
			$total = $res[2];
			$totalpage = ceil($total / Constant::$PAGESIZE);
			// $paginator = Paginator::make($posts, $res[3], Constant::$PAGESIZE);

			$resp = View::make('posts/search',
				array('title' => '博文搜索', 'username' => $username, 'sidebar' => $sidebar, 'searchtext' => $search_text_raw, 'posts' => $posts,
					'page' => $page,
					'totalpage' => $totalpage));
		} else {
			$err = $res[1];
		}
		if (strlen($err) > 0) {
			return Redirect::route('error', array($err)); //,array($post_id));
		} else {
			return $resp;
		}
		$INFO_RUNTIME = microtime(1)-$INFO_ST;
		Log::info("Class:{__CLASS__},Method:{__METHOD__},Runtime:{$INFO_RUNTIME}");
		return $resp;
	}

	public function test() {
//		$post_model = new Post;
//		echo $post_model->chk_pk(243);
		$htmlcode = <<<EOF
		<p>
			<div REL="add-2012-xs">ssssssssssssssssssss</DIV>
			<script>skdflkjdjklsf</script>
			<img href="http://sdjkfjkdf" width=100 height=233 />
			<a href="http://ww.baidu.com">sdjk</a>
			sdfsdfjkl
        </p>
EOF;
		//$htmlFilter->addLabel('a', array('href' => array('values' => array('#'))));

		$start = microtime(1);
		$htmlFilter = new HtmlFilter;
		echo $htmlFilter->filter($htmlcode);
//		var_dump($htmlFilter->filter($htmlcode));
		echo (microtime(1) - $start);

//		$id_arr = Term::process_idstr('108,100,110');
//		echo "BF:\n";
//		print_r($id_arr);
//		Term::delete_exist_post_term_relation(36,$id_arr);
//		echo "\nAF:\n";
//		print_r($id_arr);

//		echo Term::find(97)->termtaxonomy->taxonomy;
//		echo Term::where('name','=', 'aa')->firstOrFail()->termtaxonomy->taxonomy;
//		$a = explode(',', 'aaa' );
//		echo count ($a);
//		echo current($a);
//		echo Term::get_tag_id_by_name('aa');
		// $total = 6;
//		// echo ceil($total / Constant::$PAGESIZE);

//		$c = Comment::find(1);
//		echo gettype($c);



		// $comments = Post::find(37)->comments;
		// print_r($comments);
		// return;

	}

}
