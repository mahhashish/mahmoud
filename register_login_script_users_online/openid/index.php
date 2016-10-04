<?php
include '../usrincls/config.php';
require 'openid.php';      // include LightOpenID class

try {
  // create object of LightOpenID class
  $openid = new LightOpenID($_SERVER['HTTP_HOST']);

  // if $openid->mode False (user not loged) set options for URL request to login with Google OpenID
  if(!$openid->mode && isset($_GET['lgw'])) {
    // set the URL to log in, according to 'lgw' in request (yahoo, google)
    $openid->identity = ($_GET['lgw'] == 'yahoo') ? 'https://me.yahoo.com' : 'https://www.google.com/accounts/o8/id';
    $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');

    // redirect to defined URL
    header('Location: ' . $openid->authUrl());
    exit;
  }
  else if($openid->mode == 'cancel') {
    // User has canceled authentication, output message, close the window with JavaScript
    echo $lsite['userpage']['notloged'] .'<script type="text/javascript">self.close();</script>';
  }
  else if($openid->validate()) {
    // if $openid->validate() is True, User loged, get array with user's data (name, e-mail)
    $usrdata = $openid->getAttributes();
    $_SESSION['email'] = (isset($usrdata['contact/email']) && !empty($usrdata['contact/email'])) ? $usrdata['contact/email'] : 'no_email';
    $_SESSION['usropenid'] = $openid->identity;       // OpenID of current loged User`s session

    // sets the different data for logged with Yahoo, or Google
    if(isset($_GET['lgw']) && $_GET['lgw'] == 'yahoo') {
      $email_ar = explode('@', $_SESSION['email']);
      $_SESSION['username'] = $email_ar[0];
      $tabledb = 'yhusers';
    }
    else {
      $_SESSION['username'] = $usrdata['namePerson/first']. '-'. $usrdata['namePerson/last'];
      $tabledb = 'gousers';
    }

    $obj = new Base($mysql);      // creates object instance to Base class (included in config.php)
    $now = time();

    // check if user already added in table
    $sql = "SELECT `id`, `rank` FROM `$tabledb` WHERE `email`='". $_SESSION['email'] ."' LIMIT 1";
    $redb = $obj->sqlExecute($sql);

    // if row returned, if 'rank'<1 output message, else performs Update to selected ID
    if($obj->affected_rows > 0) {
      if($redb[0]['rank'] < 1) {
        echo sprintf($lsite['eror_users']['ban'], $_SESSION['username']);
        session_destroy();
        exit;
      }
      else {
        $sql = "UPDATE `$tabledb` SET `ip_visit`='$obj->ip', `dtvisit`=$now, `visits`=`visits`+1 WHERE `id`='". $redb[0]['id'] ."' LIMIT 1";
        $redb = $obj->sqlExecute($sql);
      }
    } else {
      // else performs Insert
      $sql = "INSERT INTO `$tabledb` (`name`, `email`, `rank`, `ip_visit`, `dtreg`, `dtvisit`) VALUES ('". $_SESSION['username'] ."', '". $_SESSION['email'] ."', 1, '$obj->ip', $now, $now)";
      $redb = $obj->sqlExecute($sql);
    }

    // reload the parent window
    echo '<script type="text/javascript">opener.location.reload(true); self.close();</script>';
  }
  else {
    echo $lsite['userpage']['notloged'];     // User not logged
  }
}
catch(ErrorException $e) {
  echo $e->getMessage();
}