<?php
/**
Auth controller for login and setting Admin / User to their main menus
*/

class Auth extends CI_Controller{

	function __construct(){
		parent::__construct();		
		$this->load->helper('form');
		$this->load->model ('users_model');  
	}

	//--------------------------------------------------- 
	function index()
	{
	  $this->login();
	}
	//----------------------------------------------------
	function login()
	{ 
		$msg = ""; 
		
		// If the method called from a form submission we will check its validity :
		if( $this->input->post('password') )
		{	 
		// check login :
		$stat = $this->check_login ();
		$msg = $stat ['msg' ]; // will show any login faults if any   
		
			if( $stat['result'] == 'ok' ) 
			{  /* Successful login Cases :
				  admin_user 	/ regular user 
			    */			           		 
				if ( $this->session->userdata ('role') == 
				     'admin_user' 
					)
			      // Issue controller for Admin User Main Menu  
				  redirect('auth/admin_main_menu');   						  
			    else  
				  // Issue controller for Regular User Main Menu  
				  redirect('auth/user_main_menu' ); 
				  
				return; 	
			}
		}
		else 
		{
		// if the page rendered with no submission let's destroy any previous session 
        // and challange again the user :		
		$this->session->sess_destroy();
		
		}
		
		$view_setup ['msg'] = $msg; 
		$this->load->view('login_view.php', $view_setup );
	
	}
	
	//--------------------------------------------
	function check_login () {
	$user_name = $this->input->post('user_name');
	$password  = $this->input->post('password');	
	
	$ret = array (); 
	// Check if login is ok and get the $row:
	$user_record = $this->users_model->check_login ($user_name, $password);
	
	if ($user_record ) {
	// Set the user id and category 	
	$this->session->set_userdata ('user_id', 	$user_record->id   );
	$this->session->set_userdata ('user_name', 	$user_record->user_name );
	$this->session->set_userdata ('role',    	$user_record->role );
	
	$ret ['result'] = 'ok' ;
	$ret ['msg' ]   = 'Logged-in!'; 
	}
	else {
	$ret ['result'] = 'notok' ;
	$ret ['msg' ]   = 'Invalid User/Pass - Try Again!'; 
	}
	
	return $ret; 
	
	}

	function logout() {
	$this->session->sess_destroy();
	redirect('auth'); 
	}
	
	
	function admin_main_menu () {
	
     $view_setup ['uid']  		=  $this->session->userdata ('user_id' );
	 $view_setup ['user_name']  =  $this->session->userdata ('user_name' );
	 $view_setup ['role'] 		=  $this->session->userdata ('role' );
	 	
	 $view_setup ['menu'] =  "Add User/Modify User/Delete User";
	 $this->load->view('logged_in_view.php',$view_setup);
	}
	
	function user_main_menu () {
	
     $view_setup ['uid'] 		   	=  $this->session->userdata ('user_id' );
	 $view_setup ['user_name']  	=  $this->session->userdata ('user_name' );
	 $view_setup ['role'] 			=  $this->session->userdata ('role' );
	 
	 $view_setup ['menu'] 		   	=  "View Content/Modify Your Account/Logout";
	
	 $this->load->view('logged_in_view.php',$view_setup);

	}
}

