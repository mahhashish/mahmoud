<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Display extends Flower {

    private $tablename;

    public function __construct($tablename) {
        $this->tablename = $tablename;
        $this->connectToDb();
    }

    function getContents() {

        $sql = 'SELECT `title`,`header` FROM content';
        $query = $this->cxn->cxn->prepare($sql);
        $query->execute();
        $data = $query->fetch();

        return $data;
    }

}
