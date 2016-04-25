<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  *
 *  The controller is loading our developed library
 *  linkedin (Wrapper)
 *  Next the following process will occur in the loaded library.
 *   1 – get oauth token & oauth token secret so that the user
 *        will be redirected to a LinkeIn UI permission conformation
 *        window to approve our requested permission.
 *   2 – If user confirms the permissions we requested
 *        the method onSuccess is called with the
 *        oauth token & oauth token secret as $_GET parameters.
 *        the  tokens be stored as session  parameters.
 *        Else we cannot proceed querying LinkedIn and the onFailure.
 *
 * Now we can access the LinkIn resources using the retrieved tokens.
 * Here are the methods that query LinkIn resources :
 *   me () – Get the Info of the User who confirmed the permissions
 *   connections ()  - Get the above user connection records JSON
 *                     formatted
 *   company () – We just gave an example how to retrieve any company
 *                 by  company id we got from results or query company
 *                 id by company id or search criteria
 *   company_updates () – Let us get the latest updates of this
 *                        company
 */

class Linkedinfo extends CI_Controller {

	/*
	 *  Controller constructor
	 *
	 *  Checks if session variables are set: oauth_token, oauth_token_secret
	 *    If they are set then it initializes the oauth consumer
	 *    else it call method auth() to start the procees of setting the token
	 *    It also loads linkedin library
	 */
	public function __construct() {

		parent::__construct();

		$this->load->helper('url');

		$linked_config = array(
			// Application keys registered in Linkedin developer app
			'api_key' => 'esq76cosbm9x',
			'secret_key' => 'TyUQ2FzRRzWz9bHk',

			// The url to return from Linkedin confirmation URL
			'callback_url' => base_url() . 'linkedinfo/on_success',

			// The URL when failure occurs
			'on_failure_url' => 'linkedinfo/on_failure'
		);

		// Load linkedin library
		$this->load->library('linkedin_handler', $linked_config) ;
	}


	/*
	 *  Load main menu of the application
	 */
	public function index() {
		$this->load->view('linkedin-menu') ;
	}


	/*
	 *  The URL that LinkeIn UI permission conformation
	 *  returns to
	 *
	 *  Calls library linkedin_handler to
	 *  handle the token settings
	 */
	public function on_success() {
		$this->linkedin_handler->on_success();
	}


	/*
	 *  In case authentication failed error
	 *  message page is displayed
	 */
	public function on_failure() {
		$this->load->view('linkedin-notoken-on-failure') ;
	}


	/*
	 *  This function calls library method me to get
	 *  the linkedin user's details
	 */
	public function me() {
		// Get Linkedin user's details
		$s_json = $this->linkedin_handler->me() ;
		$o_my_details = json_decode( $s_json) ;
		$prodile_url = $o_my_details->siteStandardProfileRequest->url ;

		$view_params['my_details'] = $o_my_details ;
		$view_params['profile_url'] = $prodile_url ;

		// Load the view for displaying Linkedin user's details
		$this->load->view('linkedin-me', $view_params) ;
	}


	/*
	 *  This function calls library method me to get
	 *  the linkedin user's connections
	 */
	public function connections() {
		// Get Linkedin user's connections
		$s_json = $this->linkedin_handler->connections() ;
		$o_json = json_decode($s_json) ;

		// Processing the data received from linkedin library
		$a_connections = array() ;
		for ($index = 0; $index < $o_json->_count; $index++)  {
			if ($o_json->values[$index]->id == 'private') {
				continue ;
			}

			if ( isset($o_json->values[$index]->pictureUrl) ) {
				$picture_url = $o_json->values[$index]->pictureUrl;
			} else {
				$picture_url = '' ;
			}

			$a_connections[] = array(
				'picture_url' => $picture_url,
				'name' =>$o_json->values[$index]->firstName . " ". $o_json->values[$index]->lastName,
				'headline' => $o_json->values[$index]->headline,
				'industry' =>  $o_json->values[$index]->industry,
				'url' => $o_json->values[$index]->siteStandardProfileRequest->url
			) ;
		}

		$view_params['connections_count'] = $o_json->_total ;
		$view_params['connections'] = $a_connections ;

		// Load the view for displaying Linkedin user's connections
		$this->load->view('linked-connections', $view_params) ;
	}


	/*
	 *  This function calls library method me to get
	 *  the Linkedin company's details
	 *
	 *  @param $company_id integer - Linkedin company id
	 */
	public function companies($company_id) {
		// Get Linkedin company's details
		$s_json = $this->linkedin_handler->company($company_id) ;
		$o_company_details = json_decode( $s_json) ;

		$a_company_details = array (
			'id' => $company_id,
			'name' => $o_company_details->name,
			'specialties' => $o_company_details->specialties->values,
			'websiteUrl' => $o_company_details->websiteUrl,
			'employeeCountRange' => $o_company_details->employeeCountRange->name,
			'foundedYear' => $o_company_details->foundedYear,
			'numFollowers' => $o_company_details->numFollowers
		);

		// Load the view for displaying Linkedin company's details
		$view_params = $a_company_details;
		$this->load->view('linked-company', $view_params) ;
	}


	/*
	 *  This function calls library method me to get
	 *  the Linkedin company's updates
	 *
	 *  @param $company_id integer - Linkedin company id
	 */
	public function company_updates($company_id) {
		// Get Linkedin company's updates
		$s_json = $this->linkedin_handler->company_updates($company_id) ;
		$o_company_updates = json_decode( $s_json) ;

		// Processing the data received from linkedin library
		$a_updates = array();
		$a_json_updates = $o_company_updates->values;
		for ($index = 0; $index < count($a_json_updates); $index++) {
			$o_update = $a_json_updates[$index];

			if (isset($o_update->updateContent->companyJobUpdate)) {
				$a_updates[] = array(
					'type'  => 'Job Update',
					'postion' => $o_update->updateContent->companyJobUpdate->job->position->title,
					'url' =>  $o_update->updateContent->companyJobUpdate->job->siteJobRequest->url
				);
			}
		}

		// Load the view for displaying Linkedin company's updates
		$view_params['updates'] = $a_updates;
		$this->load->view('linked-company-updates', $view_params) ;
	}
}  //  class closing tags

/* End of file linkedinfo.php */
/* Location: ./application/controllers/linkedinfo.php */