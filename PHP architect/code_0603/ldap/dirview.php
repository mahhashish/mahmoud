<?php

$server = 'localhost';
$conn = ldap_connect($server);
$bind = ldap_bind($conn);

$searchbase = 'dc=linuxlaboratory,dc=org';
$listresult = ldap_list($conn, $searchbase, 'ou=*');
$oulist = ldap_get_entries($conn, $listresult);

for($i = 0; $i < $oulist["count"]; $i++)
{
	$ou = $oulist[$i]["dn"];
	echo "Organizational Unit: ".$ou."<br>\n";

	$retattrs = array('cn');
	$objresult = ldap_list($conn, $ou, 'objectclass=*', $retattrs);
	$objlist = ldap_get_entries($conn, $objresult);
	for($o = 0; $o < $objlist["count"]; $o++)
	{
		echo "Found object:    ".$objlist[$o]['cn'][0]."<br>\n";
	}	
	
	
}
?>
