<?php

class Fbmodel extends CI_Model {
	private $token;

	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function set_token($token)
	{
		$this->token = $token;
	}

	public function get_user_profile()
	{
		$ci =& get_instance();

		$user_profile = $ci->facebook->api('/me');

		return $user_profile;
	}

	 public function get_me_by_fql($uid)
	 {
		 $ci =& get_instance();

		 $fql    =   "SELECT uid, name,pic_big  FROM user  WHERE uid=" . $uid;
		 $fql .= " LIMIT 0,10";
		 $param  =   array(
			 'method'    => 'fql.query',
			 'query'     => $fql,
			 'callback'  => ''
		 );

		 $fqlResult   =   $ci->facebook->api($param);

		 return $fqlResult;
	 }

	public function get_friends()
	{
		$ci =& get_instance();

		$friends = $ci->facebook->api('/me/friends');

		return $friends;
	}
}