<?php defined('BASEPATH') || exit('No direct script access allowed');


/**
 * Scores controller
 *
 * The base controller which displays the scores page.
 *
 * @package		Scores
 * @subpackage	Scores
 * @author		codauris
 * @link		http://codauris.tk
 */
 
class Scores extends Front_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('application');
		$this->load->library('Template');
		$this->load->library('Assets');
		$language = $this->input->cookie('language');
		$this->lang->load('tips/tips',$language);
		$this->load->library('events');
		
		$this->load->model('sports/sports_model');
		$this->load->model('countries/countries_model');
		
		Assets::add_module_css('scores', 'scores.css');
		Assets::add_module_js('scores', 'scores.js');

		
		
		
		include('simple_html_dom.php');

		$this->lang->load('bet_events/bet_events',$language);

        $this->requested_page = isset($_SESSION['requested_page']) ? $_SESSION['requested_page'] : null;
	}

	
	//--------------------------------------------------------------------
	public function index()
	{
		$date = date('Ymd');

		$url = "http://www.scoresdata.com/";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		
		Template::set('page', $page);
		$html = new simple_html_dom();
		$html->load($page);
		Template::set('html', $html);
		Template::set('date', $date);

		Template::render();
		
	}//end index()
	
	
	
	
	public function basketball()
	{

		$url = "http://www.scoresdata.com/basketball";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		
		Template::set('page', $page);
		$html = new simple_html_dom();
		$html->load($page);
		Template::set('html', $html);

		Template::render();
		
	}//end index()		
	
	public function handball()
	{

		$url = "http://www.scoresdata.com/handball";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		
		Template::set('page', $page);
		$html = new simple_html_dom();
		$html->load($page);
		Template::set('html', $html);

		Template::render();
		
	}//end index()	
	
	public function hockey()
	{

		$url = "http://www.scoresdata.com/hockey";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		
		Template::set('page', $page);
		$html = new simple_html_dom();
		$html->load($page);
		Template::set('html', $html);

		Template::render();
		
	}//end index()
	
	
	public function update(){
		//if(!file_exists('data.html')){
		$url = "http://www.scoresdata.com/";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		//file_put_contents('data.html', $page);
		//}else{
		//	$page = file_get_contents('data.html');
		//}
		
		$html = new simple_html_dom();
		$html->load($page);
		
		
		$tables = $html->find('#data-content .table_scores');
		$data  = array();
		$i = 0;
		foreach($tables as $tbl){
			$country_league = explode(':', $tbl->find('thead',0)->plaintext);
			$data[$i]['country'] = trim($country_league[0]);
			$data[$i]['laegue'] = trim($country_league[1]);
			
			$league_data = $tbl->find('tr');
			
			foreach($league_data as $ldata){
				$class =  $ldata->getAttribute('class');
				if($class == 'tr1' ||  $class == 'tr2'){
					$data[$i]['match_date'] = date('Y-m-d',strtotime(trim($ldata->find('td',1)->plaintext)));
					$data[$i]['match_time'] = trim($ldata->find('td',0)->plaintext);
					$data[$i]['home_team'] = trim($ldata->find('td',2)->plaintext);
					$data[$i]['away_team'] = trim($ldata->find('td',4)->plaintext);
					$score = explode('-',trim($ldata->find('td',3)->plaintext));
					$data[$i]['home'] = trim($score[0]) == '?' ? '' : trim($score[0]);
					$data[$i]['away'] = trim($score[1]) == '?' ? '' : trim($score[1]);
					
				}
			}
			
			$i++;
		}
		
		foreach($data as $d){
		/*
		$q = "SELECT * FROM results WHERE (away_team='{$d['home_team']}' OR home_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
		
		if($this->db->query($q)->num_rows()){
			echo $q . '<br><br><br>';
		}
		*/
		$q = "UPDATE results SET home='{$d['home']}', away='{$d['away']}' WHERE (home_team='{$d['home_team']}' OR away_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
	
		$this->db->query($q);
		}
		
		exit('records update successfully');
		
	}

		public function update_live(){
	
		if(!file_exists('data.html')){
		$url = "http://beta.livescore.com/soccer/today/";
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		file_put_contents('data.html', $page);
		}else{
			$page = file_get_contents('data.html');
		}
		$page;
		$html = new simple_html_dom();
		$html->load($page);
		
		
		$rows = $html->find('.content div');
		$data  = array();
		$i = 0;
		foreach($rows as $row){
			 $class =  $row->getAttribute('class');
			
			if($class == 'row row-tall mt4'){
				//	echo  $row->find('.right',0)->plaintext;
					$match_date = date('Y-m-d',strtotime(trim($row->find('.right',0)->plaintext)));
			}elseif($class == 'row-gray even' || $class == 'row-gray'){
				$data[$i]['match_date']  = $match_date;
				$data[$i]['match_time'] = trim($row->find('.min',0)->plaintext);
				$data[$i]['home_team'] = trim($row->find('.ply',0)->plaintext);
				$data[$i]['away_team'] = trim($row->find('.ply',1)->plaintext);
					
				$score = explode('-',trim($row->find('.sco',0)->plaintext));
				$data[$i]['home'] = trim($score[0]) == '?' ? '' : trim($score[0]);
				$data[$i]['away'] = trim($score[1]) == '?' ? '' : trim($score[1]);
					$i++;
			}
			
			
			
			
		}
		
		foreach($data as $d){
		
		$q = "SELECT * FROM results WHERE (home_team='{$d['home_team']}' OR away_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
		
		if(!$this->db->query($q)->num_rows()){
			echo $q . '<br><br><br>';
		}
		
		$q = "UPDATE results SET home='{$d['home']}', away='{$d['away']}' WHERE (home_team='{$d['home_team']}' OR away_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
	
		//$this->db->query($q);
		}
		
		exit('records update successfully');
		
	}
	
	
	// new function for two site record update
	
	function fetch_livescore(){
		
		$html = $this->curl("http://beta.livescore.com/soccer/today/");
		$rows = $html->find('.content div');
		$data  = array();
		$i = 0;
		foreach($rows as $row){
			 $class =  $row->getAttribute('class');
			
			if($class == 'row row-tall mt4'){
				//	echo  $row->find('.right',0)->plaintext;
					$match_date = date('Y-m-d',strtotime(trim($row->find('.right',0)->plaintext)));
			}elseif($class == 'row-gray even' || $class == 'row-gray'){
				$data[$i]['match_date']  = $match_date;
				$data[$i]['match_time'] = trim($row->find('.min',0)->plaintext);
				$data[$i]['home_team'] = trim(str_replace('*','',$row->find('.ply',0)->plaintext));
				$data[$i]['away_team'] = trim(str_replace('*','',$row->find('.ply',1)->plaintext));
					
				$score = explode('-',trim($row->find('.sco',0)->plaintext));
				$data[$i]['home'] = trim($score[0]) == '?' ? '' : trim($score[0]);
				$data[$i]['away'] = trim($score[1]) == '?' ? '' : trim($score[1]);
					$i++;
			}
			
		}
		
		return $data;
	}
	
	function fetch_datascore(){
		$html = $this->curl("http://www.scoresdata.com/");
		
		$tables = $html->find('#data-content .table_scores');
		$data  = array();
		$i = 0;
		foreach($tables as $tbl){
			$country_league = explode(':', $tbl->find('thead',0)->plaintext);
			$data[$i]['country'] = trim($country_league[0]);
			$data[$i]['laegue'] = trim($country_league[1]);
			
			$league_data = $tbl->find('tr');
			
			foreach($league_data as $ldata){
				$class =  $ldata->getAttribute('class');
				if($class == 'tr1' ||  $class == 'tr2'){
					$data[$i]['match_date'] = date('Y-m-d',strtotime(trim($ldata->find('td',1)->plaintext)));
					$data[$i]['match_time'] = trim($ldata->find('td',0)->plaintext);
					$data[$i]['home_team'] = trim($ldata->find('td',2)->plaintext);
					$data[$i]['away_team'] = trim($ldata->find('td',4)->plaintext);
					$score = explode('-',trim($ldata->find('td',3)->plaintext));
					$data[$i]['home'] = trim($score[0]) == '?' ? '' : trim($score[0]);
					$data[$i]['away'] = trim($score[1]) == '?' ? '' : trim($score[1]);
					
				}
			}
			
			$i++;
		}
		return 	$data;
	}
	
	function curl($url){
			
		//if(!file_exists('data.html')){
	
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);
		//file_put_contents('data.html', $page);
		//}else{
		//	$page = file_get_contents('data.html');
		//}
		//$page;
		$html = new simple_html_dom();
		$html->load($page);
		
	return $html;
	}
	
	function mapping_arr(){
			
	$xml = simplexml_load_file('fxml/mapping.xml') or die("Error: Cannot create object");
	$mapping = array();
	$j = 0;
	foreach($xml as $team){
		$mapping[$j][] = '__'.$team->db.'__';
		foreach($team->site as $s){
			$mapping[$j][] = $s.'';
		}
		$j++;
	}
	
		return $mapping;
	}

