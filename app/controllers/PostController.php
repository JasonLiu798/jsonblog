<?php
class PostController extends BaseController {
	
	
	public function index(){
		Log::info('IndexAction');
		//get posts
		$posts = Post::get_posts( Constant::$PAGESIZE );
		$posts = Post::add_meta($posts);
		$sidebar = PostController::get_sidebar();
		$username = User::get_name_from_session( Session::get('user') );
		$view = View::make('index',
				array('title'=>'AsyncBlog','username'=>$username,
						'term4title'=>null,'date4title'=>null,//'user4title'=>null,
						'posts'=>$posts,
						'sidebar'=>$sidebar));
// 		if(count($posts[0]->post_tag)>0){
// 			foreach($posts[0]->post_tag as $term)
// 				Log::info('post term:'.$term->name.',TAX:'.$term->taxonomy);
// 		}
		return $view;
	}
	
	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function term_achive($term_id){
		$msg = '';
		if(!preg_match(Constant::$DIGIT,$term_id)){
			$msg = '分类编号格式错误';
		}else{
			$posts = Post::get_posts_by_term($term_id,Constant::$PAGESIZE);
			$posts = Post::add_meta($posts);
// 			$queries = DB::getQueryLog();
// 			$last_query = end($queries);
// 			Log::info('post date:'.$last_query['query']);
			$term4title = Term::get_name_taxonomy($term_id);
			if( is_null($term4title[0]->name ) ){
				$msg = '分类/标签不存在';
			}
		}
		if(strlen($msg)>0){
			Redirect::action('ErrorController@show', array($msg));
			//App::abort(404);
		}else{
			$username = User::get_name_from_session( Session::get('user') );
			$sidebar = PostController::get_sidebar();
			$view = View::make('index',
					array('title'=>$term4title[0]->name.'|Async Blog','username'=>$username,
							'date4title'=>null,
							'term4title'=>$term4title,//'user4title'=>null,
							'posts'=>$posts,
							'sidebar'=>$sidebar));
			return $view;
		}
	}
	
	/**
	 * get posts by date
	 * @param unknown $date
	 * @return unknown
	 */
	public function date_achive($date){
		$msg='';
		if(!preg_match(Constant::$REG_YEAR_MONTH,$date,$m)){
			$msg = '日期格式错误';
		}else{
			$date_arr = explode("-",$date);
			$date4title['title'] = $date_arr[0].'年'.$date_arr[1].'月';
			$date4title['link'] = $date;
			$posts = Post::get_post_by_date($date,Constant::$PAGESIZE);
			$posts = Post::add_meta($posts);
// 			$queries = DB::getQueryLog();
// 			$last_query = end($queries);
// 			Log::info('post date:'.$last_query['query']);
		}
		if(strlen($msg)>0){
			return Redirect::route('error', array($msg));
		}else{
			$username = User::get_name_from_session( Session::get('user') );
			$sidebar = PostController::get_sidebar();
			$view = View::make('index',
					array('title'=>$date.'|Async Blog','username'=>$username,
							'date4title'=>$date4title,
							'term4title'=>null,//'user4title'=>null,
							'posts'=>$posts,
							'sidebar'=>$sidebar));
			return $view;
		}
	}
	
