<?php

//We define a RESTful controller and all its via route directly
Route::controller('subscribers', 'SubscribersController');


//This code will trigger the push request
Route::get('queue/process',function(){

	Queue::push('SendEmail');

	return 'Queue Processed Successfully!';
});

//When the push driver sends us back, we will have to marshall and process the queue. 
Route::post('queue/push',function(){
	return Queue::marshal();
});

//When the queue is pushed and waiting to be marshalled, we should assign a Class to make the job done 
Class SendEmail {

	public function fire($job,$data) {

		//We first get the all data from our subscribers database using Eloquent ORM
		$subscribers = Subscribers::all(); 

		foreach ($subscribers as $each) {

			//Now we send an email to each subscriber
			Mail::send('emails.test', array('email' => $each->email), function($message){

			    $message->from('us@oursite.com', 'Our Name');

			    $message->to($each->email);

			});
		}
		

		$job->delete();
	}
}