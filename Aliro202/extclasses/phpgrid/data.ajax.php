<?php          
require_once('phpGrid.php');

if(!session_id()){ session_start();}  

$gridName = isset($_GET['gn']) ? $_GET['gn'] : die('phpGrid fatal error: ULR parameter "gn" is not defined.');
$dg =  unserialize($_SESSION[GRID_SESSION_KEY.'_'.$gridName]);

//get db connection
$cn = $dg->get_db_connection();

if(empty($cn)){
    $db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE, DB_CHARSET);
}
else {       
    $db = new C_DataBase($cn["hostname"],$cn["username"],$cn["password"],$cn["dbname"],$cn["dbtype"],$cn["dbcharset"]);        
}


//print_r($dg->col_hiddens);

$page = $_GET['page']; 
 
// get how many rows we want to have into the grid - rowNum parameter in the grid 
$limit = $_GET['rows']; 
 
// get index row - i.e. user click to sort. At first time sortname parameter -
// after that the index from colModel 
$sidx = $_GET['sidx']; 
 
// sorting order - at first time sortorder 
$sord = $_GET['sord']; 
   
$rs     = $db->select_limit($dg->get_sql(), 1, 1);            


// prepare sql where statement. must check if the key is actual a database field
$sqlWhere = "";
$searchOn = ($_REQUEST['_search']=='true')?true:false;
if($searchOn) {
    $col_dbnames = array();
    $col_dbnames = $dg->get_col_dbnames($rs);        
              
    foreach($_REQUEST as $key=>$value) {
        if(in_array($key, $col_dbnames)){
            $fm_type = $db->field_metatype($rs, $db->field_index($rs, $key));
            switch ($fm_type) {
                case 'I':
                case 'N':
                case 'R':
                case 'L':
                    $sqlWhere .= " AND ".$key." = ".$value;
                    break;
                default:
                    $sqlWhere .= " AND ".$key." LIKE '".$value."%'";
                    break;
            }    
        }
        
    }
    //advanced search    
    if(isset($_REQUEST['filters'])){
        $op = array("eq"=>" ='%s' ","ne"=>" !='%s' ","lt"=>" < %s ",
            "le"=>" <= %s ","gt"=>" > %s ","ge"=>" >= %s ",
            "bw"=>" like '%s%%' ","bn"=>" not like '%s%%' " ,
            "in"=> " in (%s) ","ni"=> " not in (%s) ",
            "ew"=> " like '%%%s' ","en"=> " not like '%%%s' ",
            "cn"=> " like '%%%s%%' ","nc"=> " not like '%%%s%%' ");
            
        $filters = json_decode(stripcslashes($_REQUEST['filters']));
        $groupOp = $filters->groupOp;
        $rules = $filters->rules;
        
        for($i=0;$i<count($rules);$i++){                   
            $sqlWhere .=  $groupOp . " ". $rules[$i]->field .
                sprintf($op[$rules[$i]->op],$rules[$i]->data);              
        }
    }
}

$count = $dg->get_num_rows(); // $db->num_rows($dg->get_sql_table());
                 
// echo $count;
                 
// calculate the total pages for the query 
if( $count > 0 && $limit > 0) { 
    $total_pages = ceil($count/$limit); 
}else{ 
    $total_pages = 0; 
} 
 
// if for some reasons the requested page is greater than the total 
// set the requested page to total page 
if ($page > $total_pages) $page=$total_pages;
 
// calculate the starting position of the rows 
$start = $limit*$page - $limit;
 
// if for some reasons start position is negative set it to 0 
// typical case is that the user type 0 for the requested page 
if($start <0) $start = 0; 
       
// set ORDER BY. Don't use if user hasn't select a sort
$sqlOrderBy = (!$sidx) ? "" : " ORDER BY $sidx $sord";

// the actual query for the grid data 
$SQL = $dg->get_sql(). (($searchOn)?' '.$sqlWhere:'') .$sqlOrderBy; // its real query

// echo $SQL . ' - '. $limit .' - '. $start;

// $result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 
$result = $db->select_limit($SQL, $limit, $start);
 


// $col_hiddens = $dg->get_col_hiddens();       
$data_type = $dg->get_jq_datatype();
switch($data_type)
{
    // render xml. Must set appropriate header information. 
    case "xml":
        $data = "<?xml version='1.0' encoding='utf-8'?>";
        $data .=  "<rows>";
        $data .= "<page>".$page."</page>";
        $data .= "<total>".$total_pages."</total>";
        $data .= "<records>".$count."</records>"; 
        while($row = $db->fetch_array_assoc($result)) {
            $data .= "<row id='". $row[$dg->get_sql_key()] ."'>";            
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
        $response = new stdClass();   // define anonymous objects
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $i=0;
        $data = array();              
        while($row = $db->fetch_array_assoc($result)) {
            unset($data);
            $response->rows[$i]['id']=$row[$dg->get_sql_key()];
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
$db = null;
//$_SESSION[GRID_SESSION_KEY] = null;


?>