<?php

//phpcodeinject.php
if isset($_GET['page'])
	include($_GET['page']);
else
	echo "Please specify a page to view\n";

?>