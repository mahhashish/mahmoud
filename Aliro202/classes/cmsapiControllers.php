<?php

/**************************************************************
* This file is part of A CMS API
* Copyright (c) 2008-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Please see glossary.php for more details
*/

// This is the base class for all user side controllers

abstract class cmsapiControllers {
	protected $interface = null;
	protected $database = null;
	protected $configuration = null;
	protected $Itemid = 0;
	protected $live_site = '';

	public function __construct () {
		$this->interface = cmsapiInterface::getInstance($this->cname);
		$this->database = $this->interface->getDB();
		$this->configuration = aliroComponentConfiguration::getConfiguration($this->cname);
		$this->live_site = $this->interface->getCfg('live_site');
	}

}