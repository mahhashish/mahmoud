<?php
if(!session_id()){ session_start();} // this is nessecory for PHP that running on Windows
class C_Database{
	public $hostName;
	public $userName;
	public $password;
	public $databaseName;
	public $tableName;
	public $link;
	public $dbType;
	public $charset;
    public $db;
    
    
    public $result;
	
	public function __construct($host, $user, $pass, $dbName, $db_type = "mysql", $charset=""){
		$this -> hostName = $host;
		$this -> userName = $user;
		$this -> password = $pass;
		$this -> databaseName = $dbName;
		$this -> dbType  = $db_type;
        $this->charset = $charset;
		
		$this -> _db_connect();	
	}
	
	/*
	************************* working with this function in database layer class ************************
	*                                                                                                   *
	*  Connect to the Database                                                                          *
	*  Go to http://phplens.com/lens/adodb/docs-adodb.htm#connect_ex for database connection reference  *
	*  to other types of databases and simply modify/add to the Switch statement.                       *
	*                                                                                                   *
	*  There are multiple ways to connect Oracle db                                                     *
	*  see "http://phplens.com/adodb/code.initialization.html#oci8"                                     *
	*  and change it properly to suit your needs                                                        *
	*                                                                                                   *
	*****************************************************************************************************
	*/
	public function _db_connect(){
		switch($this->dbType){
			case "access":
				$this->db =& ADONewConnection($this->dbType);
				$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".$this->databaseName.";Uid=".$this->userName.";Pwd=".$this->password.";";
				$this->db->Connect($dsn);
				break;
			case "odbc_mssql":
				$this->db =& ADONewConnection($this->dbType);
				$dsn = "Driver={SQL Server};Server=".$this->hostName.";Database=".$this->databaseName.";";
				$this->db->Connect($dsn, $this->userName, $this->password);
				break;
			case "postgres":
				$this->db = &ADONewConnection($this->dbType);
				$this->db->PConnect($this->hostName, $this->userName, $this->password, $this->databaseName) or die("Error: Could not connect to the database");
				if(!empty($this->charset)) {
                    $this->db->Execute("SET NAMES '$this->charset'");
                }
                break;
			case "db2":
				$this->db =& ADONewConnection($this->dbType);
				$dsn = "driver={IBM db2 odbc DRIVER};Database=".$this->databaseName.";hostname=".$this->hostName.";port=50000;protocol=TCPIP;uid=".$this->userName."; pwd=".$this->password;
				$this->db->Connect($dsn);
				break;
			case "ibase":
				$this->db = &ADONewConnection($this->dbType); 
				$this->db->PConnect($this->hostName . $this->databaseName, $this->userName, $this->password);
				break;
			case "oci8":
                // PHP and Oracle reside on the same machine, use default SID
                $ret = $this->db->Connect(false, $this->userName, $this->password);
                if(!$ret){
                    // Host Address and Service Name
                    // <servicename> is passed in databaseName
                    $this->db->Connect($this->hostName, $this->userName, $this->password, $this->databaseName,$this->charset);                                                                     
                }             
				
				// TNS Name defined in tnsnames.ora (or ONAMES or HOSTNAMES), eg. 'myTNS'
				// $this->db->PConnect(false, $this->userName, $this->password, 'myTNS');
				
				// Host Address and SID
				// $this->db->connectSID = true;
				// $this->db->Connect('192.168.0.1', $this->userName, $this->password, $SID);
				
				break;
			case "sqlit":
				// $this->db = &ADONewConnection('sqlite');
				// $this->db->PConnect('c:\path\to\sqlite.db'); # sqlite will create if does not exist
				break;
			// default should be mysql and all other databases using the following form of connection
			default:	
				$this->db = ADONewConnection($this->dbType);		
				$this->db->Connect($this->hostName, $this->userName, $this->password, $this->databaseName) or die("Error: Could not connect to the database");
                if(!empty($this->charset)) {
                    $this->db->Execute("SET NAMES '$this->charset'");
                }
		}			
	}
	       
	// Desc: query database
	public function db_query($query_str){
		$this->db->SetFetchMode(ADODB_FETCH_BOTH);
		$result = $this->db->Execute($query_str) or die("Error: Could not execute query $query_str in db_query()");

		$this->result = $result;        
        return $result;
	}
	
	public function select_limit($query_str, $size, $starting_row){
		$result = $this->db->SelectLimit($query_str, $size, $starting_row) or die("Error: Could not execute query $query_str in select_limit()");

        $this->result = $result;        
		return $result;
	}
	
	// Desc: helper function to get array from select_limit function
	public function select_limit_array($query_str, $size, $starting_row){
		$result = $this->select_limit($query_str, $size, $starting_row);
		$resultArray = $result->GetArray();

        $this->result = $resultArray;        
		return $resultArray;
	}
    	
