<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* use CI built In libarries  
*/

class Builtins extends CI_Controller{
    
    function __construct(){
        parent::__construct();
    
	 // Load the table library that generates HTML tags for showing table struction within a view :
	 $this->load->library('table');
	}
	
    public function index(){
        
      // Load the users list into the view 
        $data['users'] = $this->db->get('users');
		  
        // Create custom header for the table 
        $header = array('id', 'User Name', 'Hashed Password', 'Position' );
		// Set the headings
        $this->table->set_heading($header);
		
		// Set formatting 
		$table_format = array ( 'table_open'  => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">' );
		$this->table->set_template($table_format); 

        // Load the view and send the results
        $this->load->view('users_view', $data);
    }
} 