<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class main_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_data() {
        $this->db->select('title,header');
        $this->db->order_by('id', 'DESC');
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

    public function add_data($title, $header) {
        $data = array('title' => $title, 'header' => $header);

        $str = $this->db->insert_string('data', $data);
        $this->db->query($str);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function check_auth($username, $password) {
        $this->db->select('username,password');
        $query = $this->db->get('auth');
        if ($query->num_rows()) {
            return TRUE;
        }
        return FALSE;
    }

}