	// Desc: fetch record from database as row
	// Note: the parameter is passed as reference
	public function fetch_row(&$result){
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if(!$result->EOF){
		 	$rs = $result->fields;
		 	$result->MoveNext();        
		 	return $rs;
		}
	}
	
	// Desc: fetch record from database as array
	// Note: the parameter is passed as reference
	public function fetch_array(&$result){
		$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		if(!$result->EOF){
		 	$rs = $result->fields;
		 	$result->MoveNext();   
		 	return $rs;
		}  
	}
	
	// Desc: fetch record from database as associative array
	// Note: the parameter is passed as reference
	public function fetch_array_assoc(&$result){
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if(!$result->EOF){
		 	$rs = $result->fields;
		 	$result->MoveNext();  
		 	return $rs;
		}
	}	
	
	// Desc: helper function. query first then fetch record from database as row
	public function query_then_fetch_row($query_str){
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$result = $this->db->Execute($query_str) or die("Error: Could not execute query $query_str");        
        if(!$result->EOF){
		 	$rs = $result->fields;
		 	$result->MoveNext();
		 	return $rs;
		}
	}
	
	// Desc: helper function. query first fetch record from database as associative array
	public function query_then_fetch_array($query_str){
		$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		$result = $this->db->Execute($query_str) or die("Error: Could not execute query $query_str");
		if(!$result->EOF){
		 	$rs = $result->fields;
		 	$result->MoveNext();     
		 	return $rs;
		}
	}
	
	// Desc: number of rows query returned
	public function num_rows($result){
        return $result->RecordCount();
        /*
		$totalCnt = 0;
        $result = $this->db_query('SELECT COUNT(*) as recordcount FROM '. $table_name);
        while($row = $this->fetch_array_assoc($result)) {
           $totalCnt = $row['recordcount'];    
        } 
        
        return $totalCnt;
        */
	} 
	
	// Desc: number of data fields in the recordset
	public function num_fields($result){
		return $result->FieldCount();
	}
	
	// Desc: a specific field name (column name) with that index in the recordset
	public function field_name($result, $index){
		$obj_field = new ADOFieldObject();
		$obj_field = $result->FetchField($index);
		return isset($obj_field->name) ? $obj_field->name : "";
	}
       
      // Desc: the type of a specific field name (column name) with that index in the recordset
    public function field_nativetype($result, $index){
        $obj_field = new ADOFieldObject();
        $obj_field = $result->FetchField($index);
        return isset($obj_field->type) ? $obj_field->type : "";
    }
   
    // Desc: the generic Meta type of a specific field name by index.      
    // Returns: 
    // C: Character fields that should be shown in a <input type="text"> tag.
    // X: Clob (character large objects), or large text fields that should be shown in a <textarea>
    // D: Date field
    // T: Timestamp field
    // L: Logical field (boolean or bit-field)
    // N: Numeric field. Includes decimal, numeric, floating point, and real.
    // I:  Integer field.
    // R: Counter (Access), Serial(PostgreSQL) or Autoincrement int field. Must be numeric.
    // B: Blob, or binary large objects.
    public function field_metatype($result, $index){
        $obj_field = new ADOFieldObject();
        $obj_field = $result->FetchField($index);
        // $type = $result->MetaType($obj_field->type);    
        $type = $result->MetaType($obj_field->type, $obj_field->max_length);   // Since ADOdb 3.0, MetaType accepts $fieldobj as the first parameter, instead of $nativeDBType.    
                
        return $type;              
    }
    
    // obtain meta column info as specific field in a table.e.g. auto increment, not null
    // file: adodb.$dbtype.inc.php - ADODB_$dbtype::ADOConnection.MetaColumns()
    // return false if col_name is not in table, else return metacolumn
    public function field_metacolumn($table, $col_name){
        $arr = array();   
        $arr =  $this->db->MetaColumns($table);

        $obj_field = new ADOFieldObject();
        if(isset($arr[strtoupper($col_name)])){
            $obj_field = $arr[strtoupper($col_name)];
    //        print('<pre>');
    //        print_r($obj_field);
    //        print('</pre>');
            return $obj_field;                                        
        }else{
            return false;
        }
        


    }
    
    // Desc: return corresponding field index by field name
    public function field_index($result, $field_name){
        $field_count = $this->num_fields($result);
        $i=0;
        for($i=0;$i<$field_count;$i++){
            if($field_name == $this->field_name($result, $i))
                return $i;        
        }    
        return -1;
    }
	
	// Desc: the length of a speciifc field name (column name) with that index in the recordset
	public function field_len($result, $index){
		$obj_field = new ADOFieldObject();
		$obj_field = $result->FetchField($index);
		return isset($obj_field->max_length) ? $obj_field->max_length : "";
	}
}
?>