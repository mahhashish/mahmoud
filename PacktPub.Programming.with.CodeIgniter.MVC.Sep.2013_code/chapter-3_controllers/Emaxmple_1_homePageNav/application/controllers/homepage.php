<?php

/**
* SENDS EMAIL WITH GMAIL
*/
class Homepage extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper  ('validators_helper'); 
		$this->load->helper  ('dates_time_helper'); 
	
	}
	
	function index() 
	{
     // get the curremt date: 

	 $data = array (); 
	 $data ['email']   =  $email = "the@email..com"; 
	 $data ['email_valid'] 		 = isValidEmail($email); 
	
	 $data ['url'] = $url = "http://cnn.com"; 
	 $data ['url_valid']  = isValidURL($url ); 
	 $data ['url_exist']  = isURLExists($url ); 
		
	 $this->load->view('home_page_view', $data);	
	
	 	
	}

	function page_b () 
	{
     // get the curremt date: 

	$data = array (); 
 
    $myqsl_date  = "1970-01-01"; 
	$data ['since'] = ui_date ($myqsl_date);   
	$data ['past']  = getAgeAccurate ( $myqsl_date, $percision = 2 ); 
	 
	$this->load->view('page_b_view', $data);		
    }
	
	function test() 
	{
     // get the curremt date: 

	$now = now_date_time (); 
    $data = array (); 
	
	$data['date1'] = $date = "2012-11-02 11:08"; 
	$data['future1'] = isFuture ( $date ) ? "$date is future!" : "$date is past!";  
	$now = now_date_time  ( ); 
	
	echo "<!DOCTYPE html>
	      <html>
	      <meta content='text/html; charset=utf-8' />"; 
	echo $data['date1']." ".$data['future1']." NOW: ".$now;
	
	
     	
	
	
	//$this->load->view('page_c_view', $data);		
	
   }
	
}


      