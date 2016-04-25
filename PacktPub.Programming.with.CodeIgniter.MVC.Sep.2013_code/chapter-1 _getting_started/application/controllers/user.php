<?php 

class User extends CI_Controller {
	
	function __construct(){
        parent::__construct();
    }
	
	
	public function users()
	{
		
		// Manualy loading the database
		$this->load->database();

		// Loading the model class
		$this->load->model('usermodel');
		
		$view_params['mega_title'] = 'Model Example';
		
		// Calling the model to retrieve the users from the database
		$view_params['users'] = $this->usermodel->get_users();
		
		$this->load->view('userview', $view_params);
	}
}

/* End of file user.php */
/* Location: ./application/controllers/user.php */