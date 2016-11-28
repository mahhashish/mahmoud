<?php
class Image extends Eloquent {

protected $table = 'images';

protected $fillable = array('album_id','description','image');

public $timestamps = true;

}