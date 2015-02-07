<?php

class Post extends BaseModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
	protected $primaryKey = 'post_id';
	protected static $MODEL_KEY = "%s#%s";//primarykey#{primarykey}


	public $err;
//	protected $primaryKey = 'post_id';
//	public static $MODELKEY = "post_id#%s";



//	public static $TIMESORT_PKSET_KEY = "POST_TS_PK#SET";


	protected static $TS_COL = 'post_date';
	protected static $CONDITION_COL = array(
		0=> "post_status ='publish'"
	);

	// public static $MODELKEY = "post_id#%s";
	public $timestamps = false;
	// protected $softDelete = true;
	/**
	 * ------------------------Relation functions------------------------------------------
	 */
	/**
	 * term-post 多对多
	 * @return mixed
	 */
	public function terms(){
		return $this->belongsToMany('Term', 'term_relationships', 'object_id', 'term_id');
	}

	/**
	 * 一对多
	 * @return mixed
	 */
	public function user(){
//		$this->('Post');
		return $this->belongsTo('User','post_author','ID');
	}

	/**
	 * 一对多
	 * @return mixed
	 */
	public function comments() {
		return $this->hasMany('Comment', 'comment_post_ID');
	}

	/**
	 * 一对一
	 * @return mixed
	 */
	public function postimage(){
		return $this->hasOne('PostImage','iid','post_cover_img');
	}

	/**
	 * 一对一
	 * @return mixed
	 */
	public function postauthor(){
		return $this->hasOne('User','ID','post_author');
	}



	/**
	 * ------------------------校验函数 functions--------------------------------------------------
	 */
	/**
	 * pid 是否存在
	 * @param $pid
	 * @return bool
	 */
	public static function chk_pid_exist($pid){
		$post = Post::find($pid);
		if(is_null($post)){
			return false;
		}
		return true;
	}


	/**
	 * ---------------------------------查询函数------------------------------------------------
	 */


	public function get_posts_onepage_with_meta($page,$pagesize,$redis = null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		$posts = $this->get_posts_onepage($page,$pagesize,$redis);
		if(!is_null($posts)){
			self::add_meta($posts,$redis);
		}
		return $posts;
	}


	/**
	 * [多条] 首页用，
	 * @param $page 页数
	 * @param $pagesize 页码
	 * @param null $redis
	 */
	public function get_posts_onepage($page,$pagesize,$redis=null){
		$err = '';
		$res = null;
		if($page <0 ){
			$this->err = '请求页数范围错误';
		}else{
			if(is_null($redis)){
				$redis = LRedis::connection();
			}
//			$total = $this->get_ts_pk_set_size($redis);
			$total_db = $this->get_size_db();//取得 size 实际值
			if($total_db == 0){
				$res = null;//库内无post
				$this->err = "库内无post";
			}else{
				if( $page * Constant::$PAGESIZE > $total_db ){//page 有效性检查完毕，可取ts_set值
					$res = null;
					$this->err = '请求页数超出范围';
				}else{//page正常，且库内有post
					$pk_set = $this->get_ts_pk_set($page,Constant::$PAGESIZE,$redis);
					if(!is_null($pk_set) && count($pk_set >0 )){
						$res = $this->get_post_from_pkset($pk_set);
					}
					if(!is_null($res)&& count($res)>0){
						//按时间排序 从大到小
						usort($res , function($p1, $p2) {
							$p1_date = strtotime($p1->post_date);
							$p2_date = strtotime($p2->post_date);
							if($p1_date == $p2_date){
								return 0;
							} else {
								return $p1_date >$p2_date ?-1:1;
							}
						});
					}else{
						$res = $this->get_posts_onepage_db($page,Constant::$PAGESIZE);
						if(is_null($res)){
							$this->err = '从库中取posts 值为空或数据库错误';
						}
					}
				}
			}
		}
		return $res;
	}

	/**
	 * [多条]
	 * @param $page
	 * @param $pagesize
	 * @return mixed
	 */
	public function get_posts_onepage_db($page,$pagesize){
		$res = DB::table( $this->table )
			->skip( ($page-1)*$pagesize )
			->take($pagesize)->orderBy(self::$TS_COL)->get();
		return $res;
	}

	/**
	 * [多条] 从pkset获取post
	 * @param $pks
	 * @param null $redis
	 * @return array
	 */
	public function get_post_from_pkset($pks,$redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		$res = array();
		if( is_array($pks) && count($pks)>0){
			foreach($pks as $pk){
//				$post = $this->get_one_post_nocontent($pk,$redis);
				$post = $this->get_model($pk,$redis);
				if(!is_null($post)){
					array_push($res,$post);
				}else{
					$errmsg = "获取到空post";
					$this->error = $errmsg;
					$method = __METHOD__;
					Log::error("{$method}|MSG:{$errmsg}");
				}
			}
		}
		return $res;
	}


	/**
	 * 单条，
	 * @param $post_id
	 * @param null $redis
	 * @return null
	 *
	public function get_one_post_nocontent($post_id,$redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		$classname = get_class($this);

		$key = strtoupper(sprintf(self::$MODELKEY ,$classname ,$post_id));
		$post_se = $redis->get($key);
		if(!is_null($post_se)){
			$res = unserialize($post_se);
			if(is_null($res)){//cannot be decoded or if the encoded data is deeper than the recursion limit.
				$res = $this->get_one_post_nocontent_db_and_init_cache($post_id,$redis);
			}
		}else{
			$res = $this->get_one_post_nocontent_db_and_init_cache($post_id,$redis);
		}
		return $res;
	}
*/


	/**
	 * 单条
	 * @param $post_id
	 * @param null $redis
	 * @return null
	 *
	public function get_one_post_nocontent_db_and_init_cache($post_id,$redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		try{
			$res = Post::find($post_id);
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}");
			$res = null;
		}
		if( !is_null($res)){
			$res->post_content = null;
			$key = sprintf(self::$MODELKEY,$post_id);
			$redis->set($key,json_encode($res));
		}
		return $res;
	}
	 */

	/**
	 * 附加post category和tag
	 * @param $posts
	 */
	public static function add_meta(&$posts,$redis=null) {
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		foreach ($posts as $post):
			try{
//				$terms = Term::get_terms_by_post($post->post_id);
				$tr = new TermRelationship();
				$terms = $tr->get_post_term( $post->post_id );//Term::get_terms_by_post
				//($post->post_id);
				if (!is_null($terms)) {
					$cat = Term::get_category($terms);
					$tag = Term::get_tag($terms);
					$post->category = is_array($cat) && count($cat) > 0 ? reset($cat) : null;
					$post->post_tag = is_array($tag) && count($tag) > 0 ? $tag : null;
				} else {
					$post->category = null;
					$post->post_tag = null;
				}
//				$img_pk = ;
				$img = new PostImage;
				$img = $img->get_model($post->post_cover_img);
//				$img = $post->postimage;
				$author = new User;
				$author = $author->get_model($post->post_author,$redis );
				if(!is_null($img)){
					$post->post_img_name = $img->name;
				}else{
					$post->post_img_name = null;
				}
				if(!is_null($author)){
					$post->post_author = $author->user_login;
				}else{
					$post->post_author = null;
				}
				$post_model = new Post;
				$cnt = $post_model->get_relate_count("comment",$post->post_id,$redis);
				if($cnt<0){
					$post->comment_count = 0;
				}else{
					$post->comment_count = $cnt;
				}
			}catch(Exception $e){
				//出现异常，全部置空
				foreach ($posts as $post):
					$post->category = null;
					$post->post_tag = null;
				endforeach;
				Log::error("Post Add Meta|Get term error| {$e->getMessage()}");
			}
		endforeach;
	}


	/**
	 *
	 * @param unknown $user_id
	 * @param unknown $pagesize
	 * @return unknown
	 */
	public static function get_posts_by_userid($user_id, $pagesize) {
/*
select users.user_login post_author,posts.ID post_id,post_date,post_content,count(comments.comment_ID) post_comment_count
from posts
left join comments on comments.comment_post_ID = posts.ID
left join users on users.ID = posts.post_author
where  posts.post_author = 1
group by posts.ID
order by posts.post_date desc;
 */
		$posts = DB::table('posts')
			->select('users.user_login as post_author',
				'posts.ID as post_id', 'post_title', 'post_status','post_date', 'post_summary',
				DB::raw('count(comments.comment_ID) as post_comment_count'))
			->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
			->leftJoin('users', 'users.ID', '=', 'posts.post_author')
			->where('posts.post_author', '=', $user_id)
			->groupBy('posts.ID')
			->orderBy('posts.post_date', 'desc')
			->paginate($pagesize);
		// $totla_cnt = DB::table('posts')->where('posts.post_author','=',$user_id)->count();
		// $paginator = Paginator::make($posts,$totla_cnt,$pagesize);
		//
		return $posts;
	}

	/**
	 * @param null $redis
	 *
	public function init_posts($redis=null){
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		try{
			$posts = Post::all();
			if( is_array($posts) && count($posts)>0){
				foreach($posts as $post){
					$key = sprintf(self::$POSTKEY,$post->post_id);
					$set_res = $redis->set($key,json_encode($post));
					if(!$set_res){
						$method = __METHOD__;
						Log::error("{$method}|MSG:设置post缓存失败");
					}
				}
				$res = $posts;
			}else{
				$res = null;
			}
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}");
			$res = false;
		}
	}
	 */





	public function get_post_db($page,$pagesize){
		try{
			$idx = $this->page2index($page,$pagesize);
			$start = $idx['start'];
			$stop = $idx['stop'];

			$res = DB::table('posts')
				->leftJoin('users', 'users.ID', '=', 'posts.post_author')
				->leftJoin('postimages', 'postimages.iid', '=', 'posts.post_cover_img')
				->select('posts.ID as post_id', 'post_title', 'post_content', 'post_date', 'users.user_login as post_author', 'post_summary', 'postimages.filename as post_img_name', DB::raw('count(comments.comment_ID) as comment_count'))
				->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
				->where('post_status','=',Constant::$POST_PUBLISH)
				->orderBy('post_date', 'desc');
		}catch(Exception $e){
			$error_msg = $e->getMessage();
			$method = __METHOD__;
			Log::error("{$method}|MSG:{$error_msg}");
			$res = null;
		}
		return $res;
	}

