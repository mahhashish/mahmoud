<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * load form & validator & library AND main_model model
 * 
 */

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
