require($SMARTY_DIR . "smarty.class.php");

$smarty = new smarty;
$smarty->assign("CustomerName","Toby Allen");	

$smarty->display('listing9.tpl');
