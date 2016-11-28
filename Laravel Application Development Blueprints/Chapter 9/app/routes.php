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

Route::when('*', 'auth.basic');
Route::get('api/getactorinfo/{actorname}', array('uses' => 'ActorController@getActorInfo'));
Route::get('api/getmovieinfo/{moviename}', array('uses' => 'MovieController@getMovieInfo'));
Route::put('api/addactor/{actorname}', array('uses' => 'ActorController@putActor'));
Route::put('api/addmovie/{moviename}/{movieyear}', array('uses' => 'MovieController@putMovie'));
Route::delete('api/deleteactor/{id}', array('uses' => 'ActorController@deleteActor'));
Route::delete('api/deletemovie/{id}', array('uses' => 'MovieController@deleteMovie'));