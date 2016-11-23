<?php

include "./UpdateServiceGPFR.php";
include "./GPFRClient.php";

function getUpgradeTo ($a_szApp, $a_szVersion)
{
	$l_oClient = new UpdateServiceClient("http://localhost/xmlrpc/");

	$l_aReturn		= array();
	$l_aReturn["App"]	= $a_szApp;
	$l_aReturn["Version"]	= $a_szVersion;
	$l_aReturn["Latest"]	= "false";
	$l_aReturn["UpgradeTo"]	= "n/a";
	$l_aReturn["Insecure"]	= "n/a";

	$result = $l_oClient->doGetIsUpToDate($a_szApp, $a_szVersion, $a_boLatest);
	$l_aReturn["Latest"] = $a_boLatest;

	if ($a_boLatest === true)
	{
		return $l_aReturn;
	}

	$l_oR = $l_oClient->doGetUpgradeFromVersion($a_szApp, $a_szVersion, $l_aInfo);
	
	$l_aReturn["UpgradeTo"]	= $l_aInfo["Version"];
	$l_aReturn["Insecure"]	= $l_aInfo["YourVersionIsInsecure"];

	return $l_aReturn;
}

$l_aDaphne = array
(
	"1.0",
	"1.1",
	"1.2",
	"1.3.1",
	"1.3.2"
);

$l_aThelma = array
(
	"3.0_rc1",
	"3.0_rc2",
	"3.0_rc3",
	"3.0",
	"3.1",
	"3.2",
	"3.3_rc1",
	"3.3_rc2",
	"3.3"
);

foreach ($l_aDaphne as $l_szVersion)
{
	$g_aOutput[] = getUpgradeTo("daphneWeb", $l_szVersion);
}

foreach ($l_aThelma as $l_szVersion)
{
	$g_aOutput[] = getUpgradeTo("thelmaWeb", $l_szVersion);
}

echo "App       | Version | Latest? | Upgrade To | Insecure?\n";
echo "------------------------------------------------------\n\n";

foreach ($g_aOutput as $g_aInfo)
{
	printf("%9.9s | %7.7s | %7.7d | %10.10s | %9.9d\n",
		$g_aInfo["App"],
		$g_aInfo["Version"],
		$g_aInfo["Latest"],
		$g_aInfo["UpgradeTo"],
		$g_aInfo["Insecure"]
	);
}

?>
