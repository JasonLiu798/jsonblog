<?php
class PostController extends BaseController {

//	private static $pagecache;
//	function __construct(){
//		self::$pagecache = new PageCache;
//	}

	/**
	 * Post Index
	 * @return mixed
	 */
	public function index($page=1) {
		$INFO_ST = microtime(1);
		$resp = null;

		/*
		 * 首页缓存，后续页数读库
		if( $page == 1 ){
			$view_cache = $this->pagecache->get_index();
			if(!is_null($view_cache)){
				$resp = $view_cache;
			}
		}
		*/

		$error = '';
		if(is_null($resp)) {
			$page = intval($page);
			if (!is_int($page) || $page < 0) {
				$error = "页数参数错误";
			}else{
				$redis = LRedis::connection();
				$post_model = new Post;
				$res = $post_model->get_posts_onepage_with_meta($page, Constant::$PAGESIZE, $redis);
				if ($res->status) {
					$total_cnt = $res->total;
					$posts = $res->posts;
					if ($total_cnt > 0) {
						$totalpage = ceil($total_cnt / Constant::$PAGESIZE);
						Log::info("total count $total_cnt ,total page $totalpage");
					} else {
						$totalpage = 1;
					}

					$sidebar = $this->get_sidebar($redis);
					$username = User::get_name_from_session();
					Log::info("username: $username");
					$resp = View::make('index',
						array('title' => 'AsyncBlog', 'sidebar' => $sidebar,
							'page' => $page, 'totalpage' => $totalpage,
							'username' => $username, 'nav' => Constant::$NAV_IDX,
							'posts' => $posts));
					if ($page == 1) {
						$this->pagecache->update_index($resp);
					}
				} else {
					Log::error($post_model->error);
					$posts = null;
					$total_cnt = -1;
					$error = $post_model->error;
				}
			}
		}

		if(strlen($error)>0){
			$resp = Redirect::route('error', array($error));
		}

		$INFO_RUNTIME = round(1000*(microtime(1)-$INFO_ST),5);
		$method = __METHOD__;
		Log::info("{$method},Runtime:{$INFO_RUNTIME}");
		return $resp;
	}

	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function term_achive($term_id) {
		$INFO_ST = microtime(1);
		$error = '';
		$resp = null;
		if (!preg_match(Constant::$DIGIT, $term_id)) {
			$msg = '分类编号格式错误';
		} else {
			$term = Term::find($term_id);
			if (is_null($term)) {
				$error = '分类/标签不存在';
			}else{
				$post_model = new Post;
				$posts = $post_model->get_posts_by_term($term_id, Constant::$PAGESIZE);
			}
		}
		if (strlen($error) > 0) {
			$resp = Redirect::action('ErrorController@show', array($error));
		} else {
			$username = User::get_name_from_session();
			$sidebar = $this->get_sidebar();
			$resp = View::make('index',
				array('title' => $term->name.'|Async Blog', 'username' => $username,
					'term' => $term, //'user4title'=>null,
					'posts' => $posts,
					'sidebar' => $sidebar));
		}
		$INFO_RUNTIME = round(1000*(microtime(1)-$INFO_ST),5);
		$method = __METHOD__;
		Log::info("{$method},Runtime:{$INFO_RUNTIME}");
		return $resp;
	}

	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function date_achive($date) {
		$error = '';
		if (!preg_match(Constant::$REG_YEAR_MONTH, $date, $m)) {
			$error = '日期格式错误';
		} else {
			$date_arr = explode("-", $date);
			$date4title = new stdClass;
			$date4title->title = $date_arr[0] . '年' . $date_arr[1] . '月';
			$date4title->link = $date;
			$post_model = new Post;
			$posts = $post_model->get_post_by_date($date, Constant::$PAGESIZE);
		}
		if (strlen($error) > 0) {
			return Redirect::route('error', array($error));
		} else {
			$username = User::get_name_from_session(Session::get('user'));
			$sidebar = $this->get_sidebar();
			$view = View::make('index',
				array('title' => $date . '|Async Blog', 'username' => $username,
					'date4title' => $date4title,
					'posts' => $posts,
					'sidebar' => $sidebar));
			return $view;
		}
	}

	/**
	 * get sidebar infos
	 * @return stdClass
	 */
	public function get_sidebar($redis = null) {
		$sidebar_cache = $this->pagecache->get_sidebar();
		if(  !is_null($sidebar_cache)){
			$res = $sidebar_cache;
		}else{
			if(is_null($redis)){
				$redis = LRedis::connection();
			}
			$sidebar = new stdClass;
			//get terms
			$term_model = new Term;
			$term_stats = $term_model->get_terms_and_stat($redis);
			$sidebar->cat_stats = Term::get_category($term_stats);
			$sidebar->tag_stats = Term::get_tag($term_stats);
			//get post archive
			$post_stats = Post::getPostsStat();
			$sidebar->post_stats = $post_stats;
			//get latest posts
			$post_model = new Post;
			$latest_posts = $post_model->get_latest_count_post(5,$redis);
			$sidebar->latest_posts = $latest_posts;
			//get latest comments
			$comm_model = new Comment;
			$latest_comments = $comm_model->get_latest_comments(5,$redis);
			$sidebar->latest_comments = $latest_comments;
			$view = View::make('templates/sidebar',array('sidebar'=>$sidebar));
			$res = $view.' ';
			$this->pagecache->update_sidebar($res);
		}
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
		$error = '';
		$post_model = new Post;
		$post = $post_model->get_post($post_id);
		Log::info('single'.$post->post_id);
		if (is_null($post)) {
			$error = '博文不存在';
		} else {
			$comm_model = new Comment;
			$comments = $comm_model->get_post_comments($post_id,Constant::$PAGESIZE);
			$pre_next_post = Post::get_pre_next_post($post_id);

			$user = User::get_user_from_session();

			$username = is_null($user)?null:$user->user_login;
			$sidebar = $this->get_sidebar();
			$comments->fragment('comments_anchor');
//			$resp = View::make('posts/single',
			$resp = View::make('posts/single_v2',
				array('post' => $post,
					'comments' => $comments,
					'username' => $username,
					'user'=>$user,
//					'total'=>$comments->getTotal(),
					'title' => $post->post_title,
					'pre_next_post' => $pre_next_post,
					'sidebar' => $sidebar));
		}
		if (strlen($error) > 0) {
			return Redirect::route('error', array($error));
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

					$START = microtime(1);

					$htmlFilter = new HtmlFilter;
					$htmlFilter->addValues('a', 'href', array('#'));
					$post_content = $htmlFilter->filter( Input::get('post_content') );

					$TOTAL = 1000* (microtime(1) - $START);
					Log::info("Filter post time:{$TOTAL}");

//					echo PHP_EOL, (microtime(1) - $start);

//					$post_content = htmlspecialchars();
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
		$user = User::get_user_from_session();
		$error = '';
		if(is_null($user)){
			$error = '未登录';
		}else{
			$username = $user->user_login;
			$post_model = new Post;
			$posts = $post_model->get_user_posts($user->user_id, Constant::$ADMIN_PAGESIZE);
			$resp = View::make('posts/post_admin',
				array('title' => '博文管理',
					'username' => $username,
					'posts' => $posts,
					'menu' => 'post',
					'nav' => Constant::$NAV_ADMIN)
			);
		}
		if (strlen($error) > 0) {
			return Redirect::route('error', array($error)); //,array($post_id));
		} else {
			return $resp;
		}
	}

	/**
	 * 搜索
	 * @return mixed
	 */
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


}
