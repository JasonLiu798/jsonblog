<?php

class TestController extends BaseController {
	
	
	public function put(){
		Session::put('key1', 'put a value');
		
	}
	
	public function get(){
		$value = Session::get('key1');
		return 'get:'.$value;
	}
	
	
}
