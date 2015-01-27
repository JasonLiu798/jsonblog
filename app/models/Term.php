<?php

class Term extends Eloquent {

	protected $table = 'terms';
	protected $primaryKey = 'term_id';
	public $timestamps = false;
	public $category2show;
	//public static function getPost

	public function __construct() {
		//$this->load->database();
		$this->category2show = array();
	}

	public function termtaxonomy() {
		return $this->hasOne('TermTaxonomy', 'term_id');
	}

	public static function delete_term_relationship($post_id) {
		//DB::table('terms')
		DB::table('term_relationships')->where('object_id', '=', $post_id)->delete();
	}

	// public static function delete_term_relationship($post_id){
	// 	//DB::table('terms')
	// 	DB::table('term_relationships')->where('object_id', '=', $post_id )->delete();
	// }

	/**
	 *
	 */
	public static function get_name_taxonomy($term_id) {
		/*
		SELECT name,terms.term_id term_id,taxonomy
		FROM terms join term_taxonomy on terms.term_id=term_taxonomy.term_id
		 */
		$term = DB::table('terms')
			->select('terms.name as name', 'terms.term_id as term_id', 'taxonomy')
			->join('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')
			->where('terms.term_id', '=', $term_id)->get();
		return $term;
	}

	// public static function delete_cat_tid($tid) {

	// 	DB::table('terms')->where('term_id', '=', $tid)->delete();
	// 	DB::table('term_taxonomy')->where('term_id', '=', $tid)->delete();
	// }

