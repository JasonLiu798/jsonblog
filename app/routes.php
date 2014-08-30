<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//User
Route::any('/user/reg/{param}','UserController@register');
// www.lblog.com/user/reg/action?username=sdddsfe&password=123456&email=asdfasfd@dfdee
Route::any('/user/login/{param}','UserController@login');
// www.lblog.com/user/login/action?login_password=123456&login_email=asdfasfd@dfdee
Route::any('/user/logout','UserController@logout');
Route::get('/user/chkparameter','UserController@chk_parameter'); 
// www.lblog.com/user/chkparameter?type=username&username=abc
// http://www.lblog.com/user/chkparameter?type=email&email=asdfasfd
//Route::post('/user/register_','UserController@register');/


//Post
Route::get('/{term_id?}', array('as'=>'index','uses' => 'PostController@index'));
Route::get('/date/{date}', array('as'=>'dpost','uses' => 'PostController@post_by_date'));

Route::get('/post/single/{post_id}','PostController@single');
Route::get('/post/create','PostController@create');// post/create?post_title=p1&post_content=c1&term_id=1
Route::get('/post/delete','PostController@delete_with_term_comment');// post/delete?post_id=

//Comment
Route::post('/comment/create','CommentController@create');
Route::get('/comment/delete','CommentController@delete');
Route::get('/term/unreadcmtcnt/{uid}','CommentController@get_unread_comment_cnt');
// /term/unreadcmtcnt/1

//Term
Route::get('/term/ajax_create','TermsController@ajax_create');
Route::get('term/admin','TermsController@admin');
Route::get('/term/delete','TermsController@delete');//term/delete?tid=


//测试用
Route::get('/test/put','TestController@put');
Route::get('/test/get','TestController@get');
Route::get('/test/push','TestController@push');
Route::get('/test/mail','TestController@sendmail');

//错误
Route::get('/error','ErrorController@showerr');



