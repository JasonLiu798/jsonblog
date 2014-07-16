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

//Route::get('/', 'PostController@index');

// Route::get('/', function()
// {
// 	//return 'Heool';//View::make('hello');
// 	return Redirect::action('PostController@index');
// 	//return 'Hello World';
// });



Route::get('/', 'PostController@index');
Route::get('/post/{post_id}','PostController@single');



/*function($id)
{
	return 'User '.$id;
});
*/
/*
Route::get('/', function()
{
	//return View::make('hello');
	//$results = DB::select('select * from posts');///, array(1))；
	//var_dump($results->post_title);
	
	$url = URL::action('PostController@index');
	return $url;//$results[0]->post_title;//"HelloWorld!";
});


Route::get('foo/bar', function()
{
    return View::make('hello');
});*/
