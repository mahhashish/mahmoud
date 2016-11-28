<?php
class Post extends Eloquent {

	//the variable that sets the table name
    protected $table = 'posts';

    //the variable that sets which columns can be edited
    protected $fillable = array('title','content','author_id');

    //The variable which enables or disables the Laravel's timestamps option. Default is true. We're leaving this here anyways
    public $timestamps = true;

    public function Author(){

    	return $this->belongsTo('User','author_id');
    }
}
