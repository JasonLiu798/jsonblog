<?php

class TestController extends BaseController {
	
	
	public function put(){
		//Session::put('key1', 'put a value');
		
	}
	
	public function get(){
		$value = Session::get('key1');
		return 'get:'.$value;
	}
	public function push(){
		$view = View::make('test/push',array('title'=>'push'));
		return $view;
	}
	
}
