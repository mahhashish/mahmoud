<?php

class UsersController extends BaseController{

	public function postLogin()
	{
		Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')));
		return View::make('addpost');
	}
	
	public function getLogout()
	{
		Auth::logout();
		return Redirect::route('index');
	}

}