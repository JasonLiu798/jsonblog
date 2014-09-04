<?php

class Post extends Eloquent  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
	protected $primaryKey = 'ID';
	
	public function get()
	{
		return $this->hasMany('Comment');
	}
	
	public static function getPosts($pagesize){
		$posts = null;
		if($pagesize>0){
			$posts = DB::table('posts')
				->join('users','users.ID','=','posts.post_author')
				->select('posts.ID as post_id', 'post_title','post_content','post_date','users.user_login as post_author')
				->paginate($pagesize);
		}
		return $posts;
	}
	
	/**
	 * get posts by term_id
	 * @param unknown $term_id
	 * @param unknown $pagesize
	 * @return NULL
	 */
	public static function getPostsByTerm($term_id,$pagesize){
		/**
			select posts.ID post_id,users.user_login post_author,post_date,post_content,post_title
			from posts 
			join term_relationships on posts.ID=term_relationships.object_id
			join users on users.ID=posts.post_author
			where term_relationships.term_taxonomy_id=1;
		 */
		$posts = null;
		if($pagesize>0){
			$posts = DB::table('posts')
			->select('posts.ID as post_id', 'post_title','post_content','post_date','users.user_login as post_author')
			->join('users','users.ID','=','posts.post_author')
			->join('term_relationships','posts.ID','=','term_relationships.object_id')
			->where('term_relationships.term_taxonomy_id','=',$term_id)
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
	public static function getPostByDate($date,$pagesize){
		/**
		  select posts.ID post_id,users.user_login post_author,DATE_FORMAT(posts.post_date,'%Y-%m'),
		  post_content,post_title
		  from posts 
		  join users on users.ID = posts.post_author 
		  where DATE_FORMAT(posts.post_date,'%Y-%m') = '2014-06';
		 */
		//$search_date = $year.'-'.$month;
		$posts = DB::table('posts')
			->select('posts.ID as post_id','users.user_login as post_author','post_date',
					'post_content','post_title')
			->leftJoin('users','users.ID','=','posts.post_author')
			//->where("DATE_FORMAT( posts.post_date,'%Y-%m')",'=',$date)
			->whereRaw("DATE_FORMAT( posts.post_date,'%Y-%m')='".$date."'")
			->paginate($pagesize);
		return $posts;
	}
	
	/**
	 * get posts by user id
	 * @param unknown $pagesize
	 */
	public static function getPostByUser($user_id,$pagesize){
		/**
		 select posts.ID post_id,users.user_login post_author,posts.post_date,
		 post_content,post_title
		 from posts
		 join users on users.ID = posts.post_author
		 where post_author=1;
		 */
		$posts = DB::table('posts')
			->select('posts.ID as post_id','users.user_login as post_author','post_date',
				'post_content','post_title')
			->join('users','users.ID','=','posts.post_author')
			->where('post_author',$user_id)
			->paginate($pagesize);
		return $posts;
	}
	
	//public static function getNextPost($post_id)
	
	public static function getPreNextPost($post_id){
		/*
		Pre:(include equale)
		select ID post_id,post_title from posts where post_date <= 
		(select post_date from posts where ID=5)  and ID !=5 
		order by post_date desc limit 1;
		Next:
		select ID post_id,post_title from posts where post_date >
		(select post_date from posts where ID=5)  and ID !=5 
		order by post_date limit 1; 
		*/
		$res = array();
		$pre_post = DB::table('posts')
			->select('ID as post_id','post_title')
			->where('post_date','<=',
				function($query) use ( $post_id ) {
	                $query->select('post_date')
	                      ->from('posts')
	                      ->where('ID','=',$post_id);
	            })
			->orderBy('post_date','desc')
	        ->take(1)->get();
	    $res['pre_post']=	$pre_post;
	    
$queries = DB::getQueryLog();
$last_query = end($queries);
Log::info('PRE POST SQL:'.$last_query['query']);

	    $next_post = DB::table('posts')
	    	->select('ID as post_id','post_title')
	    	->where('post_date','>',
	    		function($query) use ( $post_id )  {
	    			$query->select('post_date')
	    			->from('posts')
	    			->where('ID','=',$post_id);
	    		})
	    	->orderBy('post_date')
	    	->take(1)->get();
		$res['next_post']=	$next_post;
		return $res;
	}
	
	public static function postAddTerm($posts){
		foreach($posts as $post):
			$terms = Term::getTermsByPostID($post->post_id);

			$cat = Term::getCategory($terms);
Log::info('post'.$post->post_id.' cat:'.$cat[0]->term_id);			
			$tag = Term::getTag($terms);
			$post->category = !empty($cat)?$cat:null;
			$post->post_tag = !empty($tag)?$tag:null;
			$post->post_content = Post::get_adjust_post($post->post_content,200);
		endforeach;
		return $posts;
	} 
	
	
	/**
	 * get latest count posts
	 * @param unknown $count
	 * @return unknown
	 */
	public static function getNewstPost($count){
		//select ID,post_title from posts order by post_date limit 5;
		$posts = DB::table('posts')->select('ID','post_title')->orderBy('post_date')->take($count)->get();
		return $posts;
	}
	
	
	public static function getPostsStat(){
		/*
		  select DATE_FORMAT(post_date,'%Y年%m月') post_date,DATE_FORMAT(post_date,'%Y-%m') post_date_url,count(*) post_count 
		  from posts group by post_date order by post_date desc;
		 */
		
		$post_stats = DB::table('posts')
			->select(DB::raw("DATE_FORMAT(post_date,'%Y年%m月') post_date,DATE_FORMAT(post_date,'%Y-%m') post_date_url,count(*) post_count"))
			->groupBy('post_date_url')->orderBy('post_date','desc')->get();
		return $post_stats;
	}
	
	/**
	 * get one post by post ID
	 * @param unknown $post_id
	 * @return NULL|unknown
	 */
	public static function getPostById($post_id){
		/*
select posts.ID as ID,post_title,post_content,post_date,users.user_login as post_author,
posts.post_author as post_author_id 
from posts 
left join users on users.ID= posts.post_author 
where posts.ID=17;
		 */
		$post = DB::table('posts')
			->select('posts.ID as post_id', 'post_title','post_content','post_date','users.user_login as post_author','posts.post_author as post_author_id')
			->leftJoin('users','users.ID','=','posts.post_author')
			->where('posts.ID', '=', $post_id)
			->get();
		if(count($post)<=0){
			return null;
		}
		$terms = Term::getTermsByPostID($post_id);
		$cat = count($terms)>0?Term::getCategory($terms):array();
		$tag = count($terms)>0?Term::getTag($terms):array();
		$post[0]->category = $cat;
		$post[0]->post_tag = $tag;
		
		return $post[0];
	}
	
	/**
	 * 创建post
	 */
	public static function create_(){
		DB::transaction(function()
		{
			//$post = new Post;
			date_default_timezone_set("Europe/London");
			$post_date_gmt = date('Y-m-d H:i:s',time());
			date_default_timezone_set("Asia/Shanghai");
			$post_date = date('Y-m-d H:i:s',time());
			
			DB::table('posts')->insert(
				array(
					'post_title'=>Input::get('post_title'),
					'post_content'=>Input::get('term_name'), 
					//'post_author'=>Input::get('post_author'),
					'post_date'=>$post_date,
					'post_date_gmt'=>$post_date_gmt
				)
			);
			
			$term_id =Input::get('term_id'); 
			if(!is_null($term_id)){
				$get_last_post_id_sql = "SELECT LAST_INSERT_ID() ID";
				$post_id = DB::select($get_last_post_id_sql);
				DB::table('term_relationships')
				->insert(array(
				'object_id'=>$post_id[0]->ID,
				'term_taxonomy_id'=>$term_id
				));
			}
			
		});
	}
	
	/**
	 * 删除相关评论，标签
	 */
	public function delete_with_term_comment(){
		DB::transaction(function()
		{
			$post_id = Input::get('post_id');
			$post = Post::find($post_id);
			$post->delete();
			
			//delete terms relationship
			DB::table('term_relationships')->where('object_id', '=',$post_id )->delete();
			//delete comments
			DB::table('comments')->where('comment_post_ID','=',$post_id)->delete();
		});
	}
	
	/** --------------------------- Tool funcions --------------------------- **/
	
	/**
	 * 首页内容截取，获取适合长度
	 * @param unknown $content
	 * @param unknown $length
	 * @return Ambigous <number, unknown>
	 */
	public static function get_adjust_length($content, $length){
		preg_match_all("/(<(\/)*([\w]+)[^>]*>)/", $content, $labels, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		$length_backup = $length;
		foreach($labels as $label){
			$label_length  = strlen($label[0][0]);
			$label_idx = $label[0][1];
	
			if( $label_idx + $label_length <= $length ){//before label 
				$res = $length;
			}else if(  $label_idx < $length  && $length < $label[0][1]+ strlen($label[0][0]) ){//in the middle of lable
				$res = $label[0][1]+ strlen($label[0][0]);//正好在中间的标签，删除
				break;
			}else{//after label
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
	public static function is_start_label($label){
		return preg_match("(<([\w]+)[^>]*>)",$label);
	}
	
	/**
	 * 首页内容截取，截取最后一个标签
	 * @param unknown $content
	 * @return string
	 */
	public static function get_adjust_content($content){
		preg_match_all("/(<(\/)*([\w]+)[^>]*>)/", $content, $labels, PREG_SET_ORDER);
		$add_labels = array();
		$stack = array();
		foreach ($labels as $label){
			$label_type = $label[3];
			$label_all = $label[0];
			if(Post::is_start_label($label_all)>0){
				array_push($stack , $label_type);
			}else{
				$front_label_type = array_pop($stack);
				if(!is_null($front_label_type) ){
					if( strcmp($front_label_type , $label_type)!=0 ){
						array_push($stack, $label_type);
					}
				}
			}
		}
		while(count($stack)>0 ){
			$content = $content.'</'.array_pop($stack).'>';
		}
		$content = $content;//."...";
		return $content;
	}
	
	/**
	 * 首页内容截取
	 * @param unknown $content
	 * @param unknown $length
	 * @return unknown
	 */
	public static function get_adjust_post($content,$length){
		if(strlen($content)<=$length){
			return $content;
		}
		$length = Post::get_adjust_length($content,$length);
		$content  = substr($content,0,$length);
		$content = Post::get_adjust_content($content);
		return $content;
	}
}
