<?php
namespace App\Model;

use Core\Model\Model;

class UserModel extends Model
{
	protected $table = 'users';

	public function load($filter = null){
		return $this->query('SELECT
				users.*,
				 roles.role_name

				FROM users, roles

				WHERE users.role_id = roles.id
				'.$filter.'
				');
	}
	public function find($id){
		return $this->query('SELECT
				users.*,
				 roles.role_name

				FROM users, roles

				WHERE users.role_id = roles.id
				AND users.id = ?
				',[$id], true);
	}

	public function show($id){

	}

	public function login($login, $pass){
		$user = $this->db->prepare("SELECT
				users.*,
				role_name,
				show_clients,
				show_suppliers,
				show_sales,
				show_purchases,
				show_articles,
				show_stock,
				show_users_roles,
				aed_clients,
				aed_suppliers,
				aed_sales,
				aed_purchases,
				aed_articles,
				aed_users_roles


				FROM users, roles

				WHERE users.role_id = roles.id
				AND login = ?
		", [$login], true);
		if($user){
			if($user->pass == sha1($pass)){
				$_SESSION['user'] = $user;
				setcookie('cm_user_id', $user->id, time() + 3600, '/');
				return true;
			}
		} else {
			return false;
		}
	}

}