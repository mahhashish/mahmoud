<?php

class FeedsController extends \BaseController {

	//The method to show the form to add a new feed
	public function getCreate() {
		//We load a view directly and return it to be served
		return View::make('create_feed');
	}

	//Processing the form
	public function postCreate(){

		//Let's first run the validation with all provided input
		$validation = Validator::make(Input::all(),Feeds::$form_rules);

		//If the validation passes, we add the values to the database and return to the form 
		if($validation->passes()) {
			//We try to insert a new row with Eloquent
			$create = Feeds::create(array(
				'feed'		=> Input::get('feed'),
				'title'		=> Input::get('title'),
				'active'	=> Input::get('active'),
				'category'	=> Input::get('category')
			));

			//We return to the form with success or error message due to state of the 
			if($create) {
				return Redirect::to('feeds/create')
					->with('message','The feed added to the database successfully!');
			} else {
				return Redirect::to('feeds/create')
					->withInput()
					->with('message','The feed could not be added, please try again later!');
			}
		} else {
			//If the validation does not pass, we return to the form with first error message as flash data
			return Redirect::to('feeds/create')
					->withInput()
					->with('message',$validation->errors()->first());

		}
	}


	public function getIndex(){
		//First we get all the records that are active category by category:
		$news_raw 	= Feeds::whereActive(1)->whereCategory('News')->get();
		$sports_raw	= Feeds::whereActive(1)->whereCategory('Sports')->get();
		$technology_raw = Feeds::whereActive(1)->whereCategory('Technology')->get();


		//Now we load our view file and send variables to the view
		return View::make('index')
			->with('news',$news_raw)
			->with('sports',$sports_raw)
			->with('technology',$technology_raw);

	}
	

}