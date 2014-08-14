<?php

class Term extends Eloquent  {

	protected $table = 'terms';
	protected $primaryKey = 'term_id';
	public $timestamps = false;
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
