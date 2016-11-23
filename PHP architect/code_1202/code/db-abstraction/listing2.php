
<?php


/*   Class:            db_layer
   +----------------------------------------------------------------------+
     Scope:            Private
     Authors:          Maxim Maletsky (maxim@php.net)

     Description:      Database Abstraction Layer.
   +----------------------------------------------------------------------+
*/

Class db_layer {

	// Private
	var $db_alias      = '';              // Query Alias
	var $db_host       = '';              // Database Host
	var $db_username   = '';              // Database Username
	var $db_password   = '';              // Database Password
	var $db_database   = '';              // Database Name
	var $db_store      = Array();         // Locations for SQL files
	var $db_query      = Array();         // Query Handler
	var $db_record     = Array();         // DB Result array


	/*   Method:           connect
	   +------------------------------------------------------------------+
	     Description:      Create the connection handler for the API to
	                       Database server.
	     +------------------------------------------------------------------+
	*/

	function connect($user='', $pass='', $db='', $host='') {

		$this->db_username   = $user;
		$this->db_password   = $pass;
		$this->db_database   = $db;
		$this->db_host       = $host;

		// Return positively on successful connection.
		Return $this->api_connect();
	}


	/*   Method:           query
	   +------------------------------------------------------------------+
	     Description:      Execute Query
	   +------------------------------------------------------------------+
	*/

	function query($alias = '', $data = '') {

		// Connect to API
		$this->connect();

		// Store Alias
		$this->db_alias = $alias;
		$this->db_data = $data;

		extract($this->db_data);

		// Include SQL storage files
		@include($this->api_name . '.sql.php');

		// Check existence of query
		$this->db_query = isset($query)? $query : die('No query for alias ' . $this->db_alias);

		// Clean trailing semicolons ';' in SQL 
		$this->db_query['sql']  = isset($this->db_query['sql'])? preg_replace("/;?\s*$/s", '', $this->db_query['sql']) : '';

		// Execute Query
		$this->api_execute();

		// Return result:
		Return $this->result();
	}



	/*   Method:           result
	   +------------------------------------------------------------------+
	     Description:      Return results.
	   +------------------------------------------------------------------+
	*/

	function result() {

		// Number of Rows
		$this->db_rows     = $this->api_rows();

		// Number of Columns
		$this->db_cols     = $this->api_cols();

		// Compose array
		$r = 0;
		while($r<$this->db_rows and $this->api_next_record($r)) {

			// Loop values
			foreach($this->db_record as $col=>$val) {
				$this->db_result[$r][$col] = $val;
			}

			// increment row count
			$r++;
		}

		// Return Results
		Return $this->db_result;
	}


	/*   Method:           close
	   +------------------------------------------------------------------+
	     Description:      Close Database Connection
	   +------------------------------------------------------------------+
	*/

	function close() {

		// Close database connection
		$this->api_close();

		Return True;
	}
}

?>