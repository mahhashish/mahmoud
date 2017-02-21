<?php
namespace App\Controller;

use Core\HTML\BootstrapForm;
use Core\Upload;


class Quotation_cltController extends AppController
{
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('Quotation_clt');
		$this->loadModel('Quotation_clt_art');
		$this->loadModel('Client');
	}

	public function index(){
		$prs_clt = $this->Quotation_clt->load();
		$form = new bootstrapForm($_POST);

		$this->render('quotation_clt/index', compact('form', 'prs_clt'));
	}

	public function delete(){
		if(isset($_POST['quotation_id'])){
			$rs = $this->Quotation_clt->delete($_POST['quotation_id']);
			if($rs){
				return 1;
			} else {
				return 0;
			}
		}
	}
	public function deleteArt(){
		if(isset($_POST['quotation_art_id'])){
			$rs = $this->Quotation_clt_art->delete($_POST['quotation_art_id']);
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

			$rs = $this->Quotation_clt->create($params);
			if($rs){
				$this->redirect('quotation_clt/index');
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('quotation_clt/edit', compact('form'));
	}
	public function addart(){
		$quotation_id = $_GET['id'];

		$quotation_clt = $this->Quotation_clt->find($quotation_id);

		if(!empty($_POST)){
			$params = [
				'quotation_clt_id' => $quotation_id,
				'art_id' => $_POST['art_id'],
				'qte' => $_POST['qte'],
				'price' => $_POST['price'],
				'created_by' => $this->User('id'),
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Quotation_clt_art->create($params);
			if($rs){
				$this->redirect('quotation_clt/articles/'.$quotation_id);
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('quotation_clt/addart', compact('form', 'quotation_clt'));
	}
	public function editart(){
		$quotation_id = $_GET['id'];
		$art_row_id = $_GET['art_row_id'];

		$quotation_clt = $this->Quotation_clt->find($quotation_id);

		if(!empty($_POST)){
			$params = [
				'qte' => $_POST['qte'],
				'updated_by' =>$this->User('id')
			];

			$rs = $this->Quotation_clt_art->update($art_row_id, $params);
			if($rs){
				$this->redirect('quotation_clt/articles/'.$quotation_id);
			}
		}
		$quotation_art = $this->Quotation_clt_art->find(['art_row_id' => $art_row_id, 'quotation_id' => $quotation_id]);
		$form = new bootstrapForm($quotation_art);
		$this->render('quotation_clt/addart', compact('form', 'quotation', 'quotation_art'));
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

			$rs = $this->Quotation_clt->update($id, $params);
			if($rs){
				$this->redirect('quotation_clt/index');
			}
		}
		$quotation_clt = $this->Quotation_clt->find($id);
		$form = new bootstrapForm($quotation_clt);
		$this->render('quotation_clt/edit', compact('form', 'quotation'));
	}
	public function articles(){
		$id = $_GET['id'];
		$quotation_clt = $this->Quotation_clt->find($id);
		$quotation_clt_arts = $this->Quotation_clt_art->load($id);

		$this->render('quotation_clt/articles', compact('quotation_clt', 'quotation_clt_arts'));
	}
	public function show(){
		$id = $_GET['id'];
		$article = $this->Article->find($id);
		$this->render('articles/show', compact('article'));
	}
	public function printdetails(){
		$id = $_GET['id'];
		$quotation_clt = $this->Quotation_clt->find($id);
		$quotation_clt_arts = $this->Quotation_clt_art->load($id);
		$this->pdf('quotation_clt/printdetails', compact('quotation', 'quotation_arts'));
	}

}