<?php

class Term extends Eloquent  {

	protected $table = 'terms';
	protected $primaryKey = 'term_id';
	public $timestamps = false;
	//public static function getPost
	
	/**
	 * 
	 */
	public static function getTermNameTaxonomy($term_id){
		/*
			SELECT name,terms.term_id term_id,taxonomy 
			FROM terms join term_taxonomy on terms.term_id=term_taxonomy.term_id
		 */
		$term = DB::table('terms')
			->select('terms.name as name','terms.term_id as term_id','taxonomy')
			->join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
			->where('terms.term_id','=',$term_id)->get();
		return $term;
	}
	
	
	/**
	 * 
	 * @param unknown $post_id
	 * @return unknown
	 */
	public static function getTermsByPostID($post_id){
		/**
		select terms.name as name,term_taxonomy.taxonomy as taxonomy,terms.term_id as term_id
		from term_taxonomy
		join terms on terms.term_id=term_taxonomy.term_id
		join term_relationships on term_relationships.term_taxonomy_id =term_taxonomy.term_taxonomy_id
		where term_relationships.object_id=1;
		 */
		$terms = DB::table('term_taxonomy')
			->select('terms.name as name','term_taxonomy.taxonomy as taxonomy','terms.term_id as term_id')
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->join('term_relationships','term_relationships.term_taxonomy_id','=','term_taxonomy.term_taxonomy_id')
			->where('term_relationships.object_id','=',$post_id)
			->get();
		return $terms;
	}
	
	/**
	 * get posts by user id
	 * @param unknown $uid
	 */
	public static function getTermsByUserID($uid){
		/*
		 select terms.name as name,term_taxonomy.taxonomy as taxonomy,terms.term_id as term_id
			from term_taxonomy
			inner join terms on terms.term_id=term_taxonomy.term_id
		where terms.uid=1;
		 */
		$terms = DB::table('term_taxonomy')
		->select('terms.name as name','term_taxonomy.taxonomy as taxonomy','terms.term_id as term_id')
		->join('terms','terms.term_id','=','term_taxonomy.term_id')
		->where('terms.uid','=',$uid)
		->get();
		return $terms;
	}
	
	public static function getCategory($terms){
		return is_null($terms)?null:array_filter($terms,function($v){ return $v->taxonomy==='category'; });
	}
	
	public static function getTag($terms){
		return is_null($terms)?null:array_filter($terms,function($v){ return $v->taxonomy==='post_tag'; });
	}
	
	/**
	 * for index show
	 */
	public static function getTermsAndStat(){
		/*
		select terms.name term_name,count(*) term_count from terms
		left join term_relationships on terms.term_id=term_taxonomy_id 
		inner join posts on term_relationships.object_id=posts.ID
		group by term_name order by term_count desc;
		
		Improve version,add unclassfy posts
		select terms.name term_name,count(posts.ID) term_count 
			from posts
		left join term_relationships on term_relationships.object_id=posts.ID
		left join terms on terms.term_id=term_relationships.term_taxonomy_id 
		group by term_name order by terms_count desc;
		*/
// 		$terms = DB::table('posts')
// 			->select(DB::raw('count(posts.ID) term_count , terms.name as term_name,terms.term_id term_id'))
// 			->leftJoin('term_relationships', 'term_relationships.object_id', '=', 'posts.ID')
// 			->leftJoin('terms','terms.term_id','=','term_relationships.term_taxonomy_id')
// 			->groupBy('term_name')->orderBy('term_count', 'desc')
// 			->get();
		$terms = DB::table('terms')
		->select(DB::raw('count(*) term_count , terms.name as term_name,terms.term_id term_id'))
		->leftJoin('term_relationships', 'term_relationships.term_taxonomy_id', '=', 'terms.term_id')
		->join('posts','posts.ID','=','term_relationships.object_id')
		->groupBy('term_name')->orderBy('term_count', 'desc')
		->get();
		return $terms;
	}
	
	public static function ajax_create(){
		
		DB::transaction(function()
		{
			$term  = new Term;
			
			//$post_id = Input::get('post_id');
			DB::table('terms')->insert(
				array('name'=>Input::get('term_name') )
			);
			
			$get_last_term_id_sql = "SELECT LAST_INSERT_ID() term_id";
			$term_id = DB::select($get_last_term_id_sql);
Log::info("TERMID".$term_id[0]->term_id);
			
			DB::table('term_taxonomy')->insert(
				array('term_id' => $term_id[0]->term_id,
				'taxonomy' => Input::get('term_taxonomy'),
				'description'=> '',
				'parent'=> Input::get('parent'), 'count'=>0)//提交后count+1
			);
			
		});
	}
	
	public static function delete_by_tid(){//$tid){
		//$tid_ = $tid;
		DB::transaction(function()
		{
			$tid = Input::get('tid');
			Log::info('-----------------------DELETE TID:'.$tid);
			DB::table('terms')->where('term_id', '=', $tid )->delete();
			DB::table('term_taxonomy')->where('term_id', '=', $tid )->delete();
			DB::table('term_relationships')->where('term_taxonomy_id', '=', $tid )->delete();
		});
	}
	
}
