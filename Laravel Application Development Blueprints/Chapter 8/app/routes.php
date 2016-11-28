<?php

//Auth Resource
Route::get('signup',array('as'=>'signup_form', 'before'=>'is_guest', 'uses'=>'AuthController@getSignup'));
Route::post('signup',array('as'=>'signup_form_post', 'before'=>'csrf|is_guest', 'uses'=>'AuthController@postSignup'));

Route::post('login',array('as'=>'login_post', 'before'=>'is_guest', 'uses'=>'AuthController@postLogin'));

Route::get('logout',array('as'=>'logout', 'before'=>'user', 'uses'=>'AuthController@getLogout'));

//---- Q & A Resources
Route::get('/',array('as'=>'index','uses'=>'MainController@getIndex'));

//Question asking
Route::get('ask',array('as'=>'ask', 'before'=>'user', 'uses'=>'QuestionsController@getNew'));
Route::post('ask',array('as'=>'ask_post', 'before'=>'user|csrf', 'uses'=>'QuestionsController@postNew'));

//Upvoting and Downvoting
Route::get('question/vote/{direction}/{id}',array('as'=>'vote', 'before'=>'user', 'uses'=>'QuestionsController@getVote'))->where(array('direction'=>'(up|down)', 'id'=>'[0-9]+'));


//Question tags page
Route::get('question/tagged/{tag}',array('as'=>'tagged','uses'=>'QuestionsController@getTaggedWith'))->where('tag','[0-9a-zA-Z\-\_]+');

//Question's permalink
Route::get('question/{id}/{title}',array('as'=>'question_details','uses'=>'QuestionsController@getDetails'))->where(array('id'=>'[0-9]+','title'=>'[0-9a-zA-Z\-\_]+'));

//Reply Question:
Route::post('question/{id}/{title}',array('as'=>'question_reply','before'=>'csrf|user', 'uses'=>'AnswersController@postReply'))->where(array('id'=>'[0-9]+','title'=>'[0-9a-zA-Z\-\_]+'));
//Admin Question Deletion
Route::get('question/delete/{id}',array('as'=>'delete_question','before'=>'access_check:admin','uses'=>'QuestionsController@getDelete'))->where('id','[0-9]+');

//Answer upvoting and Downvoting
Route::get('answer/vote/{direction}}/{id}',array('as'=>'vote_answer', 'before'=>'user', 'uses'=>'AnswersController@getVote'))->where(array('direction'=>'(up|down)', 'id'=>'[0-9]+'));

//Choosing best Answer
Route::get('answer/choose/{id}',array('as'=>'choose_answer','before'=>'user','uses'=>'AnswersController@getChoose'))->where('id','[0-9]+');

//Deleting an answer
Route::get('answer/delete/{id}',array('as'=>'delete_answer','before'=>'user','uses'=>'AnswersController@getDelete'))->where('id','[0-9]+');



/**
* This method is to create an admin once.
* Just run it once, and then remove or comment it out.
**/
/*Route::get('create_user',function(){

	$user = Sentry::getUserProvider()->create(array(
        'email'       => 'admin@admin.com',
         //password will be hashed upon creation by Sentry 2
        'password'    => 'password',
        'first_name'  => 'John',
        'last_name'   => 'Doe',
        'activated'   => 1,
        'permissions' => array (
            'admin' => 1
        )
    ));
    return 'admin created with id of '.$user->id;
});*/