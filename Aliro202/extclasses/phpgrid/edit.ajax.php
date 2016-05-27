<?php
require_once("phpGrid.php");

if(!session_id()){ session_start();}
if (!isset($HTTP_POST_VARS) && isset($_POST)){ $HTTP_POST_VARS = $_POST;}  // backward compability when register_long_arrays = off in config 

$gridName   = isset($_GET['gn']) ? $_GET['gn'] : die('phpGrid fatal error: URL parameter "gn" is not defined');
//$db         = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE, DB_CHARSET);
$dg         =  unserialize($_SESSION[GRID_SESSION_KEY.'_'.$gridName]);

//get db connection
$cn = $dg->get_db_connection();

if(empty($cn)){
    $db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE, DB_CHARSET);
}
else {       
    $db = new C_DataBase($cn["hostname"],$cn["username"],$cn["password"],$cn["dbname"],$cn["dbtype"],$cn["dbcharset"]);        
}

// check whether it is a masterdetail grid
$src        = isset($_GET['src'])?$_GET['src']:'';
//$dg = ($src=='md')?$dg->obj_md:$dg;

$arrFields  = array();
$pk         = $dg->get_sql_key();      // primary key
$pk_val     = $_POST[JQGRID_ROWID_KEY];    
$oper       = isset($_POST['oper']) ? $_POST['oper'] : ''; // operan type
$sqlCrud    = '';     // CRUD sql

if($oper != ''){
    $rs     = $db->select_limit($dg->get_sql(), 1, 1);  
    
    // EXCLUDING: 'oper', non-table-field, and auto increment, fields.          
    foreach($HTTP_POST_VARS as $key => $value){
        if($key != 'oper'){   
            $obj_field = $db->field_metacolumn($dg->get_sql_table(), $key);

            //if(($key!=$pk)||($key==$pk && $auto_inc)){
            if($obj_field){
                if(!$obj_field->auto_increment){
                    $arrFields[$key] = $value;                                           
                }                                                    
            }else{
                $arrFields[$key] = $value;        
            }
         
        }
    }     

    if($dg->debug) print_r($arrFields);  
        
    $fm_type   = $db->field_metatype($rs, $db->field_index($rs, $pk));              

    // Add singel quote to PK Value if it's not an integer(I), numeric(N), or autocrement int(R)
    // *** TODO ***: need to handle composite pk
    if($dg->has_multiselect()){
        $pk_valArr = explode(',',$pk_val);    
        $pk_vals = '';
        foreach($pk_valArr as $key => $value){
            if($fm_type != 'I' && $fm_type != 'N' && $fm_type != 'R')  
                $pk_vals .= "'" . trim($value) ."',";                          
            else
                $pk_vals .= $value .',';   
        }
        $pk_vals = substr($pk_vals, 0, -1);             // remove last ','
    }else{
        if($fm_type != 'I' && $fm_type != 'N' && $fm_type != 'R')     
            $pk_val = "'" . $pk_val ."'";                                         
    }
    

    // *** Note ***
    // Apparently, the SQL does not put single quote around numerics. This is preferred. 
    // Why GetUpdateSQL, not AutoExecute()? 
    // 1. $GetUpdateSQL($rs, $arrFields, $forceUpdate) does not require table name as parameter
    // 2. *** It only update values with valid field name ***
    // 3. AutoExecute() creates more overhead by validating whether rs is valid
    switch($oper){
        case 'add':
			$sqlCrud = $db->db->GetInsertSQL($rs, $arrFields, get_magic_quotes_gpc(), true);
            break;
        case 'edit':      
            $sqlCrud = $db->db->GetUpdateSQL($rs, $arrFields, get_magic_quotes_gpc(), true) .'  AND '. $pk .'='. $pk_val; 
		// $sqlCrud = $db->db->GetUpdateSQL($rs, $arrFields, true, get_magic_quotes_gpc()) .' WHERE '. $pk .'='. $pk_val; comment by rajeev
            break;
        case 'del':
            // borrowed from _adodb_getupdatesql() in adodb-lib.inc.php
            preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $dg->get_sql(), $tableName);
            $tableName = $tableName[1];
            if($dg->has_multiselect()){
                $sqlCrud = 'DELETE FROM '. $tableName .'  WHERE '. $pk .' IN('. $pk_vals .')';
				//  $sqlCrud = 'DELETE FROM '. $tableName .' WHERE '. $pk .' IN('. $pk_vals .')'; comment by rajeev
            }else{
                $sqlCrud = 'DELETE FROM '. $tableName .'  WHERE '. $pk .'='. $pk_val;   
				// $sqlCrud = 'DELETE FROM '. $tableName .' WHERE '. $pk .'='. $pk_val;   comment by rajeev
            }
            break;
    }
    if($dg->debug) echo 'SQL: '. $sqlCrud .'<br /><br />'; 
	//echo $sqlCrud;
    if($sqlCrud!='') $db->db_query($sqlCrud);     
}

$dg = null;
$db = null;
?>
