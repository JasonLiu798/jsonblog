<?php

class Post extends Eloquent  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'posts';
	protected $primaryKey = 'ID';
	
// 	public function get()
// 	{
// 		return $this->hasMany('Comment');
// 	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * @param unknown $pagesize
	 * @return unknown
	 */
	public static function getPostsByUserID($user_id,$pagesize){
/**
select users.user_login post_author,posts.ID post_id,post_date,post_content,count(comments.comment_ID) post_comment_count
from posts 
left join comments on comments.comment_post_ID = posts.ID
left join users on users.ID = posts.post_author
where  posts.post_author = 1
group by posts.ID
order by posts.post_date desc;
*/
		$posts = DB::table('posts')
			->select('users.user_login as post_author','posts.ID as post_id','post_title','post_date',DB::raw('count(comments.comment_ID) as post_comment_count'))
			->leftJoin('comments','comments.comment_post_ID','=','posts.ID')
			->leftJoin('users','users.ID','=','posts.post_author')
			->where('posts.post_author','=',$user_id)
			->groupBy('posts.ID')
			->orderBy('posts.post_date','desc')
			->paginate($pagesize);
		return $posts;
	}
	
	/**
	 * 获取所有posts 评论数，作者，
	 * @param unknown $pagesize
	 * @return NULL
	 */
	public static function get_posts($pagesize){
		$posts = null;
		if($pagesize>0){
			$posts = DB::table('posts')
				->leftJoin('users','users.ID','=','posts.post_author')
				->leftJoin('postimages','postimages.iid','=','posts.post_cover_img')
				->select('posts.ID as post_id', 'post_title','post_content','post_date','users.user_login as post_author','post_summary','postimages.filename as post_img_name',DB::raw('count(comments.comment_ID) as comment_count'))
				->leftJoin('comments','comments.comment_post_ID','=','posts.ID')
				->groupBy('posts.ID')
				->orderBy('post_date','desc')
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
			select posts.ID post_id,users.user_login post_author,post_date,post_content,users.ID as post_author_id,
			post_title,count(comments.comment_ID) comment_count
			from posts 
			join term_relationships on posts.ID=term_relationships.object_id
			join users on users.ID=posts.post_author
			left join comments on comments.comment_post_ID=posts.ID
			where term_relationships.term_taxonomy_id=1 group by posts.ID;
		 */
		$posts = null;
		if($pagesize>0){
			$posts = DB::table('posts')
			->select('posts.ID as post_id', 'post_title','post_content','post_date','users.user_login as post_author'
					,DB::raw('count(comments.comment_ID) as comment_count'))
			->join('users','users.ID','=','posts.post_author')
			->join('term_relationships','posts.ID','=','term_relationships.object_id')
			->leftJoin('comments','comments.comment_post_ID','=','posts.ID')
			->where('term_relationships.term_taxonomy_id','=',$term_id)
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
	public static function getPostByDate($date,$pagesize){
		/**
		  select posts.ID post_id,users.user_login post_author,users.ID as post_author_id,DATE_FORMAT(posts.post_date,'%Y-%m'),
		  post_content,post_title,count(comments.comment_ID) comment_count
		  from posts 
		  join users on users.ID = posts.post_author 
		  left join comments on comments.comment_post_ID=posts.ID
		  where DATE_FORMAT(posts.post_date,'%Y-%m') = '2014-06' group by posts.ID;
		 */
		//$search_date = $year.'-'.$month;
		$posts = DB::table('posts')
			->select('posts.ID as post_id','users.user_login as post_author','users.ID as post_author_id','post_date',
					'post_content','post_title',DB::raw('count(comments.comment_ID) as comment_count'))
			->leftJoin('users','users.ID','=','posts.post_author')
			->leftJoin('comments','comments.comment_post_ID','=','posts.ID')
			//->where("DATE_FORMAT( posts.post_date,'%Y-%m')",'=',$date)
			->whereRaw("DATE_FORMAT( posts.post_date,'%Y-%m')='".$date."'")
			->groupBy('posts.ID')
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
		 post_content,post_title,users.ID post_author_id,count(comments.comment_ID) comment_count
		 from posts 
		 join users on users.ID = posts.post_author
		 left join comments on comments.comment_post_ID=posts.ID
		 where post_author=1 group by posts.ID;
		 */
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
	
	public static function getPostCommentStat($post_id){
		$comm_stat = DB::table('posts')
			->join('comments','posts.ID','=','comments.comment_post_ID')
			->where('posts.ID','=',$post_id)
			->count();
		return $comm_stat;
	}
	
	//public static function getNextPost($post_id)
	
	public static function getPreNextPost($post_id){
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
			->select('ID as post_id','post_title')
			->where('ID','<',$post_id)
			->orderBy('ID','desc')
			->take(1)->get();
	    $res['pre_post']=	$pre_post;
	    
$queries = DB::getQueryLog();
$last_query = end($queries);
Log::info('PRE POST SQL:'.$last_query['query']);
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
			->select('ID as post_id','post_title')
			->where('ID','>',$post_id)
			->orderBy('ID')
			->take(1)->get();
		$res['next_post']=	$next_post;
$queries = DB::getQueryLog();
$last_query = end($queries);
Log::info('NXT POST SQL:'.$last_query['query']);
		return $res;
	}
	
	public static function postAddMeta($posts){
		foreach($posts as $post):
			$terms = Term::getTermsByPostID($post->post_id);
		
			$cat = Term::getCategory($terms);
			
			$tag = Term::getTag($terms);
			$post->category = !empty($cat)?$cat:null;
			$post->post_tag = !empty($tag)?$tag:null;
			
// 			if(count($tag)>0){
// 				foreach($terms as $term)
// 					Log::info('post term:'.$term->name.',TAX:'.$term->taxonomy);
// 			}
			
		endforeach;
		return $posts;
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
	public static function create_post($user_id,$post_title,$post_content,$post_cover_img_id,$post_summary){
			//$post = new Post;
		date_default_timezone_set("Europe/London");
		$post_date_gmt = date('Y-m-d H:i:s',time());
		date_default_timezone_set("Asia/Shanghai");
		$post_date = date('Y-m-d H:i:s',time());
		
		DB::table('posts')->insert(
			array(
				'post_author'		=>$user_id,
				'post_title'		=>$post_title,
				'post_content'		=>$post_content, 
				'post_date'			=>$post_date,
				'post_date_gmt'		=>$post_date_gmt,
				'post_modified'		=>$post_date,
				'post_modified_gmt'	=>$post_date_gmt,
				
				'post_summary' 		=> $post_summary,
				'post_cover_img'	=> $post_cover_img_id
			)
		);
		$get_last_post_id_sql = "SELECT LAST_INSERT_ID() ID";
		$post_id = DB::select($get_last_post_id_sql)[0]->ID;
		Log::info('CreatePost:'.$post_id);
		return $post_id; 
	}
	
	/**
	 * 删除相关评论，标签
	 */
	public static function delete_all($post_id){
		DB::transaction(function() use($post_id)
		{
			//$post_id = Input::get('post_id');
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
		$res = 0;
		foreach($labels as $label){
			$label_length  = mb_strlen($label[0][0], Constant::$UTF_8);//, Constant::$UTF_8);
			$label_idx = $label[0][1];
			if( $label_idx + $label_length <= $length ){//before label 
				$res = $length;
			}else if(  $label_idx < $length  && $length < $label[0][1]+ mb_strlen($label[0][0], Constant::$UTF_8) ){//in the middle of lable
				$res = $label[0][1]+ mb_strlen($label[0][0], Constant::$UTF_8);//正好在中间的标签，删除
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
		preg_match("(<([\w]+)[^>]*>)",$label,$matches);
		$res = false;
		if(count($matches)>0){
			$res = true;
		}
		return $res;
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
Log::info('Cut to:'.$length);		
Log::info('Before cut:'.strlen($content));
		if(mb_strlen($content, Constant::$UTF_8)<=$length){
			return $content;
		}
		$length = Post::get_adjust_length($content,$length);
Log::info('Adjust Lenght:'.$length);		
		//$content  = substr($content,0,$length);
		$content = mb_substr($content,0,$length, Constant::$UTF_8 );
Log::info('Af cut:'.$content);		
		//$content = substr($content,0,$length);//, Constant::$UTF_8 );
		$content = Post::get_adjust_content($content);
Log::info('Af add:'.$content);
		return $content;
	}
	
	public static function create_post_term( $post_id, $termid_arr ){
		$insert_arr = array();
		foreach($termid_arr as $term_id){
			array_push($insert_arr, array('object_id'=>$post_id,'term_taxonomy_id'=>$term_id) );
		}
		DB::table('term_relationships')->insert($insert_arr);
	}
	
	
	
	/**
	 * 获取纯文字摘要
	 * @param unknown $content
	 * @param unknown $length
	 * @return string
	 */
	public static function get_summary($content,$length){
		$res = "";
		$short_content = self::remove_html_label($content);
// 		if( mb_strlen($content,'utf-8')<$length ){
// 			$res = $content;
// 		}else{
		
		if( mb_strlen($short_content,'utf-8')<$length ){
			$res = $short_content;
		}else{
			$res = mb_substr($short_content ,0,$length, Constant::$UTF_8 );// );
		}
		return $res.'...';
	}
	
	/**
	 * 删除所有html标签
	 * @param unknown $content
	 * @return unknown
	 */
	public static function remove_html_label($content){
		$res = preg_replace("/<(.[^>]*)>/","",$content);
		return $res;
	}
	
}
