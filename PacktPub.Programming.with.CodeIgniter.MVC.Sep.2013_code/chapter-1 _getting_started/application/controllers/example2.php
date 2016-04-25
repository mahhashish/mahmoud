<?php 

class Example2 extends CI_Controller {
	
	function __construct(){
        parent::__construct();
    }
	
	
	/**
	 *  This function gets parametrs and passes them to the
	 *  view example2more
     *  The example url http://yourdomain.com/index.php/example2/more/1/2/3 	 
	 *  so $a = 1, $b = 2, $c = 3
	 */
	public function more($a, $b, $c)
	{
		$rows = array('a' => $a, 'b' => $b, 'c' => $c);
		
		// The parameters in $view_params are extracted in the view example2more.php
		// In this example 2 variables will be generated by CI in
		// the view page example2more.php: 
		//                             variable: $mega_title value: Codeigniter - Passing url parameters to view
		//                             variable: $rows       value: array('a' => $a, 'b' => $b, 'c' => $c);
		$view_params = array('mega_title' => 'Codeigniter - Passing url parameters to view', 'rows' => $rows);
		$this->load->view('example2more', $view_params);
	}
} // closing the class definition

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */