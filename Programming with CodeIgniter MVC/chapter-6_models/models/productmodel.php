<?php

class Productmodel extends CI_Model {
	public function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	public function get_products() 
	{
		$query = $this->db->get('products');

		return $query->result();
	}

	public function get_product($product_id)
	{
		$this->db->select('*');
		$this->db->from('products');
		$this->db->where('product_id', $product_id);

		$query = $this->db->get();

		return $query->result();
	}

	public function addProduct($data)
	{
		$this->db->insert('products', $data);
	}

	public function updateProduct($product_id, $data)
	{
		$this->db->where('product_id', $product_id);
		$this->db->update('products', $data);
	}

	private function check_quantity($product_id) {
		$this->db->select('product_quantity');
		$this->db->from('products');
		$this->db->where('product_id', $product_id);

		$query = $this->db->get();

		$row = $query->row();
		if ($row->product_quantity < 7) {
			return false;
		} else {
			return true;
		}
	}

	public function update_quantity($product_id)
	{
		$sql = "UPDATE products SET product_quantity = product_quantity - 1 WHERE product_id=" . $product_id;

		$this->db->query($sql);

		// Checking if quantity reached it's limit
		if ($this->check_quantity($product_id)) {
			return true ;
		} else {
			return false;
		}
	}
}