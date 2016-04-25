<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_handler extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		/* Standard Libraries: database & helper url are in the auto load */
	
		 if (!$this->input->is_ajax_request())  
         {
		   exit( "Bad Request ignored! - Your info has been logged for further investigation of attacking the site!"); 
         }		 
		 
		/* ------User Defined------------ */	
		$this->load->model  ( 'users_model' );
		
	}
	
	/* -------------------------------------------------------- */	
	function save_user_feedback () {
	    // Get the feedback content 
		$feedback 	= $this->input->post( 'feedback' );
		// Get if the user is logged in keep the user id 
		$this->users_model->keep_user_feedback ( $feedback );
	}
	/* -------------------------------------------------------- */	
	function get_user_feedback_log () {
	    
		$user = $this->users_model->get_logged_in_user (); 
		if ( $user )  $uid = $user->id; 
		
		$user_feedback_rows = $this->users_model->get_user_feedbacks ( $uid ); 

		$html = ''; 
		foreach ($user_feedback_rows as $row )
        	$html .= $row->timestamp.' -  <B>'.$row->feedback.'</B><BR/>';
        
		
		$result = array ( 'result' => $html );
		echo json_encode ($result); 
		return; 
	}
	
}