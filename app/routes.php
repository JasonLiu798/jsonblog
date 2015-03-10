<?php

//主页
Route::any('/',  array('as' => 'index', 'uses' => 'PostController@index'));
Route::any('/page/{page?}',  array('as' => 'indexpage', 'uses' => 'PostController@index'));

//register/login
Route::any('/reg', 'UserController@register');
// www.lblog.com/user/reg/action?username=sdddsfe&password=123456&email=asdfasfd@dfdee
Route::any('/login', 'UserController@login');
// www.lblog.com/user/login/action?login_password=123456&login_email=asdfasfd@dfdee
Route::any('/logout', 'UserController@logout');




Route::any('about', 'BaseController@about');
//Post
Route::group(array('prefix' => 'post'), function () {
	//浏览
	//分类浏览	/post/term/xxx
	Route::get('/term/{term_id}', array('as' => 'idx_term', 'uses' => 'PostController@term_achive'));
	//日期浏览	 /post/date/2014-10
	Route::get('/date/{date}', array('as' => 'idx_date', 'uses' => 'PostController@date_achive'));
	//ID浏览
	Route::any('single/{post_id}', array('as' => 'singlepost', 'uses' => 'PostController@single')); //
	//Route::any('/author/{user_id}', array('as'=>'author','uses'=>'PostController@posts_by_author'));
	//--------管理-----------
	//管理页面
	Route::any('admin', 'PostController@admin');
	//{term_id}/{post_date?}',
	// post/single/id

	// /post/create/save?post_title=testposttitle1&post_content=testcontent&category=1&post_tag_ids=46,44,47
	Route::get('delete', 'PostController@delete_with_term_comment'); // post/delete?post_id=
	Route::get('delete_', 'PostController@delete_only_post'); // post/delete_?post_id=
	Route::any('search', 'PostController@search');
	Route::group(array('prefix' => 'api'), function () {
		Route::any('save/{post_id}','PostController@save');//	admin/post/api/save/pid
	});
});

//Comment
Route::group(array('prefix' => 'comment'), function () {
	// Route::any('/', 'CommentController@admin'); //	admin/comment
	Route::any('/', array('as' => 'comment_admin', 'uses' => 'CommentController@admin')); //	admin/comment
	Route::any('delete/{cid}', 'CommentController@delete'); // admin/comment/delete?post_id=
	Route::any('batchdelete', 'CommentController@batch_delete');
	Route::any('/create', 'CommentController@create');
	Route::any('/get', 'CommentController@postcomments');// comment/get?page=1&post_id=72
	// Route::get('deleteonly/{cid}','PostController@delete_comment');// admin/post/delete_?post_id=
});


Route::any('/term/unreadcmtcnt/{uid}', 'CommentController@get_unread_comment_cnt');
// /term/unreadcmtcnt/1

Route::group(array('prefix' => 'message'), function () {
	Route::any('/', array('as' => 'messages', 'uses' => 'CommentController@messages'));

});

//User
Route::group(array('prefix' => 'user'), function () {

});

Route::get('/user/chkparameter', 'UserController@chk_parameter');
// www.lblog.com/user/chkparameter?type=username&username=abc
// http://www.lblog.com/user/chkparameter?type=email&email=asdfasfd
//Route::post('/user/register_','UserController@register');/

// default:show page post/create/page
// add post  posts/create/do?post_title=p1&post_content=c1&term_id=1

/**
 * check user login for admin
 */
Route::filter('logincheck', function () {
	$sess_user = Session::get('user');
	if (is_null($sess_user)) {
		return Redirect::route('error', array('未登录/非管理员'));
//			Response::json(array('status' => false , 'error' => '未登录', 'errorcode' =>
//			Constant::$NOLOGIN ));
//		return Redirect::action('PostController@index');
	}
});

