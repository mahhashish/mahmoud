<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller {

	private function load_form($form_action, $a_values = array())
	{
		// Loading the form helper
		$this->load->helper('form');

		// Loading the form_validation library
		$this->load->library('form_validation');

		$view_params['form']['attributes'] = array('id' => 'productform') ;
		$view_params['form']['action'] = $form_action;

		$product_id = isset($a_values['product_id']) ? $a_values['product_id'] : 0;
		$view_params['form']['hidden_fields'] = array('product_id' => $product_id);

		// product name details
		$view_params['form']['product_name']['label'] = array('text' => 'Product name:', 'for' => 'product_name');
		$view_params['form']['product_name']['field'] = array (
			'name'        => 'product_name',
			'id'          => 'product_name',
			'value'       => isset($a_values['product_name']) ? $a_values['product_name'] : '',
			'maxlength'   => '100',
			'size'        => '30',
			'class'       => 'input'
		);

		// product sku details
		$view_params['form']['product_sku']['label'] = array('text' => 'Product SKU:', 'for' => 'product_sku');
		$view_params['form']['product_sku']['field'] = array (
			'name'        => 'product_sku',
			'id'          => 'product_sku',
			'value'       => isset($a_values['product_sku']) ? $a_values['product_sku'] : '',
			'maxlength'   => '100',
			'size'        => '30',
			'class'       => 'input'
		);

		// product quantity details
		$view_params['form']['product_quantity']['label'] = array('text' => 'Product Quantity:', 'for' => 'product_quantity');
		$view_params['form']['product_quantity']['field'] = array (
			'name'        => 'product_quantity',
			'id'          => 'product_quantity',
			'value'       => isset($a_values['product_quantity']) ? $a_values['product_quantity'] : '',
			'maxlength'   => '100',
			'size'        => '30',
			'class'       => 'input'
		);

		$config_form_rules = array(
			array('field' => 'product_name',  'label' => 'Product Name',  'rules' => 'trim|required'),
			array('field' => 'product_sku', 'label' => 'Product SKU', 'rules' => 'trim|required'),
			array('field' => 'product_quantity', 'label' => 'Product Quantity', 'rules' => 'trim|required|integer')
		);
		$this->form_validation->set_rules($config_form_rules);

		return $view_params;
	}

	public function index()
	{
		// Loading the url helper
		$this->load->helper('url');

		// Manualy loading the database
		$this->load->database();
		
		// Loading the model class
		$this->load->model('productmodel');
		
		// Calling the model to retrieve the products from the database
		$view_params['products'] = $this->productmodel->get_products();

		$this->load->view('productsview', $view_params);
	}

	public function add()
	{
		// Loading the url helper
		$this->load->helper('url');

		// Manualy loading the database
		$this->load->database();

		// Loading the model class
		$this->load->model('productmodel');

		$a_post_values = $this->input->post();
		$view_params = $this->load_form('product/add', $a_post_values);

		// Validating the form
		if ($this->form_validation->run() == FALSE) { // VAlidation failed
			$this->load->view('productform', $view_params);
		}  else {
			$data = $a_post_values;
			array_pop($data);
			$this->productmodel->addProduct($data);

			redirect('product');
		}
	}

	public function edit($product_id)
	{
		// Loading the url helper
		$this->load->helper('url');

		// Manualy loading the database
		$this->load->database();

		// Loading the model class
		$this->load->model('productmodel');

		$a_post_values = $this->input->post();

		// Checking if a form was sumitted
		if ($a_post_values) {
			$a_form_values = $a_post_values;
		}   else {
			// Get the values of the database
			$a_db_values =  $this->productmodel->get_product($product_id);
			$a_form_values = array(
				'product_id'                        =>  $a_db_values[0]->product_id,
			      'product_name'                  =>  $a_db_values[0]->product_name,
				'product_sku'                     =>  $a_db_values[0]->product_sku,
				'product_quantity'      =>  $a_db_values[0]->product_quantity
			);
		}

		$view_params = $this->load_form('product/edit/' . $product_id, $a_form_values);

		// Validating the form
		if ($this->form_validation->run() == FALSE) { // VAlidation failed
			$this->load->view('productform', $view_params);
		}  else {
			$a_fields = array('product_name', 'product_sku', 'product_quantity');
			 for ($index = 0; $index < count($a_fields); $index++) {
				 $s_field = $a_fields[$index];
				 $data[$s_field] = $this->input->post($s_field);
			 }

			$this->productmodel->updateProduct($product_id, $data);

			redirect('product');
		}
	}
}

/* End of file product.php */
/* Location: ./application/controllers/product.php */