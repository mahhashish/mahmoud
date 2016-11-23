<?php /* $Id: toolbar.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

$oMD	= new mailDirector();
$oStyle = new style();

$oStyle->assign('draft', $oMD->getDefault('draft') );

$oStyle->display('toolbar.tpl');
?>
