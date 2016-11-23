
<?php

	/*  This you should have in an include file.
	 *  It starts the database class and knows 
	 *  what database type you use from now on.
	 */

	$db = new mysql;

	/*  Connect to your database. This should
	 *  be in an include file as well to better resist 
	 *  possible future changes.
	 */

	$db->connect('username', 'password', 'dbname', 'my.host.com');

	/*  Here you will execute the query and get
	 *  the result set returned as array
	 */

	$result = $db->query('age', Array('date' => '1978-10-26 20:38:40'));

	print_r($result);

	/*  This should print you:
	 *  Array
	 *  (
	 *      [age] => 24
	 *  )
	 */
	

?>