//	public static function get_post_with_meta_db($pagesize){
//		$posts = self::get_post_db($pagesize);
//		if ($posts) {
//			Post::add_meta($posts);
//		} else {
//			$posts = null;
//		}
//		return $posts;
//	}


	/**
	 * 获取所有posts 评论数，作者，
	 * @param unknown $pagesize
	 * @return NULL
	 */
	public static function get_posts_bak($pagesize,$redis=null) {
		//$posts = Post::paginate($pagesize);

		$posts = DB::table('posts')
			->leftJoin('users', 'users.ID', '=', 'posts.post_author')
			->leftJoin('postimages', 'postimages.iid', '=', 'posts.post_cover_img')
			->select('posts.ID as post_id', 'post_title', 'post_content', 'post_date', 'users.user_login as post_author', 'post_summary', 'postimages.filename as post_img_name', DB::raw('count(comments.comment_ID) as comment_count'))
			->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
			->where('post_status','=',Constant::$POST_PUBLISH)
			->groupBy('posts.ID')
			->orderBy('post_date', 'desc')
			// ->select();
			->paginate($pagesize);
		// $totla_cnt = DB::table('posts')->count();
		// $paginator = Paginator::make($posts,$totla_cnt,$pagesize);
		return $posts;
	}



	/**
	 * get posts by term_id
	 * @param unknown $term_id
	 * @param unknown $pagesize
	 * @return NULL
	 */
	public static function get_posts_by_term($term_id, $pagesize) {
		/*
		select posts.ID post_id,users.user_login post_author,post_date,post_content,users.ID as post_author_id,
		post_title,count(comments.comment_ID) comment_count
		from posts
		join term_relationships on posts.ID=term_relationships.object_id
		join users on users.ID=posts.post_author
		left join comments on comments.comment_post_ID=posts.ID
		where term_relationships.term_taxonomy_id=1 group by posts.ID;
		 */
		$posts = null;
		if ($pagesize > 0) {
			$posts = DB::table('posts')
				->select('posts.ID as post_id', 'post_title', 'post_content', 'post_date', 'users.user_login as post_author', 'postimages.filename as post_img_name', 'post_summary'
					, DB::raw('count(comments.comment_ID) as comment_count'))
				->leftJoin('users', 'users.ID', '=', 'posts.post_author')
				->leftJoin('term_relationships', 'posts.ID', '=', 'term_relationships.object_id')
				->leftJoin('postimages', 'postimages.iid', '=', 'posts.post_cover_img')
				->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
				->where('term_relationships.term_taxonomy_id', '=', $term_id)
				->groupBy('posts.ID')
				->paginate($pagesize);
		}
		return $posts;
	}

	/**
	 * get posts by year & month
	 * @param unknown $year
	 * @param unknown $month
	 * @param unknown $pagesize
	 */
	public static function get_post_by_date($date, $pagesize) {
		/*
		select posts.ID post_id,users.user_login post_author,users.ID as post_author_id,DATE_FORMAT(posts.post_date,'%Y-%m'),
		post_content,post_title,count(comments.comment_ID) comment_count
		from posts
		join users on users.ID = posts.post_author
		left join comments on comments.comment_post_ID=posts.ID
		where DATE_FORMAT(posts.post_date,'%Y-%m') = '2014-06' group by posts.ID;
		 */
		//$search_date = $year.'-'.$month;
		$posts = DB::table('posts')
			->select('posts.ID as post_id', 'users.user_login as post_author', 'users.ID as post_author_id', 'post_date', 'postimages.filename as post_img_name', 'post_summary',
				'post_content', 'post_title', DB::raw('count(comments.comment_ID) as comment_count'))
			->leftJoin('users', 'users.ID', '=', 'posts.post_author')
			->leftJoin('postimages', 'postimages.iid', '=', 'posts.post_cover_img')
			->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
			->whereRaw("DATE_FORMAT( posts.post_date,'%Y-%m')='" . $date . "'")
			->groupBy('posts.ID')
			->paginate($pagesize);
		return $posts;
	}

	/*
	 * get posts by user id
	 * @param unknown $pagesize
	 *
	public static function getPostByUser($user_id,$pagesize){
	/**
	select posts.ID post_id,users.user_login post_author,posts.post_date,
	post_content,post_title,users.ID post_author_id,count(comments.comment_ID) comment_count
	from posts
	join users on users.ID = posts.post_author
	left join comments on comments.comment_post_ID=posts.ID
	where post_author=1 group by posts.ID;
	 *
	$posts = DB::table('posts')
	->select('posts.ID as post_id','users.user_login as post_author','users.ID as post_author_id','post_date',
	'post_content','post_title',DB::raw('count(comments.comment_ID) as comment_count'))
	->join('users','users.ID','=','posts.post_author')
	->leftJoin('comments','comments.comment_post_ID','=','posts.ID')
	->where('post_author',$user_id)
	->groupBy('posts.ID')
	->paginate($pagesize);
	return $posts;
	}
	 */

	public static function getPostCommentStat($post_id) {
		$comm_stat = DB::table('posts')
			->join('comments', 'posts.ID', '=', 'comments.comment_post_ID')
			->where('posts.ID', '=', $post_id)
			->count();
		return $comm_stat;
	}


	public static function get_pre_next_post($post_id) {
		/*
		Pre:
		[use date]
		select ID post_id,post_title from posts where post_date <=
		(select post_date from posts where ID=6)  and ID !=6
		order by post_date desc,ID  limit 1;
		[use ID,ID auto increasement]
		select ID post_id,post_title from posts where ID <6
		order by ID desc limit 1;
		Next:
		[use date]
		select ID post_id,post_title from posts where post_date >
		(select post_date from posts where ID=5)  and ID !=5
		order by post_date limit 1;
		[use ID,ID auto increasement]
		select ID post_id,post_title from posts where ID >6
		order by ID  limit 1;
		 */
		$res = array();
/*		$pre_post = DB::table('posts')
->select('ID as post_id','post_title')
->where('post_date','<',
function($query) use ( $post_id ) {
$query->select('post_date')
->from('posts')
->where('ID','=',$post_id);
})
->where('ID','!=',$post_id)
->orderBy('post_date','desc')
->take(1)->get();
 */
		$pre_post = DB::table('posts')
			->select('ID as post_id', 'post_title')
			->where('ID', '<', $post_id)
			->orderBy('ID', 'desc')
			->take(1)->get();
		$res['pre_post'] = $pre_post;

		$queries = DB::getQueryLog();
		$last_query = end($queries);
		Log::info('PRE POST SQL:' . $last_query['query']);
/*
$next_post = DB::table('posts')
->select('ID as post_id','post_title')
->where('post_date','>',
function($query) use ( $post_id )  {
$query->select('post_date')
->from('posts')
->where('ID','=',$post_id);
})
->where('ID','!=',$post_id)
->orderBy('post_date')
->take(1)->get();
 */
		$next_post = DB::table('posts')
			->select('ID as post_id', 'post_title')
			->where('ID', '>', $post_id)
			->orderBy('ID')
			->take(1)->get();
		$res['next_post'] = $next_post;
		$queries = DB::getQueryLog();
		$last_query = end($queries);
		Log::info('NXT POST SQL:' . $last_query['query']);
		return $res;
	}



