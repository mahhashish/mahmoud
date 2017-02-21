<?php
namespace App\Controller;


use Core\HTML\BootstrapForm;

class SupplierController extends AppController
{
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('Supplier');
	}

	public function index(){
		$suppliers = $this->Supplier->load();
		$this->render('suppliers/index', compact('suppliers'));
	}
	public function modal(){

		$suppliers = $this->Supplier->all();
		$rs = '';
		foreach($suppliers as $supplier) {
			$rs .= '<tr>
						<td class="table-actions">
							<a href="#" class="btn btn-success btn-xs btn-select-supplier" supplier_id="' . $supplier->id . '" onclick="selectSupplier(this, event);">اختر</a>
						</td>
						<td class="supplier_name">' . $supplier->name . '</td>
						<td class="supplier_city">' . $supplier->city . '</td>
						<td class="supplier_address">' . $supplier->address . '</td>
					</tr>';
		}
		return $rs;
	}

	public function add(){
		if(!empty($_POST)){
			$params = [
				'name' => $_POST['name'],
				'tel' => $_POST['tel'],
				'email' => $_POST['email'],
				'zip_code' => $_POST['zip_code'],
				'city' => $_POST['city'],
				'address' => $_POST['address'],
				'created_by' => $_SESSION['user']->id,
				'updated_by' => $_SESSION['user']->id
			];

			$rs = $this->Supplier->create($params);
			if($rs){
				$this->redirect('supplier/index');
			}
		}
		$form = new bootstrapForm($_POST);
		$this->render('suppliers/edit', compact('form'));
	}
	public function edit(){
		$id = $_GET['id'];
		if(!empty($_POST)){
			$params = [
				'name' => $_POST['name'],
				'tel' => $_POST['tel'],
				'email' => $_POST['email'],
				'zip_code' => $_POST['zip_code'],
				'city' => $_POST['city'],
				'address' => $_POST['address'],
				'created_by' => $_SESSION['user']->id,
				'updated_by' => $_SESSION['user']->id
			];

			$rs = $this->Supplier->update($id, $params);
			if($rs){
				$this->redirect('supplier/index');
			}
		}
		$supplier = $this->Supplier->find($id);
		$form = new bootstrapForm($supplier);
		$this->render('suppliers/edit', compact('form', 'supplier'));
	}

	public function delete(){
		if(isset($_POST['supplier_id'])){
			$rs = $this->Supplier->delete($_POST['supplier_id']);
			if($rs){
				return 1;
			} else {
				return 0;
			}
		}
	}


}