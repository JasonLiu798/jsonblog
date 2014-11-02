<?php
class ErrorController extends BaseController {
	
	public function show($msg){
		$view = View::make('templates/error',
				array('title'=>'出错了！','msg'=>$msg,
				));
		return $view;
	}
	
}
