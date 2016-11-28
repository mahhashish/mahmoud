<?php
class Actor extends Eloquent {

protected $table = 'actors';

protected $fillable = array('name');

public function Movies(){

    	return $this-> belongsToMany('Movies', 'pivot_table');
}

}
