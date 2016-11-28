<?php

class AuthController extends \BaseController {

	/**
	 * Signup GET method
	**/
	public function getSignup() {
		return View::make('qa.signup')
			->with('title','Sign Up!');
	}


	/**
	 * Signup Post Method
	**/
	public function postSignup() {

		//Let's validate the form first
		$validation = Validator::make(Input::all(),User::$signup_rules);

		//let's check if the validation passed
		if($validation->passes()) {

			//Now let's create the user with Sentry 2's create method
			$user = Sentry::getUserProvider()->create(array(
		        'email'       => Input::get('email'),
		        'password'    => Input::get('password'),
		        'first_name'  => Input::get('first_name'),
		        'last_name'   => Input::get('last_name'),
		        'activated'   => 1
		    ));

		    //Since we don't use an email validation in this example, let's log the user in directly
		    $login = Sentry::authenticate(array('email'=>Input::get('email'),'password'=>Input::get('password')));

		    return Redirect::route('index')
		    	->with('success','You\'ve signed up and logged in successfully!');

		//if the validation failed, let's return the user 
		//to the signup form with the first error message
		} else {
			return Redirect::route('signup_form')
				->withInput(Input::except('password','re_password'))
				->with('error',$validation->errors()->first());
		}
	}


	/**
	 * Login Post Method Resource
	**/
	public function postLogin() {
		//let's first validate the form:
		$validation = Validator::make(Input::all(),User::$login_rules);

		//if the validation fails, return to the index page with first error message
		if($validation->fails()) {
			return Redirect::route('index')
				->withInput(Input::except('password'))
				->with('topError',$validation->errors()->first());
		} else {

			//if everything looks okay, we try to authenticate the user
			try {
			    
			    // Set login credentials
			    $credentials = array(
			        'email'    => Input::get('email'),
			        'password' => Input::get('password'),
			    );

			    // Try to authenticate the user, remember me is set to false
			    $user = Sentry::authenticate($credentials, false);

			    //if everything went okay, we redirect to index route with success message
			    return Redirect::route('index')
			    	->with('success','You\'ve successfully logged in!');

			} catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','Login field is required.');
			} catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','Password field is required.');
			} catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','Wrong password, try again.');
			} catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','User was not found.');
			} catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','User is not activated.');
			}

			// The following is only required if throttle is enabled
			catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
				return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','User is suspended.');
			} catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
			    return Redirect::route('index')
					->withInput(Input::except('password'))
					->with('topError','User is banned.');
			}
		}
	}


	/**
	 * Logout method 
	**/
	public function getLogout() {
		//we simply log out the user
		Sentry::logout();

		//then, we return to the index route with a success message
		return Redirect::route('index')
			->with('success','You\'ve successfully signed out');

	}

}