	/**
	 *
	 * @param unknown $post_id
	 * @return unknown
	 */
	public static function get_terms_by_post($post_id) {
		/*
		select terms.name as name,term_taxonomy.taxonomy as taxonomy,terms.term_id as term_id
		from term_taxonomy
		join terms on terms.term_id=term_taxonomy.term_id
		join term_relationships on term_relationships.term_taxonomy_id =term_taxonomy.term_id
		where term_relationships.object_id=30;
		 */
		$terms = DB::table('term_taxonomy')
			->select('terms.name as name', 'term_taxonomy.taxonomy as taxonomy', 'terms.term_id as term_id')
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->join('term_relationships', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_id')
			->where('term_relationships.object_id', '=', $post_id)
			->get();
//		$queries = DB::getQueryLog();
//		$last_query = end($queries);
//		Log::info('TERMS:' . $last_query['query']);
		return $terms;
	}

	/**
	 * get posts by user id
	 * @param unknown $uid
	 */
	public static function get_terms_uid($uid) {
		/*
		select terms.name as name,term_taxonomy.taxonomy as taxonomy,terms.term_id as term_id
		from term_taxonomy
		inner join terms on terms.term_id=term_taxonomy.term_id
		where terms.uid=1;
		 */
		$terms = DB::table('term_taxonomy')
			->select('terms.name as name', 'term_taxonomy.taxonomy as taxonomy', 'terms.term_id as term_id')
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.uid', '=', $uid)
			->get();
		return $terms;
	}

	public static function get_admin_category() {
//$pagesize){
		/*
		select terms.name,term_taxonomy.taxonomy,terms.term_id,count(term_relationships.object_id) post_cnt,parent
		from term_taxonomy
		left join terms on terms.term_id=term_taxonomy.term_id
		left join term_relationships on term_relationships.term_taxonomy_id=term_taxonomy.term_id
		where term_taxonomy.taxonomy='category'
		group by term_relationships.term_taxonomy_id;
		 */
		$terms = DB::table('term_taxonomy')
			->select('terms.name', 'terms.term_id', 'term_taxonomy.parent', DB::raw('count(term_relationships.object_id) post_cnt'))
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->leftjoin('term_relationships', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_id')
			->where('term_taxonomy.taxonomy', '=', 'category')
			->groupby('terms.term_id')
			->get(); //paginate($pagesize);
		return $terms;
	}

	public static function get_admin_tag() {
		$terms = DB::table('term_taxonomy')
			->select('terms.name', 'terms.term_id', DB::raw('count(term_relationships.object_id) post_cnt'))
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->leftjoin('term_relationships', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_id')
			->where('term_taxonomy.taxonomy', '=', 'post_tag')
			->groupby('terms.term_id')
			->get(); //paginate($pagesize);
		return $terms;
	}

	public static function get_category($terms) {
		return is_null($terms) ? null : array_filter($terms, function ($v) {return $v->taxonomy === 'category';});
	}

	public static function get_tag($terms) {
		return is_null($terms) ? null : array_filter($terms, function ($v) {return $v->taxonomy === 'post_tag';});
	}

	/**
	 * for index show
	 */
	public static function getTermsAndStat() {
		/*
		select terms.name term_name,term_taxonomy.taxonomy,count(*) term_count from terms
		left join term_relationships on terms.term_id=term_taxonomy_id
		left join term_taxonomy on term_taxonomy.term_id=terms.term_id
		inner join posts on term_relationships.object_id=posts.ID
		group by term_name order by term_count desc;

		Improve version,add unclassfy posts
		select terms.name term_name,count(posts.ID) term_count
		from posts
		left join term_relationships on term_relationships.object_id=posts.ID
		left join terms on terms.term_id=term_relationships.term_taxonomy_id
		left join term_taxonomy on term_taxonomy.term_id=terms.term_id
		group by term_name order by terms_count desc;
		 */
// 		$terms = DB::table('posts')
		// 			->select(DB::raw('count(posts.ID) term_count , terms.name as term_name,terms.term_id term_id'))
		// 			->leftJoin('term_relationships', 'term_relationships.object_id', '=', 'posts.ID')
		// 			->leftJoin('terms','terms.term_id','=','term_relationships.term_taxonomy_id')
		// 			->groupBy('term_name')->orderBy('term_count', 'desc')
		// 			->get();

		$terms = DB::table('terms')
			->select( DB::raw('count(*) term_count , terms.name as term_name,terms.term_id,term_taxonomy.taxonomy'))
			->leftJoin('term_relationships', 'term_relationships.term_taxonomy_id', '=', 'terms.term_id')
			->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')
			->join('posts', 'posts.ID', '=', 'term_relationships.object_id')
			->groupBy('term_name')->orderBy('term_count', 'desc')
			->get();
		return $terms;
	}
	/*
	public static function getCategory($terms){
	return is_null($terms)?null:array_filter($terms,function($v){ return $v->taxonomy==='category'; });
	}

	public static function getTag($terms){
	return is_null($terms)?null:array_filter($terms,function($v){ return $v->taxonomy==='post_tag'; });
	}
	 */

	/**
	 * create category async
	 * used in the post create
	 */
	public static function create_category_api($category_name, $parent_id) {
		$term_id = 0;
		DB::transaction(
			function () use (&$term_id, $category_name, $parent_id) {
				$term = new Term;
				DB::table('terms')->insert(
					array('name' => $category_name) //,'uid'=>$user_id)
				);

				$get_last_term_id_sql = "SELECT LAST_INSERT_ID() term_id";
				//$term_id = ;
				//Log::info("TERMID".$term_id[0]->term_id);
				$term_id = DB::select($get_last_term_id_sql)[0]->term_id;
				DB::table('term_taxonomy')->insert(
					array('term_id' => $term_id, //$term_id[0]->term_id,
						'taxonomy' => Constant::$TERM_CATEGORY,
						'description' => '',
						'parent' => $parent_id,
						'count' => 0) //提交后count+1
				);
			});
//		Log::info('CreateCategory:' . $term_id);
		return $term_id;
	}

	public static function get_cat_tid($tid) {
		$cat = DB::table('terms')
			->select('terms.term_id', 'terms.name', 'term_taxonomy.parent')
			->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')
			->where('terms.term_id', '=', $tid)
			->take(1)
			->get();
		if (is_array($cat) && count($cat) > 0) {
			return $cat[0];
		} else {
			return null;
		}
		// return $cat[0];
	}

	public static function get_tag_tid($tid) {
		$tag = DB::table('terms')
			->select('terms.term_id', 'terms.name')
			->leftJoin('term_taxonomy', 'term_taxonomy.term_id', '=', 'terms.term_id')
			->where('terms.term_id', '=', $tid)
			->where('term_taxonomy.taxonomy', '=', 'post_tag')
			->take(1)
			->get();
		if (is_array($tag) && count($tag) > 0) {
			return $tag[0];
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param $name
	 * @return bool
	 */
	public static function get_tag_id_by_name($name){
		$term = Term::where('name','=', $name)->firstOrFail();
		if(!is_null($term)){
			$term_taxonomy = $term->termtaxonomy;
			if(!is_null($term_taxonomy)){
				$taxonomy = $term_taxonomy->taxonomy;
				if( $taxonomy === 'post_tag') {
					return $term_taxonomy->term_id;
				}
			}
		}
		return false;
	}

	/**
	 *
	 * @param unknown $user_id
	 * @param unknown $tag_name
	 * @return unknown
	 */
	public static function create_tag_api($tag_name) {
		$term_id = 0;
		DB::transaction(
			function () use (&$term_id, $tag_name) {
				$term = new Term;
				DB::table('terms')->insert(
					array('name' => $tag_name) //,'uid'=>$user_id)
				);

				$get_last_term_id_sql = "SELECT LAST_INSERT_ID() term_id";
				//$term_id = ;
				//Log::info("TERMID".$term_id[0]->term_id);
				$term_id = DB::select($get_last_term_id_sql)[0]->term_id;
				DB::table('term_taxonomy')->insert(
					array('term_id' => $term_id, //$term_id[0]->term_id,
						'taxonomy' => Constant::$TERM_TAG,
						'description' => '',
						'parent' => Constant::$TERM_TAG_NOPARENT,
						'count' => 0) //提交后count+1
				);
			});
//		Log::info('CreateTag:' . $term_id);
		return $term_id;
	}

	/**
	 * deprate 校验term名
	 * @param $term_name
	 * @return mixed
	 *
	public static function chk_term_name_exist($term_name) {
	return DB::table('terms')->where('name', '=', $term_name)->count();
	}
	 */

	/**
	 * 校验分类名
	 * @param $cat_name
	 * @return mixed
	 */
	public static function chk_category_name_exist($cat_name) {
		return DB::table('terms')
			->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.name', '=', $cat_name)
			->where('term_taxonomy.taxonomy', '=', 'category')->count();
	}

	/**
	 * 校验标签名
	 * @param $tag_name
	 * @return mixed
	 */
	public static function chk_tag_name_exist($tag_name) {
		return DB::table('terms')
			->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.name', '=', $tag_name)
			->where('term_taxonomy.taxonomy', '=', 'post_tag')->count();
	}

	public static function chk_cat_exist_tid($tid) {
		return DB::table('terms')
			->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.term_id', '=', $tid)
			->where('term_taxonomy.taxonomy', '=', 'category')->count();
	}

	public static function chk_tag_exist_tid($tid) {
		return DB::table('terms')
			->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.term_id', '=', $tid)
			->where('term_taxonomy.taxonomy', '=', 'post_tag')->count();
		// ->where('term_id','=',$tid )->count();
	}

	public static function chk_cat_exist($tid) {
		return DB::table('terms')
			->join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('terms.term_id', '=', $tid)
			->where('term_taxonomy.taxonomy', '=', 'category')->count();
	}

	/**
	 * get categories
	 */
	public static function get_all_categories() {
		/*
		select terms.name as name,term_taxonomy.parent,terms.term_id as term_id
		from term_taxonomy
		inner join terms on terms.term_id=term_taxonomy.term_id
		where terms.uid=1 and taxonomy='category';
		 */
		$category = DB::table('term_taxonomy')
			->select('terms.name', 'term_taxonomy.parent', 'terms.term_id')
			->join('terms', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->where('taxonomy', '=', 'category')
			->get();
		return $category;
	}

	/**
	 * sort category to tree format and add space in the front
	 * @param unknown $category,at least hava term_id,parent
	 */
	public function format_category2tree($category, $padding_char) {
		$root = new stdClass;
		$root->term_id = 0;
		$root->name = 'root';
		$this->sort_category2tree_recu($root, 0, $category, $padding_char);
		return $this->category2show;
	}

	/**
	 *
	 * @param unknown $term
	 * @param unknown $level
	 * @param unknown $parent_terms
	 */
	public function sort_category2tree_recu($category, $level, $parent_categorys, $padding_char) {
		if (intval($category->term_id) != 0) {
			$level += 1;
		}
		foreach ($parent_categorys as $each_cat):
			if ($category->term_id == $each_cat->parent) {
				// got child
				if (intval($each_cat->term_id) != 0) {
					$this->category_add_padding_char($each_cat, $level, $padding_char);
				}
				if ($this->has_child($each_cat, $parent_categorys)) {// ['is_leaf']==='N'){//hava child
					$this->sort_category2tree_recu($each_cat, $level, $parent_categorys, $padding_char); // find child's child
				}
			}
		endforeach;
		return;
	}

	/**
	 * TOOL
	 * add spaces by level to format a tree
	 * @param unknown $term
	 * @param unknown $level
	 */
	function category_add_padding_char($category, $level, $padding_char) {
		$space = '';
		for ($i = 1; $i <= $level; $i++) {
			$space .= $padding_char;
		}
		$category->name = $space . $category->name;
		array_push($this->category2show, $category);
		return;
	}

	/**
	 * TOOL
	 * determine the term has a child or not
	 */
	function has_child($cat, $categories) {
		foreach ($categories as $iterator):
			if ($iterator->parent == $cat->term_id) {
				return true;
			}

		endforeach;
		return false;
	}

	/**
	 * get top 5 post tags
	 */
	public static function get_top5_post_tag() {
		/*
		SELECT terms.term_id,terms.term_name,count(term_relationships.object_id) term_count FROM terms left join term_taxonomy on terms.term_id=term_taxonomy.term_id
		left join term_relationships on terms.term_id=term_relationships.term_taxonomy_id
		where taxonomy='post_tag' group by terms.term_id order by term_count limit 5;
		 */
		$terms = DB::table('terms')
			->select('terms.term_id', 'terms.name', DB::raw('count(term_relationships.object_id) as term_count'))
			->leftJoin('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
			->leftJoin('term_relationships', 'terms.term_id', '=', 'term_relationships.term_taxonomy_id')
			->where('taxonomy', '=', 'post_tag')
			->groupBy('terms.term_id')
			->orderBy('term_count')
			->take(5)
			->get();
		return $terms;
	}

	public static function get_child($tid) {
		$childs = DB::table('term_taxonomy')->select('term_id')->where('parent', '=', $tid)->get();
		return $childs;
	}

	public static function delete_with_child($tid) {
		$childs = self::get_child($tid);
		if (count($childs) > 0) {
			foreach ($childs as $child) {
				self::delete_with_child($child->term_id);
			}
		}
		self::delete_term_tid($tid);
	}

	public static function delete_term_tid($tid) {
		DB::transaction(function () use ($tid) {
			Log::info('-----------------------DELETE TID:' . $tid);
			DB::table('terms')->where('term_id', '=', $tid)->delete();
			DB::table('term_taxonomy')->where('term_id', '=', $tid)->delete();
			DB::table('term_relationships')->where('term_taxonomy_id', '=', $tid)->delete();
		});
	}

	public static function update_term($tid, $name, $pid) {
		DB::transaction(function () use ($tid, $name, $pid) {
			Log::info('-----------------------UPDATE TID:' . $tid);
			DB::table('terms')->where('term_id', '=', $tid)->update(array('name' => $name));
			DB::table('term_taxonomy')->where('term_id', '=', $tid)->update(array('parent' => $pid));
			//DB::table('term_relationships')->where('term_taxonomy_id', '=', $tid)->delete();
		});
	}

	public static function update_tag($tid, $name) {
		Log::info('-----------------------UPDATE TID:' . $tid);
		DB::table('terms')->where('term_id', '=', $tid)->update(array('name' => $name));

		// DB::transaction(function () use ($tid, $name, $pid) {
		// 	DB::table('term_taxonomy')->where('term_id', '=', $tid)->update(array('parent' => $pid));
		// 	//DB::table('term_relationships')->where('term_taxonomy_id', '=', $tid)->delete();
		// });
	}

	public static function get_cat_name($tid) {
		$cat = DB::table('terms')->where('term_id', '=', $tid)->select('terms.name')->get();
		if (is_array($cat) && count($cat) > 0) {
			return $cat[0]->name;
		} else {
			return null;
		}
	}


	public static function chk_tag_ids_str($ids_str){
		$res = true;
		if ( strlen($ids_str) > 0) {
			$tag_ids_arr = explode(',', $ids_str );
			if(count($tag_ids_arr)>0){
				foreach($tag_ids_arr as $tag_id){
					if(Term::chk_tag_exist_tid($tag_id)<=0){
						$res = false;//只要有一个tag id 为空，则此字符串整体都无效
						break;
					}
				}
			}else{
				$res = false;//无标签，
			}
		}else{
			$res = false;
		}
		return $res;
	}

	/**
	 * Process post's tag ids
	 * before:must call chk_ids_str()
	 * @param $ids_str	format:tid1,tid2,...,tid3
	 * @return mixed
	 * @throws Exception
	 */
	public static function process_idstr($ids_str){
		$res = array();
		$tag_ids_arr = explode(',', $ids_str );
		if(count($tag_ids_arr)>0) {
			foreach ($tag_ids_arr as $tag_id) {
				if (Term::chk_tag_exist_tid($tag_id) > 0) {
					array_push($res, $tag_id);
				}
			}
		}
		return $res;
	}

	/**
	 * change $post->post_tag[array] to post_tag_id[tid1,tid2...tidN]
	 * @param $post
	 */
	public static function get_post_tag_id_str(&$post){
		if (!is_null($post->post_tag)) {
			$post_tag_id = '';
			$len = count($post->post_tag);
			if ($len > 0) {
				$i = 0;
				foreach ($post->post_tag as $tag) {
					if ($i == 0) {
						$post_tag_id = $tag->term_id;
					} else {
						$post_tag_id = $post_tag_id . ',' . $tag->term_id;
					}
					$i++;
				}
			}
			$post->post_tag_id = $post_tag_id;
		} else {
			$post->post_tag_id = null;
		}
	}


	public static function exist_post_term($post_id,$term_id){
		try{
			$res = DB::table('term_relationships')
				->where('object_id','=',$post_id)
				->where('term_taxonomy_id','=',$term_id)
				->count();
		}catch(Exception $e){
			Log::error("Class:{__CLASS__},Method:{__METHOD__},DB error {$e->getMessage()}");
			return false;
		}
		return $res;
	}

	public static function delete_exist_post_term_relation($post_id,&$term_id_arr){
		foreach($term_id_arr as $key=>$term_id) {
			$res = Term::exist_post_term($post_id,$term_id);
			if( $res && $res>0){
				array_splice($term_id_arr,$key,1);
			}
		}
	}

	/**
	 * 获取分类树，标记 $post 的已有分类
	 * @param $post
	 * @return array|null
	 */
	public static function get_category_tree(&$post){
		$category = self::get_all_categories();
		if (!is_null($category) && count($category) > 0) {
			$term = new Term();
			$category_tree = $term->format_category2tree($category, '--');
			foreach ($category_tree as $cat) {
				if (!is_null($post->category)) {
					if ($cat->term_id == $post->category->term_id) {
						$cat->selected = 1;
					} else {
						$cat->selected = 0;
					}
				} else {
					$cat->selected = 0;
				}
			}
			$res = $category_tree;
		}else{
			$res = null;
		}
		return $res;
	}



}
