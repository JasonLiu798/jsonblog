<?php
class Tree{
	public static function get_childs(&$parent,&$nodes){
		if(is_null($parent)){
			return;
		}
		$tmp_arr = array();
		$got=false;
		foreach ($nodes as $node ) {
			if($node->parent == $parent->id){
				$got = true;
				array_push($tmp_arr, $node);
			}
		}
		if(!$got){
			$parent->childs = null;
		}else{
			$parent->childs = &$tmp_arr;
		}
	}
	
	public static function get_onechild(&$childs){
		if(!empty($childs) && count($childs)>0){
			foreach($childs as $child){
				$res = array_pop($childs);
				return $res;
			}
		}
		return null;
	}
	
	public static function init_child(&$nodes){
		foreach ($nodes as $node){
			Tree::get_childs($node,$nodes);
		}
	}
}