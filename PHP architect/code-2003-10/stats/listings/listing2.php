<?php

// Class to represent the data of each sale

class sale {
	var $salesman;
	var $stockitem;
	var $client;
	var $qty;

	function sale() {
		$this->salesman = New salesman();
		$this->stockitem = New stockitem();
		$this->client = New client();
	}
};

// Class to represent each salesman

class salesman {
	var $years_exp;
	var $education;
	var $name;
};

// Class to represent each stock item

class stockitem {
	var $price;
	var $name;
};

// Class to represent each client

class client {
	var $sector;
	var $name;
};
?>
