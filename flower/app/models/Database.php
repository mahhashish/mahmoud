<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Database {

    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    public $cxn;

    function __construct($filename) {
        if (is_file($filename)) {
            include "$filename";
        } else {
            throw new Exception("Error: Not connected!");
        }
        $this->db_host = $host;
        $this->db_name = $database;
        $this->db_user = $user;
        $this->db_pass = $password;
        $this->connect();
    }

    private function connect() {
//connect to the server
        try {
            $this->cxn = new PDO("mysql:host=$this->db_host;dbname=$this->db_name", $this->db_user, $this->db_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            die();
        }
    }

    function __destruct() {
        unset($this->cxn);
        $this->cxn = null;
    }
}
