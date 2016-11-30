<?php 


class Cron extends Front_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('application');
		$this->db->save_queries = FALSE;
        $this->load->model('bet_events/bet_events_model');
		$this->load->model('results/results_model');

	}

	//--------------------------------------------------------------------

	public function index()
	{
		if(!$this->input->is_cli_request())
        {
            echo "This script can only be accessed via the command line" . PHP_EOL;
            return;
        }
	
		$url = 'http://xml.cdn.betclic.com/odds_en.xml';

		set_time_limit(0);
		$fp = fopen ('fxml/download.xml', 'w+');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FAILONERROR,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_exec($ch);			 
		curl_close($ch);
		fclose($fp);

		sleep(3);
		
		$this->output->clear_all_cache();
		
		$this->results_model->update_results();
		$this->bet_events_model->update_leagues();
		
		$this->bet_events_model->delete_old_bets();
		$this->bet_events_model->delete_old_events();

		$this->bet_events_model->update_bets();				
		$this->bet_events_model->update_events();
		
		$this->results_model->update_teams1();
		$this->results_model->update_teams2();
		$this->results_model->update_teams3();
		
		$this->bet_events_model->set_update();
		
		
		echo'Cron updates succesfull';

		

		

	}//end index()
}

