<?php

class Term extends Eloquent  {

	protected $table = 'terms';
	protected $primaryKey = 'term_id';
	
	//public static function getPost
	
	
	/**
	 * 
	 * @param unknown $post_id
	 * @return unknown
	 */
	public static function getTermsByPostID($post_id){
		$terms = DB::table('term_taxonomy')
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->join('term_relationships','term_relationships.term_taxonomy_id','=','term_taxonomy.term_taxonomy_id')
			->where('term_relationships.object_id','=',$post_id)
			->select('terms.name as name','term_taxonomy.taxonomy as taxonomy')
			->get();
		return $terms;
	}
	
	public static function getCategory($terms){
		return array_filter($terms,function($v){ return $v->taxonomy==='category'; });
	}
	
	public static function getTag($terms){
		return array_filter($terms,function($v){ return $v->taxonomy==='post_tag'; });
	}
	
}
