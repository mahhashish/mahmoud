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
	
	//------------------------------------------------------------------------
	function get_logged_in_user (  )
    {
     // Will check if there's a login user asession and if so will fetch its record 	
	   $ci = &get_instance();
	   
	/// get the loogin in uid if amy :
		$uid =  $this->session->userdata('user_id');
	
	  if (! $uid ) return NULL;  
	 
	
		$sql = "SELECT *  
				FROM   users
				WHERE  id = '$uid'  
			   "; 

        $q = $ci->db->query($sql); 
		
        if  ( $q->num_rows() )
        {
            foreach ($q->result() as $row ) 
			return $row; 
		}
			return NULL;
	}
	
	
	//------------------------------------------------------------------------
	function get_user_rec ( $uid )
    {
     // Will check if there's a login user asession and if so will fetch its record 	
	   $ci = &get_instance();
	   
	/// get the loogin in uid if amy :
	
	  if (! $uid ) return NULL;  
	 
	
		$sql = "SELECT *  
				FROM   users
				WHERE  id = '$uid'  
			   "; 

        $q = $ci->db->query($sql); 
		
        if  ( $q->num_rows() )
        {
            foreach ($q->result() as $row ) 
			return $row; 
		}
			return NULL;
	}
	
	//------------------------------------------------------------------------
	function keep_user_feedback ( $feedback ) {	
	   $ci = &get_instance();
	   
	   $uid_rec = $this->get_logged_in_user (); 
	   $uid     = $uid_rec ? $uid_rec->id  : 0; 
	
	/* id 	email  	uid feedback timestamp 	
	*/
		$table = 'user_feedback';
		$data  = array ( 'feedback'  		=>  urldecode ($feedback), 
                         'uid'  	  		=>  $uid
                 	    );
		$ci->db->insert($table, $data);
	}
	//------------------------------------------------------------------------
	function get_user_feedbacks ( $uid ) {	
	   $ci = &get_instance();
	   
	  if (! $uid ) return NULL;  
	
	/* id 	email  	uid feedback timestamp 	
	*/
	    $feedbacks = array(); 
		
		$table = 'user_feedback';
		$sql = "SELECT *  
				FROM   $table
				WHERE  uid = '$uid' 
				ORDER BY timestamp DESC
			   "; 

        $q = $ci->db->query($sql); 
		
        if  ( $q->num_rows() )
        {
            foreach ($q->result() as $row ) 
			$feedbacks[] =  $row; 
		}
			return $feedbacks;
	}
	
}