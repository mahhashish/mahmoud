<?php
namespace App\Controller;

use Core\HTML\BootstrapForm;
use Core\Upload;


class Price_request_cltController extends AppController
{
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('Price_request_clt');
		$this->loadModel('Price_request_clt_art');
		$this->loadModel('Client');
	}

	public function index(){
		$prs_clt = $this->Price_request_clt->load();
		$form = new bootstrapForm($_POST);

		$this->render('price_request_clt/index', compact('form', 'prs_clt'));
	}

	public function delete(){
		if(isset($_POST['pr_clt_id'])){
			$rs = $this->Price_request_clt->delete($_POST['pr_clt_id']);
			if($rs){
				return 1;
			} else {
				return 0;
			}
		}
	}
	public function deleteArt(){
		if(isset($_POST['pr_clt_art_id'])){
			$rs = $this->Price_request_clt_art->delete($_POST['pr_clt_art_id']);
			if($rs){
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function add(){
		if(!empty($_POST)){
			$params = [
				'num' => $_POST['num'],
				'dt' => $_POST['dt'],
				'subject' => $_POST['subject'],
				'discr' => $_POST['discr'],
				'client_id' => $_POST['box-infos-id'],
				'created_by' => $this->User('id'),
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Price_request_clt->create($params);
			if($rs){
				$this->redirect('price_request_clt/index');
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('price_request_clt/edit', compact('form'));
	}
	public function addart(){
		$pr_clt_id = $_GET['id'];

		$pr_clt = $this->Price_request_clt->find($pr_clt_id);

		if(!empty($_POST)){
			$params = [
				'pr_clt_id' => $pr_clt_id,
				'art_id' => $_POST['art_id'],
				'qte' => $_POST['qte'],
				'created_by' => $this->User('id'),
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Price_request_clt_art->create($params);
			if($rs){
				$this->redirect('price_request_clt/articles/'.$pr_clt_id);
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('price_request_clt/addart', compact('form', 'pr_clt'));
	}
	public function editart(){
		$pr_clt_id = $_GET['id'];
		$art_row_id = $_GET['art_row_id'];

		$pr_clt = $this->Price_request_clt->find($pr_clt_id);

		if(!empty($_POST)){
			$params = [
				'qte' => $_POST['qte'],
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Price_request_clt_art->update($art_row_id, $params);
			if($rs){
				$this->redirect('price_request_clt/articles/'.$pr_clt_id);
			}
		}
		$pr_clt_art = $this->Price_request_clt_art->find(['art_row_id' => $art_row_id, 'pr_clt_id' => $pr_clt_id]);
		$form = new bootstrapForm($pr_clt_art);
		$this->render('price_request_clt/addart', compact('form', 'pr_clt', 'pr_clt_art'));
	}
	public function edit(){
		$id = $_GET['id'];
		if(!empty($_POST)){
			$params = [
				'num' => $_POST['num'],
				'dt' => $_POST['dt'],
				'subject' => $_POST['subject'],
				'discr' => $_POST['discr'],
				'client_id' => $_POST['box-infos-id'],
				'created_by' => $this->User('id'),
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Price_request_clt->update($id, $params);
			if($rs){
				$this->redirect('price_request_clt/index');
			}
		}
		$pr_clt = $this->Price_request_clt->find($id);
		$form = new bootstrapForm($pr_clt);
		$this->render('price_request_clt/edit', compact('form', 'pr_clt'));
	}
	public function articles(){
		$id = $_GET['id'];
		$pr_clt = $this->Price_request_clt->find($id);
		$pr_clt_arts = $this->Price_request_clt_art->load($id);

		$this->render('price_request_clt/articles', compact('pr_clt', 'pr_clt_arts'));
	}
	public function show(){
		$id = $_GET['id'];
		$article = $this->Article->find($id);
		$this->render('articles/show', compact('article'));
	}
	public function printdetails(){
		$id = $_GET['id'];
		$pr_clt = $this->Price_request_clt->find($id);
		$pr_clt_arts = $this->Price_request_clt_art->load($id);
		$this->pdf('price_request_clt/printdetails', compact('pr_clt', 'pr_clt_arts'));
	}

}