//管理
Route::group(array( 'before'=> 'logincheck','prefix' => 'admin'), function () {
//	Route::any('index', 'AdminController@index');//admin index
	Route::group(array('prefix' => 'post'), function () {
		Route::any('/', array('as' => 'post_admin', 'uses' => 'PostController@admin')); //	admin/post
		Route::any('create', 'PostController@create');// admin/post/create
		Route::any('update/{post_id}', 'PostController@update');// admin/post/update/pid

		Route::any('delete/{post_id}', 'PostController@delete_all'); // admin/post/delete?post_id=
		// Route::get('delete_/{post_id}', 'PostController@delete_post'); // admin/post/delete_?post_id=
		Route::any('batchdelete', 'PostController@batch_delete');// admin/post/batchdelete


	});

	Route::group(array('prefix' => 'comment'), function () {
		// Route::any('/', 'CommentController@admin'); //	admin/comment
		Route::any('/', array('as' => 'comment_admin', 'uses' => 'CommentController@admin')); //	admin/comment
		Route::any('delete/{cid}', 'CommentController@delete'); // admin/comment/delete?post_id=
		Route::any('batchdelete', 'CommentController@batch_delete');
		// Route::get('deleteonly/{cid}','PostController@delete_comment');// admin/post/delete_?post_id=
	});

	Route::group(array('prefix' => 'category'), function () {
		// array('as'=>'index','uses' => 'PostController@index')
		Route::any('/', array('as' => 'cat_admin', 'uses' => 'TermsController@cat_admin')); //	admin/category
		Route::any('delete/{tid}', 'TermsController@cat_delete'); // admin/category/delete/tid
		Route::any('batchdelete', 'TermsController@cat_batch_delete');
		Route::any('create', 'TermsController@cat_create'); // admin/category/create
		Route::any('update/{tid}', 'TermsController@cat_update'); // admin/category/create
		// Route::get('deleteonly/{cid}','PostController@delete_comment');// admin/post/delete_?post_id=

	});

	Route::group(array('prefix' => 'tag'), function () {
		Route::any('/', array('as' => 'tag_admin', 'uses' => 'TermsController@tag_admin')); //	admin/category
		Route::any('create', 'TermsController@tag_create'); // admin/tag/create
		Route::any('delete/{tid}', 'TermsController@tag_delete'); // admin/tag/delete/tid
		Route::any('batchdelete', 'TermsController@tag_batch_delete');
		Route::any('update/{tid}', 'TermsController@tag_update'); // admin/category/create
	});

	Route::group(array('prefix' => 'image'), function () {
		Route::any('/', array('as' => 'img_admin', 'uses' => 'ImgController@admin')); //	admin/category
		Route::any('upload', 'ImgController@post_img_upload'); // admin/image/upload
		Route::any('delete/{iid}', 'ImgController@delete'); // admin/tag/delete/tid
	});

// 	Route::any('single/{post_id}',array('as' => 'singlepost','uses'=>'PostController@single')); // /{term_id}/{post_date?}',
	// 	// post/single/id
	// 	Route::any('create/{param}','PostController@create');
	// 	// /post/create/do?post_title=testposttitle1&post_content=testcontent&category=1&post_tag_ids=46,44,47
	// 	// post/delete?post_id=
	//

});

//图片
Route::group(array('prefix' => 'img'), function () {
	Route::any('post/content/upload', 'ImgController@post_img_upload'); // img/post/content/upload
	Route::any('post/cover/upload', 'ImgController@post_cover_upload'); // img/post/cover/upload
	Route::any('post/cover/cut', 'ImgController@post_cover_cut'); // img/post/cover/cut
	Route::any('post/cover/save', 'ImgController@post_cover_save'); // img/post/cover/save

});



//Comment CRUD API
//Route::group(array('prefix' => 'api'), function () {
//	Route::resource('comments', 'CommentController',
//		array('only' => array('index', 'store', 'destroy')));
//});



//Terms
Route::group(array('prefix' => 'term'), function () {
	Route::group(array('prefix' => 'api'), function () {
		Route::any('chkname', 'TermsController@chk_term_name_exist');
		// http://www.lblog.com/term/api/chkname?term_name=TEST1

	});
});

Route::group(array('prefix' => 'category'), function () {
	//API
	Route::group(array('prefix' => 'api'), function () {
		// http://www.lblog.com/category/api/create?new_catagory_name=cat&new_category_parent=23
		Route::any('create', 'TermsController@create_category_api');


	});
	//
	Route::any('create/{param}', 'TermsController@create');
});

/**
 * Tag api
 */
Route::group(array('prefix' => 'tag'), function () {
	//API
	Route::group(array('prefix' => 'api'), function () {
		// http://www.lblog.com/tag/api/create?new_tag_name=cat
		Route::any('create', 'TermsController@create_tag_api');

	});
});


Route::get('term/admin', 'TermsController@admin');
Route::get('/term/delete', 'TermsController@delete'); //term/delete?tid=

Route::get('/test', 'TestController@test');
/*
//测试用
Route::get('/test/put', 'TestController@put');
Route::get('/test/get', 'TestController@get');
Route::get('/test/push', 'TestController@push');
Route::get('/test/mail', 'TestController@sendmail');
*/

//错误
Route::any('/error/{msg}', array('as' => 'error', 'uses' => 'ErrorController@show'));

//URL not exist
App::missing(function ($exception) {
	return Redirect::route('index');
	//return Redirect::action('PostController@index');
});

//Event::listen('comment.create', function($comment) {
//	$user_model = new User;
//
////	$user_model->
////	$user->last_login = new DateTime;
////	$user->save();
//});
//
//
//
