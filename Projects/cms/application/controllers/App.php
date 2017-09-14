<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $libraries = array('session', 'form_validation');
        $this -> load -> library($libraries);
        $helpers = array('form', 'url');
        $this -> load -> helper($helpers);
        $this -> load -> model('main_model');
    }

    public function index() {
        //$this -> load -> view('app/login_view');
    }

    public function Login() {
        if (!isset($_SESSION['username'])) {
            $this -> form_validation -> set_rules('username', 'Username', 'required', array('required' => 'You must provide a %s'));
            $this -> form_validation -> set_rules('password', 'Password', 'required', array('required' => 'You must provide a %s.'));
            if ($this -> form_validation -> run() == TRUE) {
                $username = $this -> input -> post('username', TRUE);
                $password = $this -> input -> post('password', TRUE);
                if ($this -> main_model -> check_auth($username, $password)) {
                    $this -> session -> set_userdata('username', $username);
                    $this -> load -> view('app/main_view');
                } else {
                    $data['err'] = "check your username or password";
                    $this -> load -> view('app/login_view', $data);
                }
            } else {
            $this -> load -> view('/app/login_view');
            }
        } else {
            redirect('/app/setdata','refresh');
        }
    }

    /*public function Login() {
     if (!isset($_SESSION['username'])) {
     $this -> form_validation -> set_rules('username', 'Username', 'required|max_length[50]|min_length[6]', array('required' => 'You must provide a %s', 'max_length' => 'Max length for %s is 50 characters', 'min_length' => 'Min length for %s is 6 characters'));
     $this -> form_validation -> set_rules('password', 'Password', 'required|max_length[50]|min_length[6]', array('required' => 'You must provide a %s.', 'max_length' => 'Max length for %s is 50 characters', 'min_length' => 'Min length for %s is 6 characters'));
     if ($this -> form_validation -> run() == TRUE) {
     $username = $this -> input -> post('username', TRUE);
     $password = $this -> input -> post('password', TRUE);
     if ($this -> main_model -> check_auth($username, $password)) {
     $this -> session -> set_userdata($this -> input -> post('username', TRUE));
     $this -> load -> view('app/main_view');
     } else {
     $data['err']= "check your username or password";
     $this -> load -> view('app/login_view',$data);
     }
     } else {
     $this -> load -> view('app/login_view');
     }
     } else {
     $this -> load -> view('app/login_view');
     }
     }*/

    public function Setdata() {
        if (isset($_SESSION['username'])) {
            $this -> form_validation -> set_rules('title', 'Title', 'required|max_length[50]|min_length[6]', array('required' => 'You must provide a %s.', 'max_length' => 'Max length for %s is 50 characters', 'min_length' => 'Min length for %s is 6 characters'));
            $this -> form_validation -> set_rules('header', 'Header', 'required|max_length[150]|min_length[6]', array('required' => 'You must provide a %s.', 'max_length' => 'Max length for %s is 150 characters', 'min_length' => 'Min length for %s is 6 characters'));
            if ($this -> form_validation -> run() == TRUE) {
                $title = $this -> input -> post('title', TRUE);
                $header = $this -> input -> post('header', TRUE);
                if ($this -> main_model -> add_data($title, $header)) {
                    $this -> load -> view('app/success_view');
                } else {
                    $this -> load -> view('app/main_view');
                }
            } else {
                $this -> load -> view('app/main_view');
            }
        } else {
            $this -> load -> view('app/login_view');
        }
    }

    public function logout() {
        $this -> session -> unset_userdata('username');
        $this -> session -> sess_destroy();
        redirect('/app', 'refresh');

    }

}