// 	public function comments()
	// 	{
	// 		return $this->hasMany('Comment');
	// 	}

	/**
	 * get latest count posts
	 * @param unknown $count
	 * @return unknown
	 */
	public static function getNewstPost($count) {
		//select ID,post_title from posts order by post_date limit 5;
		$posts = DB::table('posts')->select('ID', 'post_title')->orderBy('post_date')->take($count)->get();
		return $posts;
	}

	/**
	 * 获取最新博客5篇
	 * @param $count
	 * @param null $redis
	 * @return array
	 */
	public function get_latest_count_post($count,$redis=null) {
		if(is_null($redis)){
			$redis = LRedis::connection();
		}
		$pks = $this->get_ts_pk_set(1,$count,$redis);
		if(is_array($pks)&&count($pks)>0){
			$posts = $this->get_post_from_pkset($pks,$redis);
		}
		return $posts;
	}




	public static function getPostsStat() {
		/*
		select DATE_FORMAT(post_date,'%Y年%m月') post_date,DATE_FORMAT(post_date,'%Y-%m') post_date_url,count(*) post_count
		from posts group by post_date order by post_date desc;
		 */

		$post_stats = DB::table('posts')
			->select(DB::raw("DATE_FORMAT(post_date,'%Y年%m月') post_date,DATE_FORMAT(post_date,'%Y-%m') post_date_url,count(*) post_count"))
			->groupBy('post_date_url')->orderBy('post_date', 'desc')->get();
		return $post_stats;
	}

	/**
	 * get one post by post ID
	 * @param unknown $post_id
	 * @return NULL|unknown
	 */
	public static function get_post_by_id($post_id) {
		/*
		select posts.ID as post_id,post_title,post_content,post_date,users.user_login as post_author,
		posts.post_author as post_author_id
		from posts
		left join users on users.ID= posts.post_author
		left join postimages on postimages.iid = posts.post_cover_img
		where posts.ID=31;
		 */
		$post = DB::table('posts')
			->select('posts.ID as post_id', 'post_title', 'post_content', 'post_date', 'users.user_login as post_author', 'posts.post_author as post_author_id', 'post_cover_img', 'postimages.filename as post_img_name', 'post_summary', DB::raw('count(comments.comment_ID) as comment_count'))
			->leftJoin('comments', 'comments.comment_post_ID', '=', 'posts.ID')
			->leftJoin('users', 'users.ID', '=', 'posts.post_author')
			->leftJoin('postimages', 'postimages.iid', '=', 'posts.post_cover_img')
			->where('posts.ID', '=', $post_id)
			->get();

		// $queries = DB::getQueryLog();
		// $last_query = end($queries);
		// Log::info('SINGLE POST:' . $last_query['query']);

		if (!is_null($post) && count($post) > 0) {
			return $post;
		} else {
			return null;
		}
	}



	/**
	 * 全文检索搜索函数 search function
	 * @param $search_text
	 * @param $page
	 * @param $per_page
	 * @return array
	 */
	public static function search_name_content($search_text, $page, $per_page) {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); // or die('could not create socket');
		$posts = null;
		$err = '';
		if ($socket) {
			try {
				$connect = socket_connect($socket, Constant::$SEARCH_SERVER_IP, Constant::$SEARCH_SERVER_PORT);
				//向服务端发送数据
				$search_str = sprintf(Constant::$SEARCH_FUNC, $search_text, $page, $per_page);
				socket_write($socket, $search_str);
				// socket_write($socket,  . '#' . $search_text . ',' . $page . ',' . $per_page . "\n");
				//接受服务端返回数据
				$json = socket_read($socket, 1024, PHP_NORMAL_READ);
				$res = json_decode($json);
				if ($res->status === "true") {
					$search_res = $res->data;
					$search_res_arr = explode('#', $search_res);
					$cnt = count($search_res_arr);
					$total = 0;
					if ($cnt == 2) {
						$total = $search_res_arr[0];
						$ids = explode(',', $search_res_arr[1]);
						$posts = array();
						if (count($ids) > 0) {
							foreach ($ids as $id) {
								$tmp_post = self::get_post_by_id($id);
								$posts = array_merge($posts, $tmp_post);
							}
							self::add_meta($posts);
							// print_r($posts);
						}
					} else if ($cnt == 1) {
						$posts = null;
					} else {
						$err = '查询错误';
					}
				} else {
					$err = $res->data;
				}
				//关闭
				socket_close($socket);
			} catch (ErrorException $e) {
				$err = $e->getMessage();
			}
		} else {
			$err = "无法连接到搜索服务器";
		}
		if (strlen($err) > 0) {
			return array(false, $err);
		} else {
			return array(true, $posts, $total);
		}
	}

	/**
	 * 添加post，增加此博文的全文索引
	 * @param $post_id
	 */
	public static function add_search_index($post_id) {

	}

	/**
	 * 更新所有全文索引，合并碎片，定时执行
	 */
	public static function rebuild_search_index() {

	}

	/**
	 * ---------------------------------新建/删除/修改函数---------------------------------------------
	 */
	/**
	 * 创建post
	 */
	public static function create_post($post_id,$user_id, $post_title, $post_content,
$post_cover_img_id,
$post_summary, $post_status) {
		//$post = new Post;
		date_default_timezone_set("Europe/London");
		$post_date_gmt = date('Y-m-d H:i:s', time());
		date_default_timezone_set("Asia/Shanghai");
		$post_date = date('Y-m-d H:i:s', time());
		DB::table('posts')->insert(
			array(
				'ID'=>$post_id,
				'post_author' => $user_id,
				'post_title' => $post_title,
				'post_content' => $post_content,
				'post_date' => $post_date,
				'post_date_gmt' => $post_date_gmt,
				'post_modified' => $post_date,
				'post_modified_gmt' => $post_date_gmt,
				'post_status' => $post_status,
				'post_summary' => $post_summary,
				'post_cover_img' => $post_cover_img_id,
			)
		);
//		$get_last_post_id_sql = "SELECT LAST_INSERT_ID() ID";
//		$post_id = DB::select($get_last_post_id_sql)[0]->ID;
//		Log::info('CreatePost:' . $post_id);
		return $post_id;
	}


