<?php
include('../inc/connect.ini.php');
include('../inc/config.ini.php');
include('functions.php');
$w           = $_POST["os0"];
$item_amount = $_POST["option_amount" . $w . ""];
if ($sandbox) {
    $sandbox = "sandbox.";
}
$result        = $conn->query("SELECT * FROM $t_paypal");
$row           = $result->fetch(PDO::FETCH_ASSOC);
$email_subject = $row['email_subject'];
$paypal_email  = $row['paypal_email'];
$item_name     = $row['item_name'];
$email_from = $conn->query("SELECT email FROM $t_settings")->fetchColumn();
$email_to      = $email_from;


//// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {
    // Firstly Append paypal account to querystring
    $querystring .= "?business=" . urlencode($paypal_email) . "&";
    // Append amount& currency (£) to quersytring so it cannot be edited in html
    //The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
    $querystring .= "item_name=" . urlencode($item_name) . "&";
    $querystring .= "amount=" . urlencode($item_amount) . "&";
    //loop for posted values and append to querystring
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $querystring .= "$key=$value&";
    }
    // Append paypal return addresses
    $querystring .= "return=" . urlencode(stripslashes($return_url)) . "&";
    $querystring .= "cancel_return=" . urlencode(stripslashes($cancel_url)) . "&";
    $querystring .= "notify_url=" . urlencode($notify_url) . "?name=" . urlencode($name);
    // Append querystring with custom field
    //$querystring .= "&custom=".USERID;
    // Redirect to paypal IPN
    header('location:https://www.' . $sandbox . 'paypal.com/cgi-bin/webscr' . $querystring);
    exit();
} else {
    // Response from Paypal
    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
        $req .= "&$key=$value";
    }
    // assign posted variables to local variables
    list($ip, $name, $email) = explode('|', $_POST['custom']);
    //file_put_contents('name.txt', $ip.$name.$email);
    $data['item_name']         = $_POST['item_name'];
    $data['payment_status']    = $_POST['payment_status'];
    $data['payment_amount']    = $_POST['mc_gross'];
    $data['payment_currency']  = $_POST['mc_currency'];
    $data['txn_id']            = $_POST['txn_id'];
    $data['name']              = $name;
    $data['email']             = $email;
    $data['receiver_email']    = $_POST['receiver_email'];
    $data['payer_email']       = $_POST['payer_email'];
    $data['custom']            = $ip;
    $data['option_selection1'] = $_POST['option_selection1'];
    $data['option_selection2'] = $_POST['option_selection2'];
    $data['option_selection3'] = $_POST['option_selection3'];
    //post back to PayPal system to validate (replaces old headers)
    $header                    = "POST /cgi-bin/webscr HTTP/1.1\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Host: www." . $sandbox . "paypal.com\r\n";
    $header .= "Connection: close\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen('ssl://www.' . $sandbox . 'paypal.com', 443, $errno, $errstr, 30);
    if (!$fp) {
        // HTTP ERROR
    } else {
        fputs($fp, $header . $req);
        while (!feof($fp)) {
            $res = fgets($fp, 1024);
            $res = trim($res); //NEW & IMPORTANT
            //@mail("ianjgough@aol.com", "PAYPAL DEBUGGING", "res reached".$res);
            if (strcmp($res, "VERIFIED") == 0) {
                // Used for debugging
                //@mail("ianjgough@aol.com", "Verified", "Verified Response<br />data = <pre>.print_r($post, true).</pre>");
                // Validate payment (Check unique txnid & correct price)
                $valid_txnid = check_txnid($data['txn_id']);
                $valid_price = check_price($data['payment_amount'], $data['item_name']);
                // PAYMENT VALIDATED & VERIFIED!
                if ($valid_txnid && $valid_price) {
                    $orderid = updatePayments($data);
                    if ($orderid) {
                        // Payment has been made & successfully inserted into the Database
                        $email_message = "Hello! <br />You have just received a new banner advert from {@NAME} <br />Email: <a href=\"mailto:{@EMAIL}\">{@EMAIL}</a> <br />IP address: {@IP} <br />website <a href=\"{@WEBSITE}\">{@WEBSITE}</a> <br />Banner <br /><img src=\"{@IMG}\">";
                        $email_message = str_replace("{@NAME}", $name, $email_message);
                        $email_message = str_replace("{@EMAIL}", $email, $email_message);
                        $email_message = str_replace("{@IP}", $ip, $email_message);
                        $email_message = str_replace("{@WEBSITE}", $_POST['option_selection2'], $email_message);
                        $email_message = str_replace("{@IMG}", $_POST['option_selection3'], $email_message);
                        // create email headers
                        // Always set content-type when sending HTML email
                        $headers       = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
                        $headers .= 'From: ' . $email_from . "\r\n" . 'Reply-To: ' . $email . "\r\n";
                        @mail($email_to, $email_subject, $email_message, $headers);
                        //                                
                    } else {
                        // Error inserting into DB
                        @mail($email_to, "BANNER BAR DEBUGGING", "PAYPAL PAYMENT ERROR INSERTING INTO THE DATABASE", $headers);
                    }
                } else {
                    // Payment made but data has been changed
                    @mail($email_to, "BANNER BAR DEBUGGING", "PAYPAL PAYMENT MADE BUT DATA CHANGED", $headers);
                }
            } else if (strcmp($res, "INVALID") == 0) {
                // PAYMENT INVALID & INVESTIGATE MANUALY! 
                // E-mail admin or alert user
                // Used for debugging
                @mail($email_to, "BANNER BAR DEBUGGING", "PAYPAL PAYMENT INVALID", $headers);
            }
        }
        fclose($fp);
    }
}
?>