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
	
}
