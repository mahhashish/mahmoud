<?php
/**
 * Flickr Recent Controller 
 *
 * Provide recent uploaded public photos in flickr community.
 * Enable to apply several settings and filtering  
 * Enable to get photographer user profile for each photo   
 * 
 * @author        	Eli Orr
*/

class Flickr_recent extends CI_Controller{
	
	function __construct()
	{
		parent::__construct();
		/* Standard Libraries: database & helper url are in the auto load */
		
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

	
		/* ------User Defined------------ */	
		$this->load->library( 'flickr_wrapper', 
						       array(   'api_key'     => '<YOUR_FLICKR_API>',
							            'DEFAULT_RES' => '3000',  // filter 3000 pix 
										'GPS_ENABLED' => FALSE 
									) 
							);
		
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		
	}
	
	function index () {
	
	$settings = array( 'DEFAULT_RES' 	  => '4000',  // Only 4000 pix and better 
					   'GPS_ENABLED' 	  =>  FALSE , // GPS Info is not mandatory  
					   'RECENT_PHOTOS'    =>  50     // Latest 100 photo uploads 
					);  
	
	$this->flickr_wrapper->set_params ( $settings );
	
	$photos_to_filter 	= $this->flickr_wrapper->flickrPhotosGetRecent ();
	
	$filter_photos 	= $this->flickr_wrapper->filter_photos ($photos_to_filter);
	
	$data = Array(); 
	
	$data['photos'] 	= $filter_photos; 
	$data['settings']  	= $settings; 
	
	$this->load->view('flickr_recent_view.php',$data );
	
	}
}	