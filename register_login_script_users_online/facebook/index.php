<?php
include '../usrincls/config.php';
include 'facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array('appId' => FBID, 'secret' => FBSK));

// if logout request, delete cookies and sessions, redirect to domain root
if(isset($_GET['lout'])) {
  setcookie('fbs_'. $facebook->getAppId(), '', time()-100, '/', 'http://'. $_SERVER['SERVER_NAME']);
  session_destroy();
  header('Location: /');
  exit;
}

// get user UID
$fb_user_id = $facebook->getUser();

// get the url where to redirect the user after login
$location = $facebook->getLoginUrl();

// check if we have valid user
if ($fb_user_id) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $fb_user_profile = $facebook->api('/me');
    // set sessions for logged user
    $_SESSION['email'] = (isset($fb_user_profile['email']) && !empty($fb_user_profile['email'])) ? $fb_user_profile['email'] : 'no_email';
    $_SESSION['username'] = $fb_user_profile['name'];
    $_SESSION['fbuserid'] = $fb_user_id;
    $_SESSION['fbtoken'] = $facebook->getAccessToken();

    $obj = new Base($mysql);      // creates object instance to Base class (included in config)
    $now = time();

    $sql = "SELECT `id`, `rank` FROM `fbusers` WHERE `name`='". $_SESSION['username'] ."' LIMIT 1";
    $redb = $obj->sqlExecute($sql);

    // if row returned, if 'rank'<1 output message, else performs Update to selected ID
    if($obj->affected_rows > 0) {
      if($redb[0]['rank'] < 1) {
        echo sprintf($lsite['eror_users']['ban'], $_SESSION['username']);
        session_destroy();
        exit;
      }
      else {
        $sql = "UPDATE `fbusers` SET `ip_visit`='$obj->ip', `dtvisit`=$now, `visits`=`visits`+1 WHERE `id`='". $redb[0]['id'] ."' LIMIT 1";
        $redb = $obj->sqlExecute($sql);
      }
    } else {
      // else performs Insert
      $sql = "INSERT INTO `fbusers` (`fbuserid`, `name`, `email`, `rank`, `ip_visit`, `dtreg`, `dtvisit`) VALUES ('$fb_user_id', '". $_SESSION['username'] ."', '". $_SESSION['email'] ."', 1, '$obj->ip', $now, $now)";
      $redb = $obj->sqlExecute($sql);
    }

    // reload the parent window
    echo '<script type="text/javascript">opener.location.reload(true); self.close();</script>';
    exit;
  } catch (FacebookApiException $e) {
    $fb_user_id = NULL;
    // seems we don't have enough permissions
    // we use javascript to redirect user instead of header() due to Facebook bug
    print '<script language="javascript" type="text/javascript"> top.location.href="'. $location .'"; </script>';
    exit();
  }
} else {
  // seems our user hasn't logged in, redirect him to a FB login page
  print '<script language="javascript" type="text/javascript"> top.location.href="'. $location .'"; </script>';
  exit();
}