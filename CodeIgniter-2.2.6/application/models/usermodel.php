<?php

class Usermodel extends CI_Model {
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	public function get_users() 
	{
		$query = $this->db->get('users');

		return $query->result();
	}
}