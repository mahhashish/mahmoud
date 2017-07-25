<?php
// LogareReg class, extended from Logare
class LogareReg extends Logare {
  // properties
  public $from = 'From: contact@domain.net';       // administrator e-mail
  private $rank;             // determines rank and confirmation of registration
  private $nr_count = 1;      // If the value is different from 1, allow the creation of multiple accounts with same IP
  protected $site;           // website name
  protected $result = '';    // the result returned by this class
  protected $ip;             // the user IP when registers

  // constructor
  public function __construct($conn_datas, $rank=0) {
    // if $conn_datas is an array, sets 'conn_datas' property and calls setConn() method, otherwise, error
    if(is_array($conn_datas)) {
      // sets the properties values
      $this->conn_datas = $conn_datas;
      $this->rank = intval($rank);
      $this->ip = isset($_COOKIE['ip']) ? $_COOKIE['ip'] : $_SERVER['REMOTE_ADDR'];
      $this->site = $_SERVER['SERVER_NAME'];

      // if received data through POST, creates connection
      if(isset($_POST['submit']) && isset($_POST['nrv'])) $this->setConn();

      // if 'conn'is set
      if($this->conn!==false) {
        // if received data from the registration form, call the method getReg()
        if(isset($_POST['nume']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pass2']) && isset($_POST['nrv'])) {
          $_POST = array_map("strip_tags", array_map("trim", $_POST));        // remove external whitespace and tags
          $this->result = $this->getReg($_POST);      
        }
        else $this->result = 'Incorrect form fields';
      }
      else $this->result = $this->setFormReg();         // returns only the form
    }
    else $this->result = 'The first parameter should be an array';

    echo $this->result;           // Return the $result property
  }

  // the method sends the e-mail
  protected function sendMail($to, $from, $from_name, $sub, $msg){
    $eol = "\r\n";             // Used for new line

    // Sets headers for email
    $headers = "From: " . $from_name . "<" . $from . ">".$eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-type: text/html; charset=iso-8859-1" . $eol;

    // If the mail is sent successfully returns True, otherwise False
    if (mail($to, stripslashes($sub), stripslashes($msg), $headers)) return true;
    else return false;
  }

  // the method that adds user's data in database
  private function addUser($nume, $pass, $email) {
    $parola = md5($pass);
    $nume1 = $nume;

    // filter to add safety in database
    $nume = $this->conn->real_escape_string($nume);
    $pass = $this->conn->real_escape_string($pass);
    $email = $this->conn->real_escape_string($email);
  
    $sql = "INSERT INTO `users` (`nume`, `parola`, `email`, `rank`, `ip_reg`, `ip_visit`, `datereg`, `pass`) VALUES ('$nume', '$parola', '$email', '$this->rank', '$this->ip', '$this->ip', NOW(), '$pass')";
    if($this->conn->query($sql) === TRUE) {
      $mesaj = '<center><h1>Succes!</h1>
    <font size="4">Thank you <b><font color="blue">'. $nume1. '</font></b>, the registration has been completed successfully.</font><br /><br />You can Log in.</center>';

      $id = $this->conn->insert_id;      // gets the auto-inserted ID
  
      // sets the link for registration confirmation
      $set_url = 'http://'. $_SERVER['HTTP_HOST'].'/'. $this->dir.$this->file_log. '?mp='. $id. '_'. $parola;
      $link_confirm = '<a href="'. $set_url. '">'. $set_url. '</a>';

      if($this->rank===0) {
        // sets subject and message
        $subiect = 'Confirm the registration on: '.$this->site;
        $mesaj = "               Hy <br />
    You received this message because you need to confirm your registration on the website $this->site <br /><br />
To confirm the registration, click on the following link (<i>or copy it in the address bar of your browser</i>):<br /><br />
      <center><b> $link_confirm </b></center><br /><br />
    Your login data:<br /><br />
      Nume = $nume1 <br />
      Parola = $pass <br /><br /><br />
      <center><i>If you want, visit also <a href=\"http://coursesweb.net/\">coursesweb.net</a></i></center><br /><br /><br />
<i>Thanks, respectfully,<br /> Admin</i><br />";

        // sends the email
        $email = stripslashes($email);
        if($this->sendMail($email, $this->from, $this->site, $subiect, $mesaj)) {
          $mesaj = '<center><h3>Registration performed successfully</h3>A message with a link to confirm your registration will be send to the e-mail<u> '. $email. '</u>.<br/><br/> If you have not received the email, check the Spamm folder, too.<br/><br/> After confirmation you can log in.</center>';
        } 
        else $mesaj = 'Error, check if you entered the correct data.';
      }
    }
    else {
      $mesaj = '<h1>Error:</h1><i>'. $this->conn->error. ' </i><br />Your registration for the name <b>'. $nume1. '</b>, not performed.';
    }
    return $mesaj;
  }

  // sets the string with the error
  private function strEror($str) { return '<div id="rerror">'.$str.'</div>'.$this->setFormReg(); }

  private function getReg($ar_post) {
    $nume = $ar_post['nume'];
    $pass = $ar_post['pass'];
    $email = $ar_post['email'];
    $re = false;

  // check if there is already a session with registration
  if(isset($_SESSION['registered'])) $re = $_SESSION['registered'];
    // Check if the password is the same as in "Retype password"
    else if($pass!=$ar_post['pass2']) $re = $this->strEror('You must write the same password in the field "<b>Retype password</b>"');
    // check the string to contain only the allowed characters
    else if(!$this->checkStr($nume) || !$this->checkStr($pass)) $re = $this->strEror('The data should contain only letters, numbers, "-" and "_"');
    // Verifica lungimea numelui
    else if(strlen($nume)<3 || strlen($nume)>32) $re = $this->strEror('The name must contains between 3 and 32 characters<br/> Only letters, numbers, "-" and "_"');
    // Verifica lungimea parolei
    else if(strlen($pass)<7 || strlen($pass)>18) $re = $this->strEror('The password must contains between 7 and 18 characters<br/>Only letters, numbers, "-" and "_"');
    // Validate the email
    else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) $re = $this->strEror('Incorrect email address');
    // check the verification code
    else if($_SESSION['nrv']!==$ar_post['nrv']) {
      $re = $this->strEror('Incorrect verification code. '.$ar_post['nrv']);
      $this->setCodNr('nrv');
    }

    // if $re is False check the name and e-mail in database
    else if($re===false) {
      // filter with real_escape_string()
      $nume = $this->conn->real_escape_string($nume);
      $email = $this->conn->real_escape_string($email);

      $sql = "SELECT `nume`, `email`, `ip_reg`, `ip_visit` FROM `users` WHERE `nume`='$nume' OR `email`='$email' OR `ip_reg`='$this->ip' OR `ip_visit`='$this->ip' LIMIT 1";
      $result = $this->conn->query($sql);

      // check if name and email already in database
      if($result->num_rows>0) {
        while ($rand = $result->fetch_assoc()) {
          $nume_bd = stripslashes($rand['nume']);
          $email_bd = stripslashes($rand['email']);
          $ip_reg_bd = stripslashes($rand['ip_reg']);
          $ip_visit_bd = stripslashes($rand['ip_visit']);
        }
        // Sets error if the user name, email-ul, ip_reg, or ip_visit already registered
        if(strcasecmp($nume_bd, $nume)==0) $re = $this->strEror("The name: <u>$nume</u> already registered, please choose another one.");
        else if (strcasecmp($email_bd, $email)==0) $re = $this->strEror("The e-mail: <u>$email</u> has already been used for registration");
        else if ($this->nr_count==1 && (strcasecmp($ip_reg_bd, $this->ip)==0 || strcasecmp($ip_visit_bd, $this->ip)==0)) $re = $this->strEror('There is already a registration with your IP.<br />If you think that is an error, contact the administrator');
      }
      else {
        // calls addUser() methot to add the new account in database, and sets a session with the registration
        $re = $this->addUser($ar_post['nume'], $pass, trim($ar_post['email']));
        $_SESSION['registered'] = $re;
      }
    }
    $this->conn->close();
    return $re;
  }

