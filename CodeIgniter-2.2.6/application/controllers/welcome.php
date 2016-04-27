<?php 

class welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	/*function __construct(){
        parent::__construct();
    }*/
	
	public function index()
	{	    
		$view_params = array('mega_title' => 'Codeigniter - Hello World', 'title' => 'Welcome to Codegniter', 'message' => "Hello World");
		
		// Note that $view_params is optional
        // you can use $this->load->view('helloview');
        // if the view doesn't use php variables		
		// The $view_params is extracted in the view script
		// to php variables $key = $value.
		// In this example 3 variables will be generated by CI in
		// the view page helloview.php: 
		//                             variable: $mega_title value: 'Codeigniter - Hello World'
		//                             variable: $title      value: 'Welcome to Codegniter'
		//                             variable: $message    value: 'Hello World'
		$this->load->view('helloview', $view_params);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */