<?php
/*
	Swekey Authentication Frame
	(c) Musbe, Inc. 2008
*/

// $Id$

	// REQUIRED: Establish whatever environment is needed for the application
	function swekeyEnvironment () {
		if (!defined('_ALIRO_ABSOLUTE_PATH')) {
			$swekeydir = dirname(__FILE__);
			$extclassesdir = dirname($swekeydir);
			$basedir = dirname($extclassesdir);
			require_once($basedir.'/administrator/aliro.php');
			aliro::getInstance()->startup(false);
		}
	}

	//security validation
	foreach ($_GET as $key => $val)
	{
		if ($val != htmlspecialchars($val))
		{
			header ($_SERVER['SERVER_PROTOCOL'].' 403 Not Authorised');
			echo 'Invalid query - not authorised';
			exit;
		}
	}

	// to verify that the file is accesible
	if (!empty($_GET['verify']))
	{
		if (is_numeric($_GET['verify'])) echo $_GET['verify'];
		exit;
	}

	header("Cache-Control: no-cache, must-revalidate");

	swekeyEnvironment();
	$swekey = new my_swekey();

    if (isset($_GET['swekey_tokens']))
        $swekey_tokens = $_GET['swekey_tokens'];

    if (isset($_GET['swekey_ids']))
        $swekey_ids = $_GET['swekey_ids'];

    // very first call
    if (! isset($swekey_tokens) && ! isset($swekey_ids))
    {
        $_SESSION['swekey_authframe']['ids'] = "";
		$_SESSION['swekey_authframe']['session_save_path'] = "";
    }
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Swekey Authentication Frame</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
  </head>

<body style="background-color:transparent;margin:0;padding:0;">
<script type="text/javascript" src="swekey.js"></script>
<?php

echo $swekey->Swekey_InsertPlugin();

if (! isset($_SESSION['swekey_authframe']))
	$_SESSION['swekey_authframe'] = array();

if (! isset($_SESSION['swekey_authframe']['ids']) && ! isset($swekey_ids))
	$swekey_ids='';

// We have no swekey connected so no need to 
// calculate the tokens
if (isset($swekey_ids) && empty($swekey_ids))
{
	$_SESSION['swekey_authframe']['ids'] = '';
	$swekey_tokens = '';
	unset($swekey_ids);
}

// Check that we are not doing and auth in antother page
if (isset($swekey_ids) && (! isset($_SESSION['swekey_authframe']['auth_started']) || (time() - $_SESSION['swekey_authframe']['auth_started']) > 3))
{
	//error_log("authframe calculating tokens for $swekey_ids\n", 3, "/qwe.log");
	$_SESSION['swekey_authframe']['ids'] = $swekey_ids;
	$_SESSION['swekey_authframe']['rt'] = $swekey->Swekey_GetFastRndToken();
	$_SESSION['swekey_authframe']['auth_started'] = time();
	?>		
	<script type="text/javascript">
	var tokens = "";
	var ids = Swekey_ListKeyIds();
	var connected_keys = ids.split(",");
 	for (i in connected_keys) 
	    if (connected_keys[i] != null && connected_keys[i].length == 32)
		    tokens += connected_keys[i] + Swekey_GetSmartOtp(connected_keys[i], "<?php echo $_SESSION['swekey_authframe']['rt'];?>");
		    
	window.location.search = "?session_id=<?php echo session_id();?>&use_file=0&swekey_tokens=" + tokens;
	</script>
	</body>
	</html>
	<?php
	exit;
}

if (isset($swekey_tokens) && (isset($_SESSION['swekey_authframe']['rt']) || empty($swekey_tokens)))
{
//	error_log("authframe verifying tokens\n", 3, "/qwe.log");
	$_SESSION['swekey_authframe']['valid_ids'] = array();

	while (strlen($swekey_tokens) >= 32 + 64)
	{
		$id = substr($swekey_tokens, 0, 32);		
		$otp = substr($swekey_tokens, 32, 64);
		$swekey_tokens = substr($swekey_tokens, 32 + 64);		

        if ($swekey->Swekey_CheckSmartOtp($id, $_SESSION['swekey_authframe']['rt'], $otp))
            $_SESSION['swekey_authframe']['valid_ids'][sizeof($_SESSION['swekey_authframe']['valid_ids'])] = $id;
	}

	unset($_SESSION['swekey_authframe']['rt']);
	unset($_SESSION['swekey_authframe']['auth_started']);	
}

echo("<p>");
if (! empty($_SESSION['swekey_authframe']['valid_ids']))
	foreach ($_SESSION['swekey_authframe']['valid_ids'] as $key) 
		echo "$key<br/>";

//foreach ($_SESSION['swekey_authframe']['valid_ids'] as $key) error_log("\$_SESSION['swekey_authframe']['valid_ids']  $key");

		
echo("done<br/></p>");


?>	

<script type="text/javascript">	

function Refresh()
{
	var ids = Swekey_ListKeyIds();
	if (ids != "<?php echo $_SESSION['swekey_authframe']['ids'];?>")
		window.location.search = "?session_id=<?php echo session_id();?>&use_file=0&swekey_ids=" + ids;
	else
		setTimeout("Refresh()", 1000);
}

function ForceRefresh()
{
	var ids = Swekey_ListKeyIds();
	if (ids != "")
		window.location.search = "?session_id=<?php echo session_id();?>&use_file=0&swekey_ids=" + ids;
}

Refresh();
setTimeout("ForceRefresh()", 1000 * 60); // we reload every minute to the authentication does not expire

</script>     
</body>
</html>
