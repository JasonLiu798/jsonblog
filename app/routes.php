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

//Post
Route::get('/', 'PostController@index');
Route::get('/post/{post_id}','PostController@single');
//Comment
Route::post('/comment/create','CommentController@create');

