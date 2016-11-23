<?php /* $Id: folders.php,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

include_once('include/mailDirector.inc');
include_once('include/style.inc');

function printFolders(&$obj)
{
	$style = &new style();
	$style->assign('folder', $obj);
	
	$folders = $obj->getFolders();

	if ( is_array($folders) )
	{
		$children = '';
		foreach ( $folders AS $f )
		{
			$children .= printFolders( $obj->fetch($f) );
		}

		$style->assign('children', $children);
	}

	return $style->fetch('foldersItem.tpl');
}


$oMD 	= new mailDirector();
/* Get our root folder (see MD_CONFIG, [default].mail) */
$oRoot 	= &$oMD->fetchMaildir();

$oStyle = new style();
$oStyle->assign('folders', printFolders($oRoot) );
$oStyle->display('folders.tpl');
?>
