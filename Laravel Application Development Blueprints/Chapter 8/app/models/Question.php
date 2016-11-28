<?php

class Question extends Eloquent {

	public function users() {
    	return $this->belongsTo('User','userID');
    }

    public function tags() {
        return $this->belongsToMany('Tag','question_tags')->withTimestamps();
    }

    public function answers() {
    	return $this->hasMany('Answer','questionID');
    }

	protected $fillable = array('title', 'userID', 'question', 'viewed', 'answered', 'votes');

	public static $add_rules = array(
		'title'		=> 'required|min:2',
		'question'	=> 'required|min:10'
	);

}