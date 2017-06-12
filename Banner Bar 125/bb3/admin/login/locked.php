<style type='text/css'>
.center{text-align:center;}
#button1{ 
		
		
		width:125px;
		height:31px;
		background:#666666 url('../img/button.png') no-repeat;
		text-align:center;
		line-height:31px;
		color:#FFFFFF;
		cursor:pointer;
		font-size:11px;
		font-weight:bold
	}
</style>

<?php
if (isset($_SESSION['alert'])) {
    $msg = $_SESSION['alert'];
    echo "<script>alert('$msg');</script>";
    unset($_SESSION['alert']);
}

// connect to the database
require_once('../inc/connect.ini.php');
require_once('../inc/functions.ini.php');
//$keylock = "NULL";
unset($key);
/*
unset($email);
*/
$keylock = $conn->query("SELECT keylock FROM $t_settings")->fetchColumn();
//echo $keylock;
     //
$postb = isset( $_POST['resendb'] )? $_POST['resendb']: false;

if( $postb ){
$from = "BannerBar@noreply.com";

       $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: $from";

$subject = "Unlock code for Banner Bar";
$message = "Hello! Your 'Banner Bar' login page unlock link as requested<br /> <a href=\"".$website."/".$location."/admin/login/locked.php?email=$email&amp;key=$keylock\">Click Here</a> to reset your login page";



 $to = $email;

  mail($to, $subject, $message, $headers);
    $msg = "Unlock email resent";
            $_SESSION['alert'] = $msg;
        echo '<meta http-equiv="refresh" content="0;URL=L-login.php">';

unset($postb);

}

//

if (isset($_GET['email']) && preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $_GET['email'])) {
    $email1 = $_GET['email'];
}
if (isset($_GET['key']) && (strlen($_GET['key']) == 32))
//The Activation key will always be 32 since it is MD5 Hash
    {
    //$key = $_GET['key'];
   $key1 = $_GET['key'];
}


if (isset($email1) && isset($key1)) {
    // Update the database to set the "activation" field to null
    //mysql_query("UPDATE settings SET attempts='$count', keylock='$key'") or die(mysql_error());
   $query   = "UPDATE $t_settings SET keylock='NULL',attempts='0' WHERE email='$email1' AND keylock='$key1'";
 $result = $conn->query($query);
        
$affectedRows = $result->rowCount();

    if ($affectedRows == 1) //if update query was successfull
        {
        echo "<p class=\"center\">Login page successfully unlocked<br /><a href=\"../index.php\">Login</a></p>";
    } elseif ($keylock == "NULL") {
        echo "<div class=\"center\">Login Page already Unlocked<br /><a href=\"../index.php\">Login</a></div>";
    } 
/*

else {
        echo "<div class=\"center\"><img src=\"../img/Protect.png\" width=\"128\" height=\"128\" alt=\"Closed Padlock\" title=\"Locked out!\" /><br />
You have made 3 invalid login attempts!!!<br /> This page is now locked and can only be unlocked by the site Administrator!</div>";
    }
*/
}
else {
 echo "<div class=\"center\"><img src=\"../img/Protect.png\" width=\"128\" height=\"128\" alt=\"Closed Padlock\" title=\"Locked out!\" /><br />
You have made 3 invalid login attempts!!!<br /> This page is now locked and can only be unlocked by the site Administrator!</div>";
echo "<br /><button  id=\"button1\" type=\"submit\" name=\"resendb\" value=\" Resend unlock email \">Resend unlock email</button></div></p>";
}
?>