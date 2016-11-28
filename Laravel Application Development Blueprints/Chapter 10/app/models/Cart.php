<?php
Class Cart extends Eloquent {

protected $table = 'carts';

protected $fillable = array('member_id','book_id','amount','total');

public function Books(){

	return $this->belongsTo('Book','book_id');

}

}
