<?php
namespace App\Controller;


use Core\HTML\BootstrapForm;

class RoleController extends AppController
{
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('role');
		$this->loadModel('Role');
	}

	public function index(){
		$roles = $this->Role->all();
		$this->render('roles/index', compact('roles'));
	}

	public function add(){
		if(!empty($_POST)){
			$params = [
				'role_name' => $_POST['role_name'],
				'show_clients' => $_POST['show_clients'],
				'aed_clients' => $_POST['aed_clients'],
				'show_suppliers' => $_POST['show_suppliers'],
				'aed_suppliers' => $_POST['aed_suppliers'],
				'show_sales' => $_POST['show_sales'],
				'aed_sales' => $_POST['aed_sales'],
				'show_purchases' => $_POST['show_purchases'],
				'aed_purchases' => $_POST['aed_purchases'],
				'show_articles' => $_POST['show_articles'],
				'aed_articles' => $_POST['aed_articles'],
				'show_stock' => $_POST['show_stock'],
				'show_users_roles' => $_POST['show_users_roles'],
				'aed_users_roles' => $_POST['aed_users_roles']

			];

			$rs = $this->Role->create($params);
			if($rs){
				$this->redirect('role/index');
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('roles/edit', compact('form'));
	}
	
	public function edit(){
		$id = $_GET['id'];
		if(!empty($_POST)){
			$params = [
				'role_name' => $_POST['role_name'],
				'show_clients' => $_POST['show_clients'],
				'aed_clients' => $_POST['aed_clients'],
				'show_suppliers' => $_POST['show_suppliers'],
				'aed_suppliers' => $_POST['aed_suppliers'],
				'show_sales' => $_POST['show_sales'],
				'aed_sales' => $_POST['aed_sales'],
				'show_purchases' => $_POST['show_purchases'],
				'aed_purchases' => $_POST['aed_purchases'],
				'show_articles' => $_POST['show_articles'],
				'aed_articles' => $_POST['aed_articles'],
				'show_stock' => $_POST['show_stock'],
				'show_users_roles' => $_POST['show_users_roles'],
				'aed_users_roles' => $_POST['aed_users_roles']

			];

			$rs = $this->Role->update($id, $params);
			if($rs){
				$this->redirect('role/index');
			}
		}
		$role = $this->Role->find($id);
		$form = new bootstrapForm($role);
		$this->render('roles/edit', compact('form', 'role'));
	}
	
	public function delete(){
		if(isset($_POST['role_id'])){
			$rs = $this->Role->delete($_POST['role_id']);
			if($rs){
				return 1;
			} else {
				return 0;
			}
		}
	}


}