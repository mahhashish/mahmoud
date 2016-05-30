<?php

class Helpersslexample extends CI_Controller {
	public function __construct() {
		parent::__construct();
			// Loading the ssl helper
			$this->load->helper('ssl');		
			// Enforce URI request of https 
			force_ssl();
		}
		
	/**
	 * Index Page for this controller.
	 *
 	 */
	public function index()
	{
		$this->load->helper('url');
		$this->load->view('helper-ssl-view');
	}
}
/* End of file helpersslexample.php */
/* Location: ./application/controllers/helpersslexample */