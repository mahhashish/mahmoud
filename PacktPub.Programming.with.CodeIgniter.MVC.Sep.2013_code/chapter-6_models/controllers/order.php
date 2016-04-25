<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller {

	public function index()
	{
		// Loading the url helper
		$this->load->helper('url');

		// Manualy loading the database
		$this->load->database();

		// Loading the model class
		$this->load->model('productmodel');

		$view_params['products'] = $this->productmodel->get_products();

		$this->load->view('orderview', $view_params);
	}

	public function product($product_id)
	{
		// Loading the url helper
		$this->load->helper('url');

		// Manualy loading the database
		$this->load->database();

		// Loading the model class
		$this->load->model('productmodel');

		 if (!$this->productmodel->update_quantity($product_id)) {
			mail('yudazdk@gmail.com', 'product ' .  $product_id  .  " reached it's limit", 'Order product ' . $product_id);
		 }

		 redirect('product');
	}
}