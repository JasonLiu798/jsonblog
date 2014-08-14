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
Route::get('/user/{type}/{param?}','UserController@processor');
/*
Route::get('/user/register','UserController@register_pre');
Route::post('/user/register_','UserController@register');/// www.lblog.com/user/register_?username=sdd&password=123&email=asdfasfd
*/
Route::get('/user/login','UserController@login'); // 
Route::get('/user/chkparameter','UserController@chk_parameter');


// www.lblog.com/user/chkparameter?type=username&username=abc
// http://www.lblog.com/user/chkparameter?type=email&email=asdfasfd

//Post
Route::get('/', 'PostController@index');
Route::get('/index', 'PostController@index');

Route::get('/post/single/{post_id}','PostController@single');
Route::get('/post/create','PostController@create');// post/create?post_title=p1&post_content=c1&term_id=1
Route::get('/post/delete','PostController@delete_with_term_comment');// post/delete?post_id=

//Comment
Route::post('/comment/create','CommentController@create');
Route::get('/comment/delete','CommentController@delete');
//Term
Route::get('/term/ajax_create','TermsController@ajax_create');
Route::get('term/admin','TermsController@admin');
Route::get('/term/delete','TermsController@delete');//term/delete?tid=


//测试用
Route::get('/test/put','TestController@put');
Route::get('/test/get','TestController@get');

//错误
Route::get('/error','ErrorController@showerr');



