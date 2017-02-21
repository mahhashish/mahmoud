<?php
namespace App\Controller;

class HomeController extends AppController
{
	public function __construct()
	{
		parent::__construct();

	}

	public function index(){
		$form = null;
		$this->render('home', compact('form'));
	}


}