  // This method create a verification code in the Session $ses, and return it
  protected function setCodNr($ses) {
    // sets the code with the current date-time
    $data_nrv = date(" j-F-Y, g:i a ");
    $nr_v = md5($data_nrv);
    if(isset($_SESSION[$ses])) { unset($_SESSION[$ses]); }
    $_SESSION[$ses] = substr($nr_v, 3, 5);
    return $_SESSION[$ses];
  }

  // method that sets the registration form
  private function setFormReg() {
    // if there is a session with the registration, return it. Otherwise return the form
    if(isset($_SESSION['registered'])) $re = $_SESSION['registered'];
    else {
      // keep the data from form fields (not need rewriting)
      $nume = isset($_POST['nume']) ? $_POST['nume'] : '';
      $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
      $pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
      $email = isset($_POST['email']) ? $_POST['email'] : '';
      $nrv = $this->setCodNr('nrv');     // seteaza cod de verificare

      $re = '<h2>Registration</h2><div id="form_re">
           <p><br /><b> - Add your registration data and this code: <font color="blue" size="4">'. $nrv. '</font></b></p>- <i>You must use a valid e-mail address, you will receive a message to confirm the registration.</i><hr style="width:88px;" /><br />
      <form action="" method="post" onsubmit="return datReg(this)">
       <input type="hidden" name="nrv0" value="'. $nrv. '" />
       <label for="nume">Name: &nbsp;</label><input type="text" name="nume" maxlength="32" id="nume" value="'.$nume.'" /><br /><br />
       <label for="pass">Password: </label><input type="password" name="pass" maxlength="18" id="pass" value="'.$pass.'" /><br /><br />
       <label for="pass2">Retype password: </label><input type="password" name="pass2" maxlength="18" id="pass2" value="'.$pass2.'" /><br /><br />
       <label for="email">E-Mail: </label><input type="text" name="email" maxlength="42" id="email" value="'.$email.'" /><br /><br />
       <label for="nrv">Verification code: </label><input type="text" name="nrv" size="5" maxlength="6" id="nrv" /><br /><br />
       <input type="submit" name="submit" value="Register" />
      </form><br/><sub>From: <a href="http://coursesweb.net/php-mysql/" target="_blank" title="Free PHP-MySQL Course">coursesweb.net</a></sub></div>';
      }
    return $re;
  }
}
?>