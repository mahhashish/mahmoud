<?php
class Movie extends Eloquent {

protected $table = 'movies';

protected $fillable = array('name','release_year');

public function Actors(){

    	return $this-> belongsToMany('Actors' , 'pivot_table');
}

}