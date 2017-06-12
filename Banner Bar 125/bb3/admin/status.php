<?php
require_once ('inc/check.php');
require_once('inc/connect.ini.php');
require_once('inc/functions.ini.php');

  $id = $_GET['id'];
  $page = $_GET['page'];

//

   $from = $conn->query("SELECT email FROM $t_settings")->fetchColumn();
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: $from";

//

$txn_id = $conn->query("SELECT txn_id FROM $t_client_banners WHERE id='$id'")->fetchColumn();

$emails = $conn->query("SELECT email FROM $t_payments WHERE txn_id ='$txn_id' LIMIT 1")->fetchColumn();



  if (isset($_GET['status']) && $_GET['status'] == "approve") {
mail_outcome('approve',$emails, $txn_id);
      // get id value
      $date = date("Y-m-d H:i:s");// current date
$period_f = $conn->query("SELECT period FROM $t_client_banners WHERE id='$id'")->fetchColumn();


$period = "+".$period_f['period'];

$expi = date("Y-m-d, H:i:s", strtotime($period));

      $conn->query("UPDATE $t_client_banners SET status='active' , created='$date' ,expires='$expi' WHERE id='$id'");
      // once saved, redirect back to the index page
    header("Location: client.php?page=$page");
  } elseif ($_GET['status'] == "deny") {
mail_outcome('deny',$emails, $txn_id);

      // get id value
      $conn->query("UPDATE $t_client_banners SET status='deny' WHERE id='$id'");
      // once saved, redirect back to the index page
     header("Location: client.php?page=$page");

 }
else {
echo "Error";


}




  function mail_outcome($outcome,$emails,$txn_id) {

    global $conn, $t_settings, $website, $emails, $location;


if ($outcome == "deny") {
   $message = "<b>I'm sorry your Banner has not been approved a refund will be issued shortly</b>";

   mailman($message,$emails);


}
elseif ($outcome == "approve") {



$message = "Your Banner is now active at ".$website. "<br />";
 //  $message = "<b>This is HTML approve.</b>";
//
//
  $message .= "<a href=\"".$website."/".$location."/admin/stats.php?user=".$emails."&kt=".$txn_id."\">View Stats</a>";

   mailman($message,$emails);
}
else {
echo "Error";
}
return true;
}

function mailman($message,$emails) {

     global $conn, $website, $emails, $from, $headers;


$to = $emails;

   $subject = "Your Banner at ".$website;

   $retval = mail ($to,$subject,$message,$headers,'-f'.$from);
/*
   if( $retval == true )
   {
      echo "Message sent successfully...";
   }
   else
   {
      echo "Message could not be sent...";
   }
*/
return true;

}
?>