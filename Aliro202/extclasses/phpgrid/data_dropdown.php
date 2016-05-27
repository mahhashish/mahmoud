<?php             

// Questionable status at present?  (MNB)

require_once('phpGrid.php');

if(!session_id()){ session_start();}  

$data = ''; // this is our data
$tableName      = $_GET['tableName'] or die('phpGrid fatal error: ULR parameter "tableName" for table name is not defined.');
$dataText       = $_GET['dataText']  or die('phpGrid fatal error: ULR parameter "dataText" for data text field is not defined.'); 
$dataValue      = $_GET['dataValue'] or die('phpGrid fatal error: ULR parameter "dataValue" for data value field is not defined.'); 
$addBlank       = isset($_GET['addBlank']) ? true : false;  // add a blank item in dropdown
 
$db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_TYPE);
$result = $db->db_query("SELECT $dataText, $dataValue FROM $tableName");            

/*
$data .= '<select>';
$data .= ($addBlank) ? '<option></option>' : '';
while($row = $db->fetch_array_assoc($result)) {
    $data .= '<option value="'. $row[$dataValue] .'">';
    $data .= $row[$dataText];               
    $data .= '</option>';       
}
$data .= "</select>";    
*/

$data = '"":"";';
while($row = $db->fetch_array_assoc($result)) {
    $data .= ''. $row[$dataValue] .':' .$row[$dataText];
    $data .= ';';               
}


echo $data;   

$db = null;
?>