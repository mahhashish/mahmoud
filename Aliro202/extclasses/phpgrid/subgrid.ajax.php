<?php                             
require_once('phpGrid.php');
if(!session_id()){ session_start();}  

$gridName = isset($_GET['gn']) ? $_GET['gn'] : die('phpGrid fatal error: URL parameter "gn" is not defined');
//$db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE);
$dg =  unserialize($_SESSION[GRID_SESSION_KEY.'_'.$gridName]);

//get db connection
$cn = $dg->get_db_connection();

if(empty($cn)){
    $db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE, DB_CHARSET);
}
else {       
    $db = new C_DataBase($cn["hostname"],$cn["username"],$cn["password"],$cn["dbname"],$cn["dbtype"],$cn["dbcharset"]);        
}


$sdg= $dg->obj_subgrid;

$pk_val = (isset($_GET['id']))?$_GET['id']:null;
$fk     = $sdg->get_sql_fkey();

// the request url should looks sth like this: 
// subgrid.ajax.php?id=2&_search=false&nd=1277597709752&rows=20&page=1&sidx=lineid&sord=asc
$page   = (isset($_GET['page']))?$_GET['page']:1; 
$limit  = (isset($_GET['rows']))?$_GET['rows']:20;
$sord   = (isset($_GET['sord']))?$_GET['sord']:'asc'; 
$sidx   = (isset($_GET['sidx']))?$_GET['sidx']:""; 

$sqlWhere   = "";
$rs         = $db->select_limit($sdg->get_sql(), 1, 1);            
$fm_type    = $db->field_metatype($rs, $db->field_index($rs, $fk));
// check datatype necessary? need to re-evaluate. for safty, leave it for now.
switch ($fm_type) {
    case 'I':
    case 'N':
    case 'R':
    case 'L':
        $sqlWhere = " AND ". $fk ."=". $pk_val;  
        break;
    default:
        $sqlWhere = " AND ". $fk ."='". $pk_val ."'";    
        break;
}    

// set ORDER BY. Don't use if user hasn't select a sort
$sqlOrderBy = (!$sidx) ? "" : " ORDER BY $sidx $sord";
                
// the actual query for the grid data   
$SQL = $sdg->get_sql(). ' '.$sqlWhere .$sqlOrderBy;  // its real query
                        
$result = $db->db_query($SQL);
$count  = $db->num_rows($result);              

// total pages
$total_pages = 0;             
if( $count > 0 && $limit > 0) { 
    $total_pages = ceil($count/$limit); 
}else{ 
    $total_pages = 0; 
} 
if ($page > $total_pages) $page=$total_pages;

      
$data_type = $sdg->get_jq_datatype();
switch($data_type)
{
    // render xml. Must set appropriate header information. 
    case "xml":
        $data = "<?xml version='1.0' encoding='utf-8'?>";
        $data .=  "<rows>";
        $data .= "<page>".$page."</page>";
        $data .= "<total>".$total_pages."</total>";
        $data .= "<records>".$count."</records>"; 
        $i = 0;
        while($row = $db->fetch_array_assoc($result)) {
            $data .= "<row id='". $row[$sdg->get_sql_key()] ."'>";            
            for($i = 0; $i < $db->num_fields($result); $i++) {
                $col_name = $db->field_name($result, $i);             
                    $data .= "<cell>". $row[$col_name] ."</cell>";    
            }  
            $data .= "</row>";       
        }
        $data .= "</rows>";    

        header("Content-type: text/xml;charset=utf-8");
        echo $data;   
        break;
                 
    case "json":
        $response = new stdClass();   // define anonymous object
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i=0;
        $data = array();              
        while($row = $db->fetch_array_assoc($result)) {
            unset($data);
            $response->rows[$i]['id']=$row[$sdg->get_sql_key()];
            for($j = 0; $j < $db->num_fields($result); $j++) {
                $col_name = $db->field_name($result, $j);                             
                    $data[] = $row[$col_name];    
            }            
            $response->rows[$i]['cell'] = $data;
//            $response->rows[$i]['cell']=array($row[id],$row[invdate],$row[name],$row[amount],$row[tax],$row[total],$row[note]);
            $i++;
        }        
        echo json_encode($response);  
//      echo C_Utility::indent_json(json_encode($response));  
        break;  
} 
          
// free resource       
$dg = null;
$sdg= null;
$db = null;
?>