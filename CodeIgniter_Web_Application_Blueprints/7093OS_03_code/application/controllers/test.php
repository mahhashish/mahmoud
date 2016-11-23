<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller {

    function __construct() {
    parent::__construct();    
    }
    
    function index() {
        $this->session->set_flashdata('flag_err','error happened while flaging');
        $this->load->view('test');
    }
}
