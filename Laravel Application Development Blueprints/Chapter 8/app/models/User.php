<?php

//use Illuminate\Auth\UserInterface;
//use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Cartalyst\Sentry\Users\Eloquent\User {

	public function questions() {
        return $this->hasMany('Question','userID');
    }

	public static $signup_rules = array(
		'first_name'	=> 'required|min:2',
		'last_name'		=> 'required|min:2',
		'email'			=> 'required|email|unique:users,email',
		'password'		=> 'required|min:6',
		're_password'	=> 'required|same:password'
	);

	public static $login_rules = array(
		'email'		=> 'required|email|exists:users,email',
		'password'	=> 'required|min:6'
	);

}