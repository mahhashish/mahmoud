<?php

/** Use The Google Maps CI Library Wrapper for several
  marked places altogether and zoom-in */
class gmaps extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('googlemaps');
        $this->load->helper('url');
// Set the map window sizes:
        $config['map_width'] = "1000px";
// map window width
        $config['map_height'] = "1000px";
// map window height
        $this->googlemaps->initialize($config);
    }

    function index() {
        /* Initialize and setup Google Maps for our App starting
          with 3 marked places
          London, UK, Bombai, India, Rehovot, Israel
         */
// Initialize our map for this use case of show 3
// places altogether.
// let the zoom be automatically decided by Google for showing
// the several places on one view.
        $config['zoom'] = "auto";
        $this->googlemaps->initialize($config);
//Define the places we want to see marked on Google Map!
        $this->add_visual_flag('London, UK');
        $this->add_visual_flag('Bombai, India');
        $this->add_visual_flag('Rehovot, Israel');
        $data = $this->load_map_setting();
// Load our view, passing the map data that has just been
//created.
        $this->load->view('google_map_view', $data);
    }

//The class Gmaps continued with several more functions as
//follows:
    function london() {
// Initialize our map
//Here you can also pass in additional parameters for
// customizing the map (see the following code:)
// Define the address we want to be on the map center
        $config['center'] = 'London, UK';
//to be on the map center
// Set Zoom Level - Zoom 0: World – 18 Street Level
        $config['zoom'] = "16";
        $this->googlemaps->initialize($config);
// Add visual flag
        $this->add_visual_flag($config['center']);
        $data = $this->load_map_setting();
// Load our view passing the map data that has just been
//created
        $this->load->view('google_map_view', $data);
    }

    function Bombay() {
//Initialize our map.
//Here you can also pass in additional parameters for
//customizing the map (see the following code)
//Define the address we want to see as the map center
        $config['center'] = 'Bombay, India';
        $config['zoom'] = "16"; // City Level Zoom
        $this->googlemaps->initialize($config);
// Add visual flag
        $this->add_visual_flag($config['center']);
        $data = $this->load_map_setting();
// Load our view passing the map data that has just been created
        $this->load->view('google_map_view', $data);
    }

//class Gmaps continues with several more functions as follows:
    function rehovot() {
// Initialize our map.
//Here you can also pass in additional parameters for
//customizing the map (see the following code)
        $config['center'] = 'Rehovot, Israel';
        $config['zoom'] = "16";
// City Level Zoom
        $this->googlemaps->initialize($config);
// Add visual flag
        $this->add_visual_flag($config['center']);
        $data = $this->load_map_setting();
// Load our view, passing the map data that has just been
//created.
        $this->load->view('google_map_view', $data);
    }

    function load_map_setting() {
        $data = array();
        $locations = array();
        $controllers = array();
// Set controllers list for zoom in
        $locations[] = 'London, UK';
        $locations[] = 'Bombai, India';
        $locations[] = 'Rehovot, Israel';
// Set controllers list for zoom in
        $controllers[] = "london";
        $controllers[] = "bombay";
        $controllers[] = "rehovot";
        $data['locations'] = $locations;
        $data['controllers'] = $controllers;
        $data['map'] = $this->googlemaps->create_map();
        return $data;
    }

//The class Gmaps continues with several more functions as follows:
    function add_visual_flag($place) {
        $marker = array();
// Setup Marker for the place and the title as the place name
        $marker['position'] = $place;
        $marker['title'] = $place;
        $this->googlemaps->add_marker($marker);
    }

}