private function not_found($site_name_array,$date){
	$site_name_array = array ('Royal Charleroi SC','xtra');
    $not_found = array();       
	$q = "SELECT home_team,away_team FROM results WHERE match_date='{$date}'";
	$query = $this->db->query($q);
	$res = $query->result();
	foreach($res as $r)
	{
		if(!in_array($r->home_team, $site_name_array)){
			$not_found['home_team'][] = $r->home_team;
		}
	
		if(!in_array($r->away_team, $site_name_array)){
			$not_found['away_team'][] = $r->away_team;
		}
	
	}
	
	$content = '';
	if(isset($not_found['home_team'])){
		$content .= "HOME TEAMS NOT FOUND:: \r\n \r\n";
		$content .= implode(',',$not_found['home_team']);
	}
	if(isset($not_found['away_team'])){
		$content .= "\r\n \r\n AWAY TEAMS NOT FOUND:: \r\n \r\n";
		$content .= implode(',',$not_found['away_team']);
	}
		
		file_put_contents( 'fxml/not_found_'.$date.'.csv', $content);
	}

	public function update_new(){
	
	
		$mapping = $this->mapping_arr();
		
		$site_data[] = $this->fetch_livescore();
		$site_data[] = $this->fetch_datascore();
		$site_name_array = array();
		foreach($site_data as $data){
		foreach($data as $d){
		
		$site_name_array[] = $d['home_team'];
		$site_name_array[] = $d['away_team'];
		
		$q = "SELECT * FROM results WHERE (home_team='{$d['home_team']}' OR away_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
		
		if(!$this->db->query($q)->num_rows()){
			
			foreach($mapping as $m){
				if(in_array($d['home_team'], $m)){
					$d['home_team'] = trim($m[0],'__');
					break;
				}elseif(in_array($d['away_team'], $m)){
					$d['away_team'] = trim($m[0],'__');
					break;
				}
			
	//	echo $q . '<br><br>';		
				
			}
		}
		
		$q = "UPDATE results SET home='{$d['home']}', away='{$d['away']}' WHERE (home_team='{$d['home_team']}' OR away_team='{$d['away_team']}') AND match_date='{$d['match_date']}'";
	//echo '<br><br>';
		$this->db->query($q);
		}
	}
	
	//$this->not_found($site_name_array,$d['match_date']);
	exit('records update successfully');
}

}