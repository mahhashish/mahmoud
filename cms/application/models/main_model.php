<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * 
 * check_auth
 * -get_info
 * add_data
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class main_model extends CI_Model {

    public function __construct() {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->database();
    }

    public function get_info() {
        $this->db->select('title,header');
        $this->db->limit(1);
        $query = $this->db->get('data');
        if ($query->num_rows()) {
            $row = array();
            foreach ($query->result_array() as $row) {
                return $row;
            }
            $query->free_result();
        }
        return FALSE;
    }

}