//	public static function add_post()

	/**
	 * 创建post
	 */
	public static function update_post($post_id, $user_id, $post_title, $post_content, $post_cover_img_id, $post_summary) {
		//$post = new Post;
		date_default_timezone_set("Europe/London");
		$post_date_gmt = date('Y-m-d H:i:s', time());
		date_default_timezone_set("Asia/Shanghai");
		$post_date = date('Y-m-d H:i:s', time());

		DB::table('posts')
			->where('ID', $post_id)
			->update(
				array(
					'post_author' => $user_id,
					'post_title' => $post_title,
					'post_content' => $post_content,
					'post_modified' => $post_date,
					'post_modified_gmt' => $post_date_gmt,
					'post_summary' => $post_summary,
					'post_cover_img' => $post_cover_img_id,
				)
			);
		//$get_last_post_id_sql = "SELECT LAST_INSERT_ID() ID";
		//$post_id = DB::select($get_last_post_id_sql)[0]->ID;
		Log::info('Update Post:' . $post_id);
		//return $post_id;
	}

	/**
	 * 删除相关评论，标签
	 */
	public static function delete_all($post_id) {
		DB::transaction(function () use ($post_id) {
			//$post_id = Input::get('post_id');
			$post = Post::find($post_id);

			$post->delete();

			//delete terms relationship
			DB::table('term_relationships')->where('object_id', '=', $post_id)->delete();
			//delete comments
			DB::table('comments')->where('comment_post_ID', '=', $post_id)->delete();
		});
	}

	/** --------------------------- Tool funcions --------------------------- **/

	/**
	 * 首页内容截取，获取适合长度
	 * @param unknown $content
	 * @param unknown $length
	 * @return Ambigous <number, unknown>
	 */
	public static function get_adjust_length($content, $length) {
		preg_match_all("/(<(\/)*([\w]+)[^>]*>)/", $content, $labels, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		$length_backup = $length;
		$res = 0;
		foreach ($labels as $label) {
			$label_length = mb_strlen($label[0][0], Constant::$UTF_8); //, Constant::$UTF_8);
			$label_idx = $label[0][1];
			if ($label_idx + $label_length <= $length) {
//before label
				$res = $length;
			} else if ($label_idx < $length && $length < $label[0][1] + mb_strlen($label[0][0], Constant::$UTF_8)) {
//in the middle of lable
				$res = $label[0][1] + mb_strlen($label[0][0], Constant::$UTF_8); //正好在中间的标签，删除
				break;
			} else {
//after label
				$res = $length_backup;
				break;
			}
		}
		return $res;
	}

	/**
	 * 是否开始标签
	 * @param unknown $label
	 */
	public static function is_start_label($label) {
		preg_match("(<([\w]+)[^>]*>)", $label, $matches);
		$res = false;
		if (count($matches) > 0) {
			$res = true;
		}
		return $res;
	}

	/**
	 * 首页内容截取，截取最后一个标签
	 * @param unknown $content
	 * @return string
	 */
	public static function get_adjust_content($content) {
		preg_match_all("/(<(\/)*([\w]+)[^>]*>)/", $content, $labels, PREG_SET_ORDER);
		$add_labels = array();
		$stack = array();
		foreach ($labels as $label) {
			$label_type = $label[3];
			$label_all = $label[0];
			if (Post::is_start_label($label_all) > 0) {
				array_push($stack, $label_type);
			} else {
				$front_label_type = array_pop($stack);
				if (!is_null($front_label_type)) {
					if (strcmp($front_label_type, $label_type) != 0) {
						array_push($stack, $label_type);
					}
				}
			}
		}
		while (count($stack) > 0) {
			$content = $content . '</' . array_pop($stack) . '>';
		}
		$content = $content; //."...";
		return $content;
	}

	/**
	 * 首页内容截取
	 * @param unknown $content
	 * @param unknown $length
	 * @return unknown
	 */
	public static function get_adjust_post($content, $length) {
		Log::info('Cut to:' . $length);
		Log::info('Before cut:' . strlen($content));
		if (mb_strlen($content, Constant::$UTF_8) <= $length) {
			return $content;
		}
		$length = Post::get_adjust_length($content, $length);
		Log::info('Adjust Lenght:' . $length);
		//$content  = substr($content,0,$length);
		$content = mb_substr($content, 0, $length, Constant::$UTF_8);
		Log::info('Af cut:' . $content);
		//$content = substr($content,0,$length);//, Constant::$UTF_8 );
		$content = Post::get_adjust_content($content);
		Log::info('Af add:' . $content);
		return $content;
	}



	/**
	 * 获取纯文字摘要
	 * @param unknown $content
	 * @param unknown $length
	 * @return string
	 */
	public static function get_summary($content, $length) {
		$res = "";
		$short_content = self::remove_html_label($content);
// 		if( mb_strlen($content,'utf-8')<$length ){
		// 			$res = $content;
		// 		}else{

		if (mb_strlen($short_content, 'utf-8') < $length) {
			$res = $short_content;
		} else {
			$res = mb_substr($short_content, 0, $length, Constant::$UTF_8); // );
		}
		return $res . '...';
	}

	/**
	 * 删除所有html标签
	 * @param unknown $content
	 * @return unknown
	 */
	public static function remove_html_label($content) {
		$res = preg_replace("/<(.[^>]*)>/", "", $content);
		return $res;
	}




	public static function create_post_term($post_id, $termid_arr) {
		$insert_arr = array();
		foreach ($termid_arr as $term_id) {
			array_push($insert_arr, array('object_id' => $post_id, 'term_taxonomy_id' => $term_id));
		}
		DB::table('term_relationships')->insert($insert_arr);
	}

}
