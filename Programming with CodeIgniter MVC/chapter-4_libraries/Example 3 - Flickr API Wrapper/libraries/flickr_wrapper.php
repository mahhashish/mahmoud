<?php 
 if (!defined('BASEPATH')) exit('No direct script access allowed');   

/**
 * CodeIgniter Flickr API wrapper Library Class
 *
 * Enable Simple Flickr API usage 
 *
 * @package        	CodeIgniter
 * @category    	Libraries
 * @author        	Eli Orr - http://eliorr.com 

 Usage:
 
  Via CI controller : 
 
  $this->load->library( 'flickr_wrapper', 
						       array(   'api_key'     => '<YOUR_FLICKR_API_KEY>',
							            'DEFAULT_RES' => '3000',  // filter 3000 pix 
										'GPS_ENABLED' => FALSE 
									) 
							);
	
  $this->flickr_wrapper->set_params ( $keyed_array ); 

  $recent_photos 	= $this->flickr_wrapper->flickrPhotosGetRecent ();
  
  $filter_photos 	= $this->flickr_handler->filter_photos ($photos_to_filter); 
  
  $user_info        = $this->flickr_wrapper->flickrUserInfo ($uid); // $uid e.g. 72095130@N00   

  
  //....... PRIVATE ................... 
 
  private function _file_get_contents_curl($url); 
  private function _flickrRestAPI ($params); 
  private function _is_filtered_photo ($photo_rec ); 

 */
 
 
class Flickr_wrapper {

    // filters 
  	private $DEFAULT_RES       		= 2000;   // Width in Pixels 
	private $GPS_ENABLED         	= TRUE;   // Filter if Photo Exif shall have GPS 
	private $RECENT_PHOTOS       	= 500;   // how many in each poll ?
	// CI instance 
	private $CI; 
	// Flickr api_key to use 
	private $api_key = "" ; 
	
	//-----------------------------------------
	function __construct( $params = array()  )
	{
	if (!isset ($params['api_key'])) 
	      exit ('FATAL - flickr_handler must be constructed with api_key!'); 
	$this->set_params ($params);  
	
	error_reporting(E_ALL);
    ini_set('display_errors', '1');
	}
	//-----------------------------------------
	function set_params ( $key_array ) {
	// sets array of setup params 
	 foreach ($key_array as $key => $val ){ 
		switch ($key) {
	    case 'DEFAULT_RES'      	: $this->DEFAULT_RES 	 		= $val; break; 
		case 'GPS_ENABLED' 		    : $this->GPS_ENABLED 		    = $val; break;
		case 'RECENT_PHOTOS'        : $this->RECENT_PHOTOS          = $val; break;
		case 'api_key'              : $this->api_key                = $val; break;
        // We can add many more here...
		
		default                     : exit ( "FATAL! - flickr_handler - set_params invalid param: $key" );  
		}
	 }	
	}

	//------------------------------------------
	function flickrPhotosGetRecent () {
	
	#
	# build the Params for API:
	#
 
		$params = array(
		'api_key'	=> $this->api_key,
		'method'	=> 'flickr.photos.getRecent',
		'extras'  	=> 'o_dims,owner_name,date_taken,media,path_alias,url_sq,geo',
		'per_page'  => $this->RECENT_PHOTOS, 
    	'format'	=> 'php_serial'
	  );
	$rsp_obj = $this->_flickrRestAPI ($params);  
	#
	# display the photo title (or an error if it failed)
	#	
					
	if ($rsp_obj['stat'] == 'ok'){
	#  Get the  array of all the photo records in this cycle    
		return  $recent_photos = $rsp_obj['photos']['photo']; 
	}
	else 
	#  Query failed    
	   return NULL; 
	}
	
	//------------------------------
	function GetPhotoExif ($photo_id) {
	#
	# build the API URL to call
	#
		$params = array(
		'api_key'	=> $this->api_key,
		'method'	=> 'flickr.photos.getExif',
		'photo_id'	=> $photo_id,
		'format'	=> 'php_serial',
	  );
	$rsp_obj = $this->_flickrRestAPI ($params); 
	#
	# display the photo title (or an error if it failed)
	#
	if ($rsp_obj['stat'] == 'ok') {
   /*
   Array ( [photo] => 
            Array (  [id] => 8002716747 
			         [secret] => 559f87aea0 
					 [server] => 8030 
					 [farm] => 9 
					 [camera] => Casio EX-H20G 
					 [exif] => ... A LOT OF EXTRA INFO ...
   
   */
   

	$photo_camera = $rsp_obj['photo']['camera']; 
	// We can add more interesting itesms for our app here ... 
	
	$params = array ( 'camera'    => $photo_camera,  
	                  'full_exif' => $rsp_obj              // All EXIF info 
	                ); 
				return $params;  
		}else // Request Failed - We shall return error: 
		{
				return NULL;   
		}
    }
   	
	//------------------------------------------
	function filter_photos ($photos) {
		$filtered_photos = array(); 
	
		foreach            ($photos  as $photo) {
		if ( $this->_is_filtered_photo ($photo) ) 
				   $filtered_photos[] = $photo; 
		}    
	  return $filtered_photos; 
	}
	
	
	 //-----------------------------------------------------------
	function flickrUserInfo ($uid) {
	// UID e.g. : 72095130@N00   
	// find info for this User  
	#
	# build the API URL to call
	#
		 $params = array(
		 'api_key'	=> $this->api_key,
		 'method'	=> 'flickr.people.getInfo',
		 'user_id' 	=> $uid,
		 'extras'  	=> 'contact,friend,family',
		 'format'	=> 'php_serial',
	    );

	 $rsp_obj = $this-> _flickrRestAPI ($params); 
	 
	#
	# Check if response isOK 
	#
				
	if ($rsp_obj['stat'] == 'ok'){
	
	$real_name   	= @urlencode($rsp_obj['person']['realname']['_content']); 
	$location       = @urlencode (strtolower ($rsp_obj['person']['location']['_content'])); 
	$photos         = @$rsp_obj['person']['photos']['count']['_content']; 
	// ... more can be added ....
	
	$params = array ( 'name'   		 => $real_name,  
	                  'uid' 		 => $uid,
					  'photos' 		 => $photos, 
					  'location'     => $location, 
					  'full_info'    => $rsp_obj
					 ); 
	
	
				return $params;  
		}else{ // Response failed return NULL
				
				return NULL;   
		}
   }
    	


	// PRIVATE SECTION //////////////
	//------------------------------
	private function _flickrRestAPI ($params)  {
		$encoded_params = array();
		foreach ($params as $k => $v){
		$encoded_params[] = urlencode($k).'='.urlencode($v);
		}
	#
	# call the API and decode the response
	#
	$url = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
	$rsp = $this->_file_get_contents_curl($url);	
	return $rsp_obj = unserialize($rsp);
	}
	
	//-----------------------------------------
	private function _file_get_contents_curl($url) {
                    
	if (! function_exists('curl_init') ) exit ('PHP curl library is not enabled please fix!'); 
	
					  $ch = curl_init();
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
    }
	//------------------------------------------
	private function _is_filtered_photo ($photo_rec ) {
	/*
	[o_width]   => 4416 
	[latitude] => 0 
	... 
	More can be added 
	*/
	
	// Photo width >  $this->DEFAULT_RES ?
	 if (   (int) (@$photo_rec['o_width'] )  < 
	        (int)  $this->DEFAULT_RES    )	return FALSE;
	 // GPS info required & Found ?
	 if (( $this->GPS_ENABLED && ! @$photo_rec['latitude'] )) 
											return FALSE;
	 return TRUE;	 
	}	
	
}
