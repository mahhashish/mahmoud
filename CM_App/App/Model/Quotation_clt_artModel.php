<?php
namespace App\Model;

use Core\Model\Model;

class Quotation_clt_artModel extends Model
{
	protected $table = 'quotations_clt_arts';
	public function load($id){
		return $this->query('SELECT
				quotations_clt_arts.*,
				(quotations_clt_arts.qte * quotations_clt_arts.price) AS total,
				articles.ref,
				articles.desig


				FROM quotations_clt, quotations_clt_arts, articles

				WHERE quotations_clt.id = quotations_clt_arts.quotation_clt_id
				AND quotations_clt_arts.art_id = articles.id
				AND quotations_clt.id = ?', [$id]);
	}
	public function find($vars){
		return $this->query("SELECT
				quotations_clt_arts.*,
				articles.ref,
				articles.desig


				FROM quotations_clt, quotations_clt_arts, articles

				WHERE quotations_clt.id = quotations_clt_arts.quotation_clt_id
				AND quotations_clt_arts.art_id = articles.id
				AND quotations_clt_arts.id = ?
				AND quotations_clt_arts.quotation_clt_id = ?
				", [$vars['art_row_id'], $vars['quotation_clt_id']], true);
	}

	public function show($id){

	}

}