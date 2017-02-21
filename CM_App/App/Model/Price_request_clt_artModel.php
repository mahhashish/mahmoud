<?php
namespace App\Model;

use Core\Model\Model;

class Price_request_clt_artModel extends Model
{
	protected $table = 'prs_clt_arts';
	public function load($id){
		return $this->query('SELECT
				prs_clt_arts.*,
				articles.ref,
				articles.desig


				FROM prs_clt, prs_clt_arts, articles

				WHERE prs_clt.id = prs_clt_arts.pr_clt_id
				AND prs_clt_arts.art_id = articles.id
				AND prs_clt.id = ?', [$id]);
	}
	public function find($vars){
		return $this->query("SELECT
				prs_clt_arts.*,
				articles.ref,
				articles.desig


				FROM prs_clt, prs_clt_arts, articles

				WHERE prs_clt.id = prs_clt_arts.pr_clt_id
				AND prs_clt_arts.art_id = articles.id
				AND prs_clt_arts.id = ?
				AND prs_clt_arts.pr_clt_id = ?
				", [$vars['art_row_id'], $vars['pr_clt_id']], true);
	}

	public function show($id){

	}

}