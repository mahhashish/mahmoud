<?php

class QuestionsController extends \BaseController {

	/**
	 * A new question asking form
	**/
	public function getNew() {
		return View::make('qa.ask')
			->with('title','New Question');
	}

	/**
	 * Post method to process the form
	**/
	public function postNew() {

		//first, let's validate the form
		$validation = Validator::make(Input::all(), Question::$add_rules);

		if($validation->passes()) {

			//First, let's create the question
			$create = Question::create(array(
				'userID'	=> Sentry::getUser()->id,
				'title'		=> Input::get('title'),
				'question'	=> Input::get('question')
			));

			//We get the insert id of the question
			$insert_id = $create->id;

			//Now, we need to re-find the question to "attach" the tag to the question
			$question = Question::find($insert_id);


			//Now, we should check if tags column is filled, and split the string and add a new tag and a relation
			if(Str::length(Input::get('tags'))) {
				//let's explode all tags from the comma
				$tags_array = explode(',', Input::get('tags'));

				//if there are any tags, we will check if they are new, if so, we will add them to database
				//After checking the tags, we will have to "attach" tag(s) to the new question 
				if(count($tags_array)) {
					foreach ($tags_array as $tag) {
						//first, let's trim and get rid of the extra space bars between commas 
						//(tag1, tag2, vs tag1,tag2) 
						$tag = trim($tag);
						
						//We should double check its length, because the user may have just typed "tag1,,tag2" (two or more commas) accidentally
						//We check the slugged version of the tag, because tag string may only be meaningless character(s), like "tag1,+++//,tag2"
						if(Str::length(Str::slug($tag))) {
							//the URL-Friendly version of the tag
							$tag_friendly = Str::slug($tag);

							//Now let's check if there is a tag with the url friendly version of the provided tag already in our database:
							$tag_check = Tag::where('tagFriendly',$tag_friendly);

							//if the tag is a new tag, then we will create a new one
							if($tag_check->count() == 0) {
								$tag_info = Tag::create(array(
									'tag'			=> $tag,
									'tagFriendly'	=> $tag_friendly
								));

							//If the tag is not new, this means There was a tag previously added on the same name to another question previously
							//We still need to get that tag's info from our database 
							} else {
								$tag_info = $tag_check->first();
							}
						}

						//Now the attaching the current tag to the question
						$question->tags()->attach($tag_info->id);
					}
				}

			}

			//lastly, we should return the user to the asking page with a permalink of the question
			return Redirect::route('ask')
				->with('success','Your question has been created successfully! '.HTML::linkRoute('question_details','Click here to see your question',array('id'=>$insert_id,'title'=>Str::slug($question->title))));

		} else {
			return Redirect::route('ask')
				->withInput()
				->with('error',$validation->errors()->first());
		}
	}


	/**
	 * Vote AJAX Request
	**/
	public function getVote($direction,$id) {

		//request has to be AJAX Request
		if(Request::ajax()) {

			$question = Question::find($id);

			//if the question id is valid
			if($question) {

				//new vote count
				if($direction == 'up') {
					$newVote = $question->votes+1;
				} else {
					$newVote = $question->votes-1;
				}

				//now the update
				$update = $question->update(array(
					'votes'	=> $newVote
				));

				//we return the new number
				return $newVote;
			} else {
				//question not found
				Response::make("FAIL", 400);
			}
		} else {
			return Redirect::route('index');
		}
	}

	
	/**
	 * Details page
	**/
	public function getDetails($id,$title) {
		//First, let's try to find the question:
		$question = Question::with('users','tags','answers')->find($id);

		if($question) {

			//We should increase the "viewed" amount
			$question->update(array(
				'viewed' => $question->viewed+1
			));

			return View::make('qa.details')
				->with('title',$question->title)
				->with('question',$question);

		} else {
			return Redirect::route('index')
				->with('error','Question not found');
		}
	}

	/**
	 * Deletes the question
	**/
	public function getDelete($id) {
		//First, let's try to find the question:
		$question = Question::find($id);

		if($question) {
			//We delete the question directly
			Question::delete();
			//We won't have to think about the tags and the answers, ,
			//because they are set as foreign key and we defined them cascading on deletion, 
			//they will be automatically deleted
			
			//Let's return to the index page with a success message
			return Redirect::route('index')
				->with('success','Question deleted successfully!');
		} else {
			return Redirect::route('index')
				->with('error','Nothing to delete!');
		}
	}

	/**
	 * Shows the questions tagged with $tag friendlyURL
	**/
	public function getTaggedWith($tag) {

		$tag = Tag::where('tagFriendly',$tag)->first();

		if($tag) {
			return View::make('qa.index')
			->with('title','Questions Tagged with: '.$tag->tag)
			->with('questions',$tag->questions()->with('users','tags','answers')->paginate(2));
		} else {
			return Redirect::route('index')
				->with('error','Tag not found');
		}
	}


}