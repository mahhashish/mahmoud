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


Route::get('/', array('as' => 'index', 'uses' => 'PostsController@getIndex'));
Route::get('/admin', array('as' => 'admin_area', 'uses' => 'PostsController@getAdmin'));
Route::post('/add', array('as' => 'add_new_post', 'uses' => 'PostsController@postAdd'));
Route::post('/login', array('as' => 'login', 'uses' => 'UsersController@postLogin'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'UsersController@getLogout'));
