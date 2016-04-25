<?php
/**
Users Model 

Load : 
    $this->load->model  ('users_model');

Usage : 
    $user_rec = $this->users_model->check_login ($user, $pass);

	
*/

class Users_model  extends CI_Model  {
    
	//-----------------------
	function __construct()
    {
        parent::__construct();	
    }
    //-----------------------
	function check_login ($user, $pass)
    {
	
    	$ci = &get_instance();
	    
		$md5_pass = md5($pass); 
		
//	id 	user_name 	password

		$sql = "SELECT *  
				FROM   users
				WHERE  user_name = '$user'  
				AND    password  = '$md5_pass' "; 

        $q = $ci->db->query($sql); 
		
        if  ( $q->num_rows() )
        {
            foreach ($q->result() as $row ) 
			return $row; 
	    }
		
			return NULL;
    }
	
}