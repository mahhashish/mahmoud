<?php

class Answer extends Eloquent {

	//The relation with users
	public function users() {
    	return $this->belongsTo('User','userID');
    }

    //The relation with questions
    public function questions() {
    	return $this->belongsTo('Question','questionID');
    }

    //table name
	protected $table = 'answers';

	//which fields can be filled
	protected $fillable = array('questionID', 'userID', 'answer', 'correct', 'votes');

	//Answer Form Validation Rules
	public static $add_rules = array(
		'answer'	=> 'required|min:10'
	);

}