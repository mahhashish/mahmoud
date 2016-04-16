<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Flower {

    //class attr
    protected $cxn;  // databse object => connection to Mysql

    //class methods or functions

    function connectToDb() {
        //require_once MODELS.'Database.php';
        $vars = "/app/includes/vars.php";
        $this->cxn = new Database($vars);
    }
}
