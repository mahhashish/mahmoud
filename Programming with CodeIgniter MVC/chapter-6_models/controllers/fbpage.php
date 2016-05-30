<?php

class Fbpage extends CI_Controller {

	public function __construct() {
		parent::__construct();

		parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );
	}

	public function index() {
		$a_config = array(
			'appId'     =>   '489883934396220',
			'secret'  =>   '6c2dd382215597ce708acad52a0ff951' ,
			'cookie' => true
		) ;


		$this->load->library('facebook', $a_config);

		$user = $this->facebook->getUser();
		if ($user) {
			try {
                      // Proceed knowing you have a logged in user who's authenticated.
				$access_token = $this->facebook->getAccessToken();

				$this->load->model('fbmodel');
				$this->fbmodel->set_token($access_token);

				$user_profile = $this->fbmodel->get_user_profile();
				$uid = $user_profile['id'];

				$me = $this->fbmodel-> get_me_by_fql($uid);

				$friends = $this->fbmodel->get_friends();

				$view_params = array(
					'me'  => $me,
					'friends' => $friends
				);
				$this->load->view("fbview", $view_params);
			} catch (FacebookApiException $e) {
				error_log($e);
				$user = null;
			}
		} else {
			$login_url =  $this->facebook->getLoginUrl(array(
				'canvas' => 1,
				'fbconnect' => 0,
				'req_perms' => 'email,status_update,offline_access'
			));

			header("Location:{$login_url}");
		}
	}
}