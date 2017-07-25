<?php
// LogareReCon class, extended from LogareReg
class LogareReCon extends LogareReg {
  // constructor
  public function __construct($conn_datas) {
    // if $conn_datas is an array, sets 'conn_datas' property, and calls setConn() method, otherwise, error
    if(is_array($conn_datas)) {
      $this->conn_datas = $conn_datas;           // adds data from parameter in 'conn_datas' property
      $this->site = $_SERVER['SERVER_NAME'];

      // if received data through POST, or GET for confirmation, creates connection
      if((isset($_POST['submit']) && isset($_POST['email'])) || (isset($_GET['mp']))) $this->setConn();

      // if 'conn' property is set
      if($this->conn!==false) {
        // if GET (for confirmation) calls getConfirm(), otherwise, calls getReCon() with form data
        if(isset($_GET['mp'])) {
          $_GET = array_map("strip_tags", array_map("trim", $_GET));        // remove external whitespace
          $this->result = $this->getConfirm($_GET['mp']);
        }
        else {
          $_POST = array_map("strip_tags", array_map("trim", $_POST));        // remove external whitespace and tags
          $this->result = $this->getReCon($_POST);
        }
      }
      else $this->result = $this->setFormReCon($_REQUEST['rc']);       // initially returns the form
    }
    else $this->result = 'The parameter should be an array';

    echo $this->result;        // return / output the data stored in $result
  }

  // this method sends the user data to his e-mail, or the message for reconfirmation
  private function getReCon($ar_post) {
    $tip = $ar_post['submit'];       // request type (recovery or confirmation)
    $email = $ar_post['email'];
    $re = $tip;                      // for data returned by this method

    // validate the e-mail address
    if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
      // If the verification code is correct
      if(isset($_SESSION['nrv']) && $_SESSION['nrv']==$ar_post['nrv']) {
        unset($_SESSION['nrv']);

         // check if email address is in the database
         $sql = "SELECT `nume`, `parola`, `pass`, `email`, `id` from `users` WHERE `email`='$email' LIMIT 1";
         $result = $this->conn->query($sql);

        // if not found, sets a message and add the form with the setFormReCon() method
        if(!$result || $result->num_rows<1) {
          $re = '<div id="logeror">There is not registration with the e-mail: <i><u>'. $email.'</u></i></div><br/>'.$this->setFormReCon($tip);
        }
        else {
          // gets the name, password, and id associated to the email
          while ($rand = $result->fetch_assoc()) {
            $nume = stripslashes($rand['nume']);
            $parola = stripslashes($rand['parola']);
            $pass = stripslashes($rand['pass']);
            $id = (int)$rand['id'];
          }

          // sends data to e-mail (recovery request)
          if($tip=='Recover') {
            // sets the subject and message
            $subiect = 'Recovery registration datas';
            $mesaj1 = "               Hy<br /> \n
          You received this email due to a request to recover your registration data on $this->site. \n\n";
        }
        else if($tip=='Confirm') {
          // sets the link for registration confirmation
          $set_url = 'http://'. $_SERVER['HTTP_HOST'].'/'. $this->dir.$this->file_log. '?mp='. $id. '_'. $parola;
          $link_confirm = '<a href="'. $set_url. '">'. $set_url. '</a>';
          // sets subject and message
          $subiect = 'Registration Confirmation';
          $mesaj1 = "               Hy<br /> \n
          You received this email due to a request to resend the link for registration confirmation.<br /> \n\n
      To confirm the registration on $this->site , click on the following link:<br /><br /> \n
      $link_confirm <br /> \n\n";
        }

          $mesaj2 = "<br />Your login data are:<br /><br /> \n
              Name = $nume <br /> \n
              Password = $pass <br /><br /> \n\n
      <center><i>If you want, visit also: <a href=\"http://coursesweb.net/\">coursesweb.net</a></i></center><br /><br /><br /> \n\n
        Have a good day<br /> \n
        With respect, Admin";
          $mesaj = $mesaj1. $mesaj2;
          if($this->sendMail($email, $this->from, $this->site, $subiect, $mesaj)) {
          $re = '<center>Your login data have been sent to: <b>'. $email. '</b>.<br />
          Check the Spamm folder, too. If you have not received the email, please contact the site administrator.
               <br /><br />Thank you, <a href="/">Home Page</a></center>';
        }
          else {
            $re = '<div id="logeror"><h2>Error at data checking.<br/>Try again.</h2></div><br />'.$this->setFormReCon($tip);
          }
        }
      }
      else {
        unset($_SESSION['nrv']);
        $re = '<h4 id="logeror">Incorrect verification code.</h4><br />'.$this->setFormReCon($tip);
      }
    }
    else $re = '<div id="logeror">Incorrect e-mail address.</div><br />'.$this->setFormReCon($tip);
    return $re;
  }

  // the method that confirms the registration
  private function getConfirm($get) {
    // get and split data (id_password) to check in database
    $ar_get = explode('_', $get);
    $id = (int)$ar_get[0];
    $parola = $ar_get[1];
    $rank = 1;
   
    $parola = $this->conn->real_escape_string($parola);      // filter to add safety

    // update to set 'rank' to the value of $rank
    $sql = "UPDATE `users` SET `rank`='$rank' WHERE `id`='$id' AND `parola`='$parola' LIMIT 1";
    if($this->conn->query($sql)) {
      // Select to check if 'rank' was updated
      $sql = "SELECT `rank` FROM `users` WHERE `id`='$id' LIMIT 1";
      $result = $this->conn->query($sql);
      $rand = $result->fetch_assoc();
      if($rand['rank']>0) $re = '<center><font color="blue"><h2>Confirmation approved</h2></font><h4>Now you can log on the site. <a href="/">Home Page</a></h4></center>';
      else  {
        $link_confirm = '<b><a href="'.$this->dir.$this->file_log.'?rc=Confirm">Click aici</a></b>';
        $re = '<center><font color="red"><h2>Confirmation approved</h2></font><h4>The URL for confirmation is incorrect</h4><br /><br /> - To request a new e-mail with the link for confirmation: '. $link_confirm. '<br /><br /><i>Or contact the site administrator.</i></center>';
      }
    }
    else $re = 'The Confirmation failed, error: '. $this->conn->error;
    $this->conn->close();
    return $re;
  }

  // the method with the form for Recovery and Confirmation
  function setFormReCon($tip) {
    $nrv = $this->setCodNr('nrv');     // calls the method that returns a verification code

    // define the form
    $re = '<div id="form_re">
  <p>Add the e-mail address you used for registration and this verification code: <b><font color="blue" size="4">'. $nrv. '</font></b></p><br />
   <form action="" method="post" onsubmit="return datReCon(this);">
   <input type="hidden" name="nrv0" value="'. $nrv. '" />
   <label for="email">E-mail: </label> <input type="text" name="email" maxlength="42" id="email" /><br /><br />
   <label for="nrv">Verification code: </label><input type="text" name="nrv" size="5" maxlength="6" id="nrv" /><br />
   <input type="submit" name="submit" value="'. $tip. '" />
   </form><br/><sub>From: <a href="http://coursesweb.net/php-mysql/" target="_blank" title="Free PHP-MySQL Course">coursesweb.net</a></sub></div>';
     return $re;
  }
}
?>