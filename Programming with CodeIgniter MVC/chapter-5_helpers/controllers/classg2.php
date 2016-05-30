<?php 

class Classg2 extends CI_Controller {
	public function index()
	{	 
		$this->load->helper('url');
		
		$this->load->view('classg2view');
	}
	
	function download() 
	{
		// Loading the helpers url, my_download
		$this->load->helper(array('url', 'my_download'));
		
		// FCPATH is a constant that Codeigniter sets which contains the absolute path to index.php 
		$fullPath = FCPATH . 'files/movie-classg2.wmv';
		
		// Using the helper my_download function to download a very large file
		download_large_files($fullPath);
	}
}	

/* End of file classg2.php */
/* Location: ./application/controllers/classg2.php */