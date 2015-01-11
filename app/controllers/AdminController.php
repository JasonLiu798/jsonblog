<?php

class AdminController extends BaseController {

    public function index(){
        $sess_user = Session::get('user');
        $username = User::get_name_from_session( $sess_user);
        $title = '后台管理';
        $view = View::make('admin/index',
                array('title'=>$title,'username'=>$username, 
                        ));
        return $view;
    }
}