
<?php

/*   Class:            mysql 
   +----------------------------------------------------------------------+
     Scope:            Private
     Authors:          Maxim Maletsky (maxim@php.net)

     Description:      MySQL database abstraction Layer.

                       API is the back-level connection resource handler.
                       It can be expanded into any amount of data handling 
                       procedures, however, all the public must preserve 
                       their names and return structure.
--------------------------------------------------------------------------+
*/

Class mysql extends db_layer {

	// Public
	var $api_name       = 'mysql';         // Api literal name

	// Private
	var $api_conn       = False;           // Connection Handler
	var $api_stmt       = False;           // Statement Handler


	/*   Method: api_connect   */

	function api_connect() {
		
		// if API is already connected, Exit
		if($this->api_conn)
			Return True;

		// Connect to MySQL Server
		if(!$this->api_conn = @mysql_pconnect($this->db_host, $this->db_username, $this->db_password)) {
			die('Cannot connect to mySQL server');
			Return False;
		}

		// Hook onto a MySQL database
		if(!@mysql_select_db($this->db_database, $this->api_conn)) {
			die('Cannot Select mySQL Database' . $this->db_database);
			Return False;
		}

		// Return positively on successful connection
		Return True;
	}


	/*   Method: api_execute   */

	function api_execute() {
		Return ($this->api_stmt = @mysql_query($this->db_query['sql'], $this->api_conn));
	}


	/*   Method: api_escape   */

	function api_escape($var) {
		Return @mysql_escape_string($var);
	}


	/*   Method: api_rows   */

	function api_rows() {
		$rows = @mysql_num_rows($this->api_stmt);
		Return $rows? $rows : 0;
	}


	/*   Method: api_cols   */

	function api_cols() {
		$cols = @mysql_num_fields($this->api_stmt);
		Return $cols? $cols : 0;
	}

	/*   Method: api_close   */

	function api_close() {
		$this->api_free();
		@mysql_close($this->api_conn);
		Return $this->api_conn = False;
	}
}

?>
