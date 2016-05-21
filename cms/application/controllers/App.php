<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct() {
        parent::__construct();
        $libraries = array('session', 'form_validation');
        $this->load->library($libraries);
        $helpers = array('form', 'url');
        $this->load->helper($helpers);
    }

    public function index() {


        /*
         * To change this license header, choose License Headers in Project Properties.
         * To change this template file, choose Tools | Templates
         * and open the template in the editor.
         * 
         * load form & validator & library AND main_model model
         * 
         *
         */
        if (!isset($_SESSION['username'])) {
            if ($this->input->post()) {
                if ((NULL !== $this->input->post('submit')) && $this->input->post('submit') == 'Login') {
                    /* validate username & password from the form
                      check_auth() and if OK
                      session_start();
                      $_SESSION['username'] = $username;
                      header('Location:app'); */
                    $this->form_validation->set_rules('username', 'Username', 'required|[50]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 50 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    $this->form_validation->set_rules('password', 'Password', 'required|max_length[50]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 50 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    if ($this->form_validation->run() == FALSE) {
                        $this->load->view('myform');
                    } else {
                        $this->load->view('formsuccess');
                    }
                } elseif (NULL !== ($this->input->post('submit')) && $this->input->post('submit') == 'SetData') {


                    $this->form_validation->set_rules('title', 'Title', 'required|[50]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 50 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    $this->form_validation->set_rules('header', 'Header', 'required|max_length[50]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 50 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    if ($this->form_validation->run() == TRUE) {
                        if ($this->main_model->add_data($username, $password)) {
                            $this->load->view('app/success_view');
                        } else {
                            $this->load->view('app/main_view', $err);
                        }
                    } else {
                        $this->load->view('app/main_view', $err);
                    }
                } else {
                    $this->session->sess_destroy();
                }
            } else {
                $this->load->view('app/login_view');
            }
        } else {
            $this->load->view('app/main_view');
        }
    }

}
