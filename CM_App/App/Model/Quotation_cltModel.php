<?php
namespace App\Model;

use Core\Model\Model;

class Quotation_cltModel extends Model
{
	protected $table = 'quotations_clt';
	public function load($filter = null){
		return $this->query('SELECT
				quotations_clt.*,
				clients.name as client_name

				FROM quotations_clt, clients

				WHERE quotations_clt.client_id = clients.id
				'.$filter.'
				');
	}
	public function find($id){
		return $this->query("SELECT
				quotations_clt.*,
				clients.name as client_name,
				clients.city,
				clients.address

				FROM quotations_clt, clients

				WHERE quotations_clt.client_id = clients.id
				AND quotations_clt.id = ? ", [$id], true);
	}

	public function show($id){

	}

}