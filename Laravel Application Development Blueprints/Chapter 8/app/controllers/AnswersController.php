<?php

class AnswersController extends \BaseController {


	/**
	 * Adds a reply to the questions
	**/
	public function postReply($id,$title) {

		//First, let's check if the question id is valid
		$question = Question::find($id);

		//if question is found, we keep on processing
		if($question) {

			//Now let's run the form validation
			$validation = Validator::make(Input::all(), Answer::$add_rules);

			if($validation->passes()) {

				//Now let's create the answer
				Answer::create(array(
					'questionID'	=> $question->id,
					'userID'		=> Sentry::getUser()->id,
					'answer'		=> Input::get('answer')
				));

				//Finally, we redirect the user back to the question page with a success message
				return Redirect::route('question_details',array($id,$title))
					->with('success','Answer submitted successfully!');

			} else {
				return Redirect::route('question_details',array($id,$title))
					->withInput()
					->with('error',$validation->errors()->first());
			}

		} else {
			return Redirect::route('index')
				->with('error','Question not found');
		}

	}


	/**
	 * Vote AJAX Request
	**/
	public function getVote($direction, $id) {

		//request has to be AJAX Request
		if(Request::ajax()) {
			$answer = Answer::find($id);
			//if the answer id is valid
			if($answer) {
				//new vote count
				if($direction == 'up') {
					$newVote = $answer->votes+1;
				} else {
					$newVote = $answer->votes-1;
				}
				
				//now the update
				$update = $answer->update(array(
					'votes'	=> $newVote
				));

				//we return the new number
				return $newVote;
			} else {
				//answer not found
				Response::make("FAIL", 400);
			}
		} else {
			return Redirect::route('index');
		}
	}

	


	/**
	 * Chooses a best answer
	**/
	public function getChoose($id) {

		//First, let's check if there is an answer with that given ID
		$answer = Answer::with('questions')->find($id);

		if($answer) {
			//Now we should check if the user who clicked is an admin or the owner of the question 
			if(Sentry::getUser()->hasAccess('admin') || $answer->userID==Sentry::getUser()->id) {
				//First we should unmark all the answers of the question from correct (1) to incorrect (0)
				Answer::where('questionID',$answer->questionID)
					->update(array(
						'correct'	=> 0
					));

				//And we should mark the current answer as correct/best answer
				$answer->update(array(
					'correct'	=> 1
				));

				//And now let's return the user back to the questions page
				return Redirect::route('question_details',array($answer->questionID, Str::slug($answer->questions->title)))
					->with('success','Best answer chosen successfully');
			} else {
				return Redirect::route('question_details',array($answer->questionID, Str::slug($answer->questions->title)))
					->with('error','You don\'t have access to this attempt!');
			}

		} else {
			return Redirect::route('index')
				->with('error','Answer not found');
		}
		
	}

	/**
	 * Deletes an answer
	**/
	public function getDelete($id) {

		//First, let's check if there is an answer with that given ID
		$answer = Answer::with('questions')->find($id);

		if($answer) {
			//Now we should check if the user who clicked is an admin or the owner of the question 
			if(Sentry::getUser()->hasAccess('admin') || $answer->userID==Sentry::getUser()->id) {

				//Now let's delete the answer
				$delete = Answer::find($id)->delete();
				
				//And now let's return the user back to the questions page
				return Redirect::route('question_details',array($answer->questionID, Str::slug($answer->questions->title)))
					->with('success','Answer deleted successfully');
			} else {
				return Redirect::route('question_details',array($answer->questionID, Str::slug($answer->questions->title)))
					->with('error','You don\'t');
			}

		} else {
			return Redirect::route('index')
				->with('error','Answer not found');
		}
	}

}