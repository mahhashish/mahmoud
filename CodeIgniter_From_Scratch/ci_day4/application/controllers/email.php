<?php

/**
* SENDS EMAIL WITH GMAIL
*/
class Email extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function index() 
	{
		$this->load->view('newsletter');
	}
	
	function send() 
	{	
		$this->load->library('form_validation');
		
		// field name, error message, validation rules
		$this->form_validation->set_rules('name', 'Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('newsletter');
		}
		else
		{
			// validation has passed. Now send the email
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			
			$this->load->library('email');
			$this->email->set_newline("\r\n");

			$this->email->from('nettutsplustutorials@gmail.com', 'Jeffrey Way');
			$this->email->to($email);		
			$this->email->subject('Test Newsletter Signup Confirmation');		
			$this->email->message('You\'ve now signed up, fool!');

			$path = $this->config->item('server_root');
			$file = $path . '/ci_day4/attachments/newsletter1.txt';

			$this->email->attach($file);

			if($this->email->send())
			{
				//echo 'Your email was sent, fool.';
				$this->load->view('signup_confirmation_view');
			}

			else
			{
				show_error($this->email->print_debugger());
			}			
		}
	}
}


      