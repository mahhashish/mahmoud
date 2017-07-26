<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('main_model');
    }

    public function index() {
        $data = array();
        $this->load->helper('url');
        $data['info'] = $this->main_model->get_data();
        $this->load->view('site/index_view', $data);
    }

}