	/**
	 * get sidebar infos
	 * @return stdClass
	 */
	public static function get_sidebar(){
		$res = new stdClass;
		//get terms
		$term_stats = Term::getTermsAndStat();
		$res->cat_stats =Term::get_category($term_stats);
		$res->tag_stats =Term::get_tag($term_stats);
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
	public function single($post_id){
//Log::info("Single,post_id:".$post_id);
		$post = Post::get_post_by_id($post_id);
		$msg = '';
		if(empty($post)){
			$msg = '博文不存在';
		}else{
			$post = Post::add_meta($post);
			$post = $post[0];
			$post->cover_img_url = PostImage::get_img_url_by_name($post->post_img_name);
			$comments = Comment::getCommentsByPostID($post_id);
			$pre_next_post = Post::get_pre_next_post( $post_id );
		}
		if(strlen($msg)>0){
			return Redirect::route('error', array($msg));
		}else{
			$username = User::get_name_from_session( Session::get('user') );
			$sidebar = PostController::get_sidebar();
			$view = View::make('posts/single_comm_r',
					array('post'=>$post,'comments'=>$comments,'username'=>$username,
					'title'=>$post->post_title,//.'|'.Lang::get('posts.TITLE'),
					'pre_next_post'=>$pre_next_post,
					'sidebar'=>$sidebar));
			return $view;
		}
	}
	
	public function create($param='page'){
		$sess_user_json = Session::get('user','default');
		$user = json_decode($sess_user_json);
		$msg = '';
		if( is_null( $user ) ){
			$msg = "未登录";
		}else{
			if($param === 'page'){
				$category = Term::get_all_categories();
				$term = new Term();
				$category_tree = $term->format_category2tree($category);
				$top5post_tag = Term::get_top5_post_tag();
				$sidebar = self::get_sidebar();
				$view = View::make('posts/create_post',array(
						'title'=>Lang::get('post.TITLE'),'username'=>$user->username,
						'category'=>$category_tree,
						'post_tag'=>$top5post_tag,
						'sidebar'=>$sidebar));
			}else if($param === 'draft_page'){
				
			}else if($param === 'save'){
				$post_title = Input::get('post_title');//urldecode(urldecode());
				$post_content = Input::get('post_content');//urldecode(urldecode(Input::get('post_content')));
				$set_cover = (string)Input::get('set_cover');
				$cover_img_id = Input::get('cover_img_id');
Log::info('Post Save:set_cover'.$set_cover.' ,iid:'.$cover_img_id);
				if( $set_cover === 'true' ){
					//有封面图片，只显示 标题+图片
					$post_cover_img = (int)$cover_img_id;//url().Constant::$UPLOAD_IMG_DIR.$post_cover_img_name;
				}else{
					//无封面图片，显示 标题+summary
					$post_cover_img = 0;
				}
				$post_summary = Post::get_summary($post_content,Constant::$POST_INDEX_CUT_SIZE);
				
				//$post_cover_img_name = Input::get('cover_img_name');
Log::info('CREATE POST:'.$post_title.','.$post_cover_img.','.$post_summary );
				$post_id = Post::create_post($user->uid,$post_title,$post_content,$post_cover_img,$post_summary);
				if($post_id<0){
					$msg = '创建博文失败!';
				}else{
					$category_id = Input::get('category');
					$post_tag_ids = Input::get('post_tag_ids');
					
					if( strlen($post_tag_ids) > 0 ){
						$termid_arr = explode(',',$post_tag_ids);
						array_push( $termid_arr, $category_id);
					}else{
						$termid_arr = array();
						array_push( $termid_arr, $category_id);
					}
					Post::create_post_term( $post_id, $termid_arr );						
Log::info("POST TAGS:".$post_tag_ids.",CAT:".$category_id);
					$view = Redirect::route('singlepost',array($post_id));
				}
			}else{//App::abort(404);
				$msg='未定义操作！';
			}
		}
		if(strlen($msg)>0){
			return Redirect::route('error', array($msg));
		}else{
			return $view;
		}
	}
	
	public function pre_update( $post_id ){
		$sess_user_json = Session::get('user','default');
		$user = json_decode($sess_user_json);
		$msg = '';
		if( is_null( $user ) ){
			$msg = "未登录";
		}else{
			$post = Post::get_post_by_id( $post_id);
			if(empty($post)){
				$msg = "博文不存在";
			}else{
				$post = Post::add_meta($post);
				
				$post = $post[0];
				$post_tag_id = '';
				$i=0;
				$len = count($post->post_tag);
				foreach($post->post_tag as $tag){
					if($i==0){
						$post_tag_id = $tag->term_id;
					}else{//else if($i== $len-1){
						$post_tag_id =$post_tag_id . ','.$tag->term_id;
					}
					$i++;
				}
				$post->post_tag_id = $post_tag_id;
				$post->cover_img_url = PostImage::get_img_url_by_name($post->post_img_name);
				$category = Term::get_all_categories();
				$term = new Term();
				$category_tree = $term->format_category2tree($category);
				$top5post_tag = Term::get_top5_post_tag();
				$sidebar = self::get_sidebar();
				$view = View::make('posts/update_post',array(
						'title'=>'修改博文','username'=>$user->username,
						'post'=>$post,
						'category'=>$category_tree,
						'post_tag'=>$top5post_tag,
						'sidebar'=>$sidebar));
			}
		}
		if(strlen($msg)>0){
			return Redirect::route('error', array($msg));
		}else{
			return $view;
		}
	}
	
	public function update(){
		$sess_user_json = Session::get('user','default');
		$user = json_decode($sess_user_json);
		$msg = '';
		if( is_null( $user ) ){
			$msg = "未登录";
		}else{
			
				$category = Term::get_all_categories();
				$term = new Term();
				$category_tree = $term->format_category2tree($category);
				$top5post_tag = Term::get_top5_post_tag();
				$sidebar = self::get_sidebar();
				$view = View::make('posts/create_post',array(
						'title'=>Lang::get('post.TITLE'),'username'=>$user->username,
						'category'=>$category_tree,
						'post_tag'=>$top5post_tag,
						'sidebar'=>$sidebar));
			
		}
		if(strlen($msg)>0){
			return Redirect::route('error', array($msg));
		}else{
			return $view;
		}
	}
	
	
	/**
	 * delete post relate comments,terms,post_image
	 * @param unknown $post_id
	 */
	public function delete_all($post_id){
		Post::delete_all($post_id);
		return Redirect::action('PostController@admin');
	}
	/**
	 * 
	 * @param unknown $post_id
	 */
	public function delete_post($post_id){
		Post::delete_all($post_id);
		return Redirect::action('PostController@admin');
	}
	
	
	/**
	 * 博文管理
	 * @return unknown
	 */
	public function admin(){
		$sess_user = Session::get('user');
		if(is_null($sess_user) ){
			return Redirect::action('PostController@index');
		}
		$username = User::get_name_from_session( $sess_user);
		$user_id =  User::get_userid_from_session( $sess_user );
		
		$posts = Post::get_posts_by_userid( $user_id, Constant::$ADMIN_PAGESIZE);
		if(count($posts)<=0){
			$posts = null;
		}else{
			$posts = Post::add_meta( $posts);
		}
		$view = View::make('posts/post_admin',
				array('title'=>'博文管理','username'=>$username, 'posts'=>$posts,
				));
		return $view;
	}
	
	
}
