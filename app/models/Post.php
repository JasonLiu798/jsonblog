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
	
	public static function getPosts($start,$limit){
		
	}
	
	public static function getPostById($post_id){
		$post = DB::table('posts')
			->join('users','users.ID','=','posts.post_author')
			->select('posts.ID as ID', 'post_title','post_content','post_date','users.user_login as post_author')
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
