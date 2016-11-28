<?php
Class Order extends Eloquent {

protected $table = 'orders';

protected $fillable = array('member_id','address','total');

public function orderItems(){

    return $this->belongsToMany('Book','order_books')->withPivot('amount','price','total');

}
public function User(){

	return $this->belongsTo('User','member_id');
}

}
