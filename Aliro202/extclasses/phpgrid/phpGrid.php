<?php                                             

// auto calculate ABS_PATH. user no longer requires to define ABS_PATH
define('SERVER_ROOT', str_replace($_SERVER['DOCUMENT_ROOT'],'', str_replace( '\\', '/',dirname(__FILE__))));

require_once(dirname(__FILE__) .'/conf.php');  
require_once(dirname(__FILE__) .'/server/classes/cls_db.php');  
require_once(dirname(__FILE__) .'/server/classes/cls_datagrid.php');  
require_once(dirname(__FILE__) .'/server/classes/cls_util.php');  
require_once(dirname(__FILE__) .'/server/classes/cls_control.php');  
require_once(dirname(__FILE__) .'/server/adodb5/adodb.inc.php');  

define('GRID_SESSION_KEY', '_oPHPGRID');
define('JQGRID_ROWID_KEY', 'id');
define("CHECKBOX", "checkbox");
define("SELECT", "select");
define("MULTISELECT", "multiselect");  
