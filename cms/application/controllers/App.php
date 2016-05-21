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
    }

    public function index() {
        $this->load->view('welcome_message');
    }

}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * load form & validator & library AND main_model model
 * 
 *
*
if (!isset(sess{username})) {
if (>post){
if (isset($_POST['submit']) AND $_POST['submit'] == "Login"){
validate username & password from the form
check_auth() and if OK
    session_start();
    $_SESSION['username'] = $username;
    header('Location:app');
}elseif (isset($_POST['submit']) AND $_POST['submit'] == "SET") {
validate data
add_data() and return app/success_veiw if OK
}else{
echo 'un-official operation';
}

}else{
view>login_view
}
}











} else {
    load>main_view
}
*/