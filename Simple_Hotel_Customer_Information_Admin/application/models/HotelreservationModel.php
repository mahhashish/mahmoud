<?php

class HotelReservationModel extends CI_Controller 
{
	public function index()
	{
		$this->load->view('home');
	}
	public function view_all()
	{
		$query = $this->db->get('customer');
		return $query->result();
	}
	public function add()
	{
		$data = array(
			'firstname' => $this->input->post('fn'),
			'mi' => $this->input->post('mi'),
			'lastname' => $this->input->post('ln'),
			'Email'=>$this->input->post('em'),
			'Phone'=> $this->input->post('con')
		);
		$this->db->insert('customer', $data);
	}
	public function search($result)
	{
		$this->db->where('lastname',$result);
		$query = $this->db->get('customer');
		return $query->result();
	}
	public function update($cid)
	{
		$customerData = array(
		'firstname' => $this->input->post('fname'),
			'mi' => $this->input->post('min'),
			'lastname' => $this->input->post('lname'),
			'Email'=>$this->input->post('email'),
			'Phone'=> $this->input->post('contact')	
		);
		$this->db->where('cid', $cid);
		$this->db->update('customer',$customerData);
	}
}
