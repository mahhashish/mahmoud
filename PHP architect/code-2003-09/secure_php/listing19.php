<?php

//auth.inc
class auth {

	var $isvalid;

	function auth($un, $pw) {
		if ($un == 'user' && $pw == 'pass')
			$this->isvalid=true;
		else
			$this->isvalid=false;
	}
}

?>
