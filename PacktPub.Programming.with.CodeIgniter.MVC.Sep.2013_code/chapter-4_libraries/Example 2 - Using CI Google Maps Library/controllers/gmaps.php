<?php
/**
* Operate The Google Maps CI Library Wrapper 
*/
class Gmaps extends CI_Controller
{
	function __construct()
	{  parent::__construct();
		$this->load->library('googlemaps');
		
		// Set the map window sizes: 
		$config['map_width'] 	= "1000px";  // map window width 
		$config['map_height'] 	= "1000px";  // map window height 
	
		$this->googlemaps->initialize($config);
	}
	
	function index() 
	{		
	/*  
	 Initialize and setup Google Maps for our App starting with 3 marked places :
	 London, UK,  Bombai, India, Rehovot, Israel
	*/
    // Initialize our map for this use case of show 3 places altogther. 
	// let the zoom be automatically decided by Google for showin the several places on one view :	
	$config['zoom'] 		= "auto";   
	$this->googlemaps->initialize($config);
	
   // Defined the plaes we want to see marked on Google Map! 
	$this->add_visual_flag ('London, UK');
	$this->add_visual_flag ('Bombai, India');
	$this->add_visual_flag ('Rehovot, Israel');
	
	$data = $this->load_map_setting (); 
	// Load our view, passing the map data that has just been created
	$this->load->view('google_map_view', $data); 
	
   }
   
   function london() 
	{		
	// Initialize our map. Here you can also pass in additional parameters for customising the map (see below)
	
	// Create the map. This will return the Javascript to be included 
	// in our pages <head></head> section and the HTML code to be
	$config['center'] = 'London, UK';  // Define the address we want to be on the map center 
	$config['zoom'] = "16";  // City Level Zoom 

	$this->googlemaps->initialize($config);

   // Add visual flag  
	$this->add_visual_flag ($config['center']); 
	
	$data = $this->load_map_setting (); 
	// Load our view, passing the map data that has just been created
	$this->load->view('google_map_view', $data); 
	}
   
   
    function bombai() 
	{		
	// Initialize our map. Here you can also pass in additional parameters for customising the map (see below)
	
	// Create the map. This will return the Javascript to be included 
	// in our pages <head></head> section and the HTML code to be
	$config['center'] = 'Bombai, India';  // Define the address we want to be on the map center 
	$config['zoom'] = "16";  // City Level Zoom 
	
	$this->googlemaps->initialize($config);

   // Add visual flag  
	$this->add_visual_flag ($config['center']); 
	
	$data = $this->load_map_setting (); 
	// Load our view, passing the map data that has just been created
	$this->load->view('google_map_view', $data); 
   
   }
   
    function rehovot() 
	{		
	// Initialize our map. Here you can also pass in additional parameters for customising the map (see below)
	
	// Create the map. This will return the Javascript to be included 
	// in our pages <head></head> section and the HTML code to be
	$config['center'] = 'Rehovot, Israel';  // Define the address we want to be on the map center 
	$config['zoom'] = "16";  // City Level Zoom 
	
	$this->googlemaps->initialize($config);

	// Add visual flag  
	$this->add_visual_flag ($config['center']); 
	
	$data = $this->load_map_setting (); 
	// Load our view, passing the map data that has just been created
	$this->load->view('google_map_view', $data); 
   }
   
   
   
	function load_map_setting ( ) {
	
		$data 			= array();
		$locations 		= array();
		$controllers 	= array();
	
		// Set controllers list for zoom in 
		$locations[]  	 = 'London, UK';
		$locations[]     = 'Bombai, India';
		$locations[]     = 'Rehovot, Israel'; 
	
	    // Set controllers list for zoom in 
		$controllers[]   = "london";
		$controllers[]   = "bombai";
		$controllers[]   = "rehovot";
	
		$data['map'] = $this->googlemaps->create_map();
		$data['locations'] 	 = $locations;
		$data['controllers'] = $controllers;
	
		$data['map'] = $this->googlemaps->create_map();
	
	
	return $data;
	}
	
    function add_visual_flag ( $place ) {
	$marker 		= array();
	// Setup Marker for the place and the title as the place name 
	$marker['position'] = $place;
	$marker['title'] 	= $place;
	$this->googlemaps->add_marker($marker);
	}
   
}