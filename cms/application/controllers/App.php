<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $libraries = array('session', 'form_validation');
        $this->load->library($libraries);
        $helpers = array('form', 'url');
        $this->load->helper($helpers);
        $this->load->model('main_model');
    }

    public function index() {
        if (!$this->session->has_userdata('username')) {
            if ($this->input->post()) {
                if ($this->input->post('submit') && $this->input->post('submit') == 'Login') {
                    /* validate username & password from the form
                      check_auth() and if OK
                      session_start();
                      $_SESSION['username'] = $username;
                      header('Location:app'); */
                    $this->form_validation->set_rules('username', 'Username', 'required|max_length[50]|min_length[6]', array(
                        'required' => 'You must provide a %s',
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
                    if ($this->form_validation->run() == TRUE) {
                        $username = $this->input->post('username');
                        $password = $this->input->post('password');
                        if ($this->main_model->check_auth($username, $password)) {
                            $this->session->set_userdata($this->input->post('username'));
                            $this->load->view('app/main_view');
                        } else {
                            $this->load->view('app/login_view');
                        }
                    } else {
                        $this->load->view('app/login_view');
                    }
                } elseif ($this->input->post('submit') && $this->input->post('submit') == 'SetData') {


                    $this->form_validation->set_rules('title', 'Title', 'required|max_length[50]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 50 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    $this->form_validation->set_rules('header', 'Header', 'required|max_length[150]|min_length[6]', array(
                        'required' => 'You must provide a %s.',
                        'max_length' => 'Max length for %s is 150 characters',
                        'min_length' => 'Min length for %s is 6 characters'
                            )
                    );
                    if ($this->form_validation->run() == TRUE) {
                        $title = $this->input->post('title');
                        $header = $this->input->post('header');
                        if ($this->main_model->add_data($title, $header)) {
                            $this->load->view('app/success_view');
                        } else {
                            echo '1';
                            $this->load->view('app/main_view');
                        }
                    } else {
                        $this->load->view('app/main_view');
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
