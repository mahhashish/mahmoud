<?php

class Sites extends Controller {

    function Sites()
    {
		parent::Controller();
    }
    
	function index($page = "0")
    {
		//First we load the library and the model
		$this->load->library('table');
		$this->load->model('sites_model');
		
		//Then we call our model's get_sites function
		$sites = $this->sites_model->get_sites();

		//If it returns some results we continue
		if ($sites['query']->num_rows() > 0){
		
			$table = array();		
			//table headers   
			$table[] = array('order','id','name','url','un','pw','client1','view','edit','delete');			

//initialize the order variable
$order = 0;
		
		   foreach ($sites['query']->result() as $row){
		   
			//with the row() function, we can indicate which row we want to get, this time we add 1 to the current one, thus obtaining the next record
			   $next = $sites['query']->row(($order)+1);	   
			// And now the previous one
			   $previous = $sites['query']->row(($order)-1);	
			//add 1 to the order variable for the next iteration
			   $order++;
			
			//If we are in the first record, only the down link will be created
				if($order == 1){
				
				//we create links to our order function, passing the needed parameters
					$order_line = anchor('sites/order/'.$row->site_order.'/'.$next->site_order.'/'.$row->id.'/'.$next->id, '>>');
				
				}else if($order == $sites['total_rows']){
				
				//if we are at the last record only the up link will be created
					$order_line = anchor('sites/order/'.$row->site_order.'/'.$previous->site_order.'/'.$row->id.'/'.$previous->id, '<<');
				
				}else{
				
				//for every other record, we will need up and down links
					$order_line = anchor('sites/order/'.$row->site_order.'/'.$previous->site_order.'/'.$row->id.'/'.$previous->id, '<<');
					$order_line .= "&nbsp;&nbsp;".anchor('sites/order/'.$row->site_order.'/'.$next->site_order.'/'.$row->id.'/'.$next->id, '>>');
				
				}	   
		   
				//we add our $order_line variable contents to each one of our records.
				$table[] = array($order_line,$row->id,$row->name,$row->url,$row->un,$row->pw,$row->client1, anchor('sites/view_site/'.$row->id, 'View'),anchor('sites/edit_site/'.$row->id, 'Edit'), anchor('sites/delete_site/'.$row->id, 'Delete',array('onclick' => 'return confirm(\'Are you sure you want to delete the record?\');')));
				
				}
		   
		$data['sites'] = $table; 		   
		}

		//Also we put into that array another variable, �heading� that we will use to echo the heading title
		$data['heading'] = "Sites admin";
		
		//Pagination code
		//load the pagination library
		$this->load->library('pagination');
		
		//the base url that will be used in the links
		$config['base_url'] = 'http://localhost/codeigniter/sites/index/';

		//the total number of records
		$config['total_rows'] = $sites['total_rows'];

		//how many records we want in each page
		$config['per_page'] = '5';		

		//we indicate in which segment the current page will be indicated, this time /sites/index/page, third position		
		$config['uri_segment'] = 3;
		
		//and then we initialize the library
		$this->pagination->initialize($config);	
		//***************
		//***************
		
		//Now we are prepared to call the view, passing all the necessary variables inside the $data array
		$this->load->view('sites/index', $data);

    }
	
	function order($current = '', $destiny = '', $current_id = '', $destiny_id = ''){
	
		$this->load->model('sites_model');
		
		//we call our model function
		$sites = $this->sites_model->change_order($current,$destiny,$current_id,$destiny_id);		
	
		redirect('sites/index', 'refresh');	
	}		
	
	function new_site(){
	
		//We are going to need the form helper
		$this->load->helper('form');
	
		//And then we load the view, �new� in this case
		$this->load->view('sites/new');
	
	}

	function add(){
		//we are going to use the session library to create some flash data
		$this->load->library('session');

		//Also we are going to need the sites model
		$this->load->model('sites_model');
		
		//Put the received data into an array.
		$data = array(
					   'name' => $_POST['name'],
					   'url' => $_POST['url'],
					   'un' => $_POST['un'],
					   'pw' => $_POST['pw'],
					   'client1' => $_POST['client1'],
					   'client2' => $_POST['client2'],
					   'admin1' => $_POST['admin1'],
					   'admin2' => $_POST['admin2'],
					   'domainid' => $_POST['domainidr'],
					   'hostid' => $_POST['hostid'],
					   'webroot' => $_POST['webroot'],
					   'files' => $_POST['files'],
					   'filesdate' => $_POST['filesdate'],
					   'lastupdate' => $_POST['lastupdate'],
					   'submit' => $_POST['submit'],
					);
		
		//We call the model add_site function, passing the created array as a parameter, we save returned data into the $sites variable.			
		$sites = $this->sites_model->add_site($data);
		
		//If data was inserted ok we create a new flashdata with an ok message
		if($sites){
			$this->session->set_flashdata('status', 'Data added ok<br/><br/>');
		}else{

			//But if something has gone wrong, we also need to tell that to our users
			$this->session->set_flashdata('status', 'Data was not added, please try again<br/><br/>');
		}

		//Then we redirect to the index page again
		redirect('sites/index', 'refresh');		
	}	
	
	function view_site($id = ''){	
		$this->load->model('sites_model');
		
		//we call our model's get_site function, we will create that function in a moment
		$site = $this->sites_model->get_site($id);
		
		$data['title'] = "Record view: ";
		//Returned data will be put into the $row variable that will be send to the view.
		$data['row'] = $site;
		
		$this->load->view('sites/view', $data);		
		
	}

	function edit_site($id = ''){	
		$this->load->helper('form');
		$this->load->model('sites_model');
		
		$site = $this->sites_model->get_site($id);
		
		$data['title'] = "Record edit: ";
		$data['row'] = $site;
		
		$this->load->view('sites/edit', $data);		
		
	}	
	
	function edit(){
		$this->load->library('session');
		$this->load->model('sites_model');	
	
		$data = array(
					   'name' => $_POST['name'],
					   'url' => $_POST['url'],
					   'un' => $_POST['un'],
					   'pw' => $_POST['pw'],
					   'client1' => $_POST['client1'],
					   'client2' => $_POST['client2'],
					   'admin1' => $_POST['admin1'],
					   'admin2' => $_POST['admin2'],
					   'domainid' => $_POST['domainid'],
					   'hostid' => $_POST['hostid'],
					   'webroot' => $_POST['webroot'],
					   'files' => $_POST['files'],
					   'filesdate' => $_POST['filesdate'],
					   'lastupdate' => $_POST['lastupdate'],
					   'submit' => $_POST['submit'],
					);	
			
		//Here we call a new model function, the edit_site one		
		$sites = $this->sites_model->edit_site($data, $_POST['id']);
		
		if($sites){
			$this->session->set_flashdata('status', 'Data updated ok<br/><br/>');
		}else{
			$this->session->set_flashdata('status', 'Data was not updated, please try again<br/><br/>');
		}

		redirect('sites/index', 'refresh');						
	}

	function delete_site($id = ''){
		$this->load->library('session');
		$this->load->model('sites_model');		
	
		$sites = $this->sites_model->delete_site($id);
		
		if($sites){
			$this->session->set_flashdata('status', 'Record deleted ok<br/><br/>');
		}else{
			$this->session->set_flashdata('status', 'Data was not deleted, please try again<br/><br/>');
		}
		
		redirect('sites/index', 'refresh');			
	
	}	
}