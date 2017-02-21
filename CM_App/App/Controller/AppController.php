<?php

namespace App\Controller;


use Core\Controller\Controller;


class AppController extends  Controller
{
    protected $viewPath;
    protected $template = 'default';
    public function __construct()
    {
        parent::__construct();
    }

    protected function User($key){
        if(!isset($_SESSION['user'])){
            return null;
        }
        $user = $_SESSION['user'];
        if(!isset($user->$key)){
            return null;
        }
        return $user->$key;
    }


}