<?php
// Include Functions
include("../inc/connect.ini.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Payment Cancelled</title>
</head>

<body>
<h1>Payment Cancelled</h1>
<p>Your payment was Cancelled. If you have any questions please email <a href="mailto:<?php echo $email; ?>?Subject=Paypal%20Payment%20Question"><?php echo $email; ?></a></p>
</body>
</html>

