<?php

//trustedvaluesformhandler.php
if (!isset($_GET['price']) || !isset($_GET['cc']))
	die;
mail("billing@server.com", "New Bill", "Bill card " . $_GET['cc'] .. "\nFor amount: \$" . $_GET['price']);
echo "Order placed\n";

?>