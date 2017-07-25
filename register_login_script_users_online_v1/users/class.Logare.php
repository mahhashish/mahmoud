<?php
// Logare class Logare
class Logare {
  // properties
  protected $conn = false;           // store the connection to mysql
  protected $conn_datas = array();    // will contain the data for connecting to mysql
  protected $sid;                   // for the session ID
  protected $nr_logs = 1;            // for the current number of login attempts
  public $users = array();           // will store the total users, last registered, and online users
  protected $eror = false;          // to store the errors
  protected $ip;                    // for the user IP (the IP saved in cookie or the current IP)
  public $dir = 'users/';          // the directory that contains the classes for this script
  protected $file_log = 'index.php';   // the file in which the instances of the classes are created
  public $logat = '';               // stores the login form or the "Welcome" message
  public $js = '';                 // for the code that include the file with JavaScript functions

  // constructor
  public function __construct($conn_datas) {
    // daca parametrul e array
    if(is_array($conn_datas)) {
      $this->conn_datas = $conn_datas;        // add data from parameter in 'conn_datas' property
      $this->sid = session_id();             // add the session ID in $sid property

      // define the $js property
      $this->js = '<script src="'. $this->dir. 'logare.js" type="text/javascript"></script>';

      // if no $_POST['ajax'], define the login form in "logat" property
      if(!isset($_POST['ajax'])) $this->logat = '<form action="" method="post" id="log_form"> Name: &nbsp; &nbsp; &nbsp; <input type="text" name="nume" id="nume" size="12" maxlength="30" /> &nbsp; <input type="submit" name="login"  class="submit" value="Login" /><br /> Password: <input type="password" name="pass" id="pass" size="11" maxlength="30" /> <label for="rem" id="lrem"><input type="checkbox" name="rem" id="rem" />Remember</label><br/><br/>
  <a href="'. $this->dir.$this->file_log. '?rc=Recover" title="Recover data" id="recdat">Recover data</a>
  <span id="log_reg"><a href="'. $this->dir.$this->file_log. '?submit=Register" title="Register" id="linkreg">Register</a></span></form>'.$this->js;

      // if $_COOKIE['ip'] exists, get the IP from cookie, else, get the current IP and save it in cookie
      if(isset($_COOKIE['ip'])) $this->ip = $_COOKIE['ip'];
      else {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        setcookie("ip", $this->ip, time()+60*60*24*100, "/");
      }

      $this->setConn();       // calls setConn() method (that sets the mysql connection)

      // if the connection to database is set ('conn' property isn't false)
      if($this->conn!==false) {
        // if there is data from the login form, calls the getLogin() method
        if(isset($_POST['login']) && isset($_POST['nume']) && isset($_POST['pass'])) {
          $_POST = array_map("trim", $_POST);        // removes whitespace from the beginning and end
          $this->getLogin($_POST);
        }
        // if request for logout ($_GET['lout']), calls logOut() method
        else if(isset($_GET['lout'])) $this->logOut();
        else $this->setLogat();        // else, calls setLogat() method that sets the $logat property

        if($this->nr_logs>1) $this->logOut(2);        // $nr_logs>1 means two logins with the same name. Logout the user
      }
    }
    else $this->eror = 'The argument for class instance must be an Array';

    // if $eror property isn't false, add it in $logat property
    if($this->eror!==false) $this->logat = '<div id="logeror">'.$this->eror. '</div>'. $this->logat;
  }

  // method that create the connection to mysql
  public function setConn() {
    // if the connection is successfully established
    if($conn = new mysqli($this->conn_datas['host'], $this->conn_datas['user'], $this->conn_datas['pass'], $this->conn_datas['bdname'])) {
      $sql = "SET NAMES 'utf8'";
      $conn->query($sql);
      $this->conn = $conn;          // add the connection in the $conn property
    }
    else if (mysqli_connect_errno()) $this->eror = 'MySQL connection failed: '. mysqli_connect_error();
    return $this->conn;
  }

  // method that sets the $logat property (the $sl is passed to the call of the userOn() method)
  private function setLogat($sl=0) {
    // If the user is stored in cookie, add the data in session
    if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])) {
      $_SESSION['nume'] = $_COOKIE['cookname'];
      $_SESSION['parola'] = $_COOKIE['cookpass'];
    }

    // if the name and password are stored in session
    if(isset($_SESSION['nume']) && isset($_SESSION['parola'])) {
      // calls the confirmUser() method to confirm if the name and passord are valid
      if($this->confirmUser($_SESSION['nume'], $_SESSION['parola'])===0) {
        $this->logat = '<div id="logat">Welcome <b>'.$_SESSION['nume'].'<br />Click <a href="'.$this->dir.$this->file_log.'?usr='.$_SESSION['nume'].'" id="idpp">Personal page</a><br/><a href="'.$_SERVER['PHP_SELF'].'?lout=lo">LogOut</a></b></div>'.$this->js;
      }
      else {
        // else, the variables are incorrect, calls logOut() to delete the session and cookies
        $this->logOut(0);
        $this->logat = 'Incorrect data logging session';
      }
    }

    // calls the method that gets and adds total users, last and registered users in $users property
    $this->usersOn($sl);
  }

  // method to check if the string contains only the allowed characters
  protected function checkStr($str) {
    $allow = '/^([A-Za-z0-9_-]+)$/';
    if(preg_match($allow, $str)) return true;
    else return false;
  }

  // method that checks data from the login form (passed in parameter) and from the database
  private function getLogin($ar_post) {
    // check for allowed characters
    if(!$this->checkStr($ar_post['nume']) || !$this->checkStr($ar_post['pass'])) $this->eror = 'The data should contain only letters, numbers, "-" and "_"';

    // check the length of the name
    else if(strlen($ar_post['nume'])<3 || strlen($ar_post['nume'])>32) $this->eror = 'Name must be between 3 and 32 characters';

    // check the length of the password
    else if(strlen($ar_post['pass'])<7 || strlen($ar_post['pass'])>18) $this->eror = 'Password must be between 7 and 18 characters';
    else {
      // Check and register with userTemp() method the log in attempt
      $continua = $this->userTemp($ar_post['nume']);

      if($continua==='continue') {
        $md5pass = md5($ar_post['pass']);    // encript the password
        $re = $this->confirmUser($ar_post['nume'], $md5pass);      // uses confirmUser() to check if name and password are correct

        // sets 'eror' if name or password are incorrect
        if($re===1) $this->eror = 'The name <b>'. stripslashes($ar_post['nume']). '</b> not registered';
        else if($re===2) $this->eror = 'Incorrect password';
        else if($re===3) {
          exit('<center><h4 style="color:red">Registration for <u>'. stripslashes($ar_post['nume']). '</u> is unconfirmed.</h4>Check your e-mail used for registration (including in Spamm directory), for the message with the confirmation link.<br /><br />If you wish to request a new confirmation e-mail <a href="'.$this->dir.$this->file_log.'?rc=Confirm">Click here</a></center>');
        }
        else {
          // user name and password are correct
          // if the "Remember" checkbox is checked, sets 2 cookies, for name and password (expires in 100 days)
          if(isset($ar_post['rem'])) {
            setcookie("cookname", $_SESSION['nume'], time()+60*60*24*100, "/");
            setcookie("cookpass", $_SESSION['parola'], time()+60*60*24*100, "/");
          }

          // UPDATE `ip_visit` with the user's IP, and the `visits` number
          $sql = "UPDATE `users` SET `ip_visit`='$this->ip', `visits`=`visits`+1 WHERE `nume`='". $_SESSION['nume']. "' LIMIT 1";
          $this->conn->query($sql);

          $this->setLogat(1);        // calls the method that sets $logat property
        }
      }
      else {
        // Sets a message with the remaining time to a new allowed authentication attempt
        $continua = floor($continua/60). ' minute, '. ($continua%60). ' secunde';
        $this->eror = 'Exceeding number of login attempts.<br/>You can retry after:<br /><b>'. $continua. '</b>';
      }
    }
  }
 
  // this method checks the name and password in the database,
  // if they are correct returns 0, otherwise 1 or 2, indicating the error
  private function confirmUser($nume, $parola) {
    // apply real_escape_string() to filter data
    $nume_db = $this->conn->real_escape_string($nume);

    // Check if the name is in the database
    $sql = "SELECT `id`, `parola`, `rank`, DATE_FORMAT(`datevisit`, '%M %D, %Y, %H:%i') AS datavisit FROM `users` WHERE `nume`='$nume_db' LIMIT 1";
    $result = $this->conn->query($sql);
    if(!$result || $result->num_rows<1) return 1;         // Indicates name not confirmed
    else {
       // Find password that is associated with the name
      $dbarray = $result->fetch_assoc();
      $dbarray['parola']  = stripslashes($dbarray['parola']);
  
      // Check if the user is confirmed
      if ($dbarray['rank']==0) return 3;  // Registration has not been confirmed

      // if the password is the same as that found in the database
      else if($parola==$dbarray['parola']) {
        // adds in session name, parola, id, rank, and last-visit-date (if isn't added)
        $_SESSION['nume'] = $nume;
        $_SESSION['parola'] = $parola;
        $_SESSION['idusr'] = $dbarray['id'];
        $_SESSION['rank'] = $dbarray['rank'];
        if(!isset($_SESSION['datavisit'])) $_SESSION['datavisit'] = $dbarray['datavisit'];
        return 0;          // name and password confirmed
      }
      else return 2;    // indicating incorrect password
    }
  }

  // this method deletes rows in the 'user_temp' table, older than 10 minutes
  // if the user already tried 3 times to log in, blocks that name for 10 minutes
  private function userTemp($nume) {
    $dt = time();
    $timp_expir = $dt-600;

    // deletes rows in the 'user_temp' table, older than 10 minutes
    $sql = "DELETE FROM `user_temp` WHERE `dt`<$timp_expir";
    $this->conn->query($sql);

    $nume = $this->conn->real_escape_string($nume);     // Filter the name to add safe

    // add / increment number of attempt by 1, and updates the date-time
    $sql = "INSERT INTO `user_temp` (`nume`, `ip`, `dt`) VALUES ('$nume', '$this->ip', $dt) ON DUPLICATE KEY UPDATE `nri`=`nri`+1";
    $this->conn->query($sql);

    // check if it was performed UPDATE (existing names) [ mysql_affected_rows()=2]
    if($this->conn->affected_rows==2) {
      // select to get the number of attempts
      $sql = "SELECT `nume`, `nri`, `dt` FROM `user_temp` WHERE `nume`='$nume' LIMIT 1";
      $result = $this->conn->query($sql);

      if(!$result || $result->num_rows<1) return 'continue';
      else {
        // get the number of login attempts, 'nri'
        $tbarray = $result->fetch_assoc();
        $nri = $tbarray['nri'];
        if($nri<3) return 'continue';
        else {
          $timp = 600 - ($dt - $tbarray['dt']);
          return $timp;    // Indicates number of attempts exceeded, returns the number of seconds to wait
        }
      }
    }
    else return 'continue';         // otherwise, it was performed INSERT
  }

  // this method gets the total number of users, last registered user, and online users
  private function usersOn($sl) {
    $re = array('total'=>0, 'last'=>'', 'online'=>0);        // this array will be added in $users property
    $dt = time();
    $timp_expir = $dt-120;         // Current time minus 2 minutes

    // deletes rows in the 'useron' table, older than 2 minutes
    $sql = "DELETE FROM `useron` WHERE `dt`<$timp_expir";
    $this->conn->query($sql);

    // If the user is logged in, insert or update him in 'useron'
    if(isset($_SESSION['nume'])) {
      $nume = $this->conn->real_escape_string($_SESSION['nume']);     // Filter the name to add safe

      // add the users, or if already in the table, update date-time
      $upd_sid = ($sl==1) ? ", `sid`='$this->sid'" : '';       //if $sl is 1 (the user is logging) sets to update the SID too
      $sql = "INSERT INTO `useron` (`nume`, `sid`, `dt`) VALUES ('$nume', '$this->sid', $dt) ON DUPLICATE KEY UPDATE `dt`=$dt". $upd_sid;
      $this->conn->query($sql);
    }

    // select that gets the total number of users, last registered user, and online users
    $sql = "SELECT `useron`.`nume`, `useron`.`sid`, (SELECT count(*) FROM `users`) AS nrusers, (SELECT `nume` FROM `users` WHERE `rank`>0 ORDER BY `id` DESC LIMIT 1) AS last FROM `useron`";
    $result = $this->conn->query($sql);

    // if the select returns at least one row
    if($result->num_rows>0) {
      while ($rand = $result->fetch_assoc()) {
        $useron = stripslashes($rand['nume']);
        if($useron!==NULL) $numeon[] = '<a href="'.$this->dir.$this->file_log.'?usr='.$useron.'" title="'.$useron.'">'.$useron.'</a>';

        // if $_SESSION['nume'] exists and the SID from table is different from $sid, increment $nr_logs
        if(isset($_SESSION['nume'])) {
          if(strtolower($useron)==strtolower($_SESSION['nume']) && $rand['sid']!==$this->sid) $this->nr_logs++;
        }

        // adds the total number of users, last registered user, and online users in $nrusers property
        $re['total'] = $rand['nrusers'];
        $re['last'] = '<a href="'.$this->dir.$this->file_log.'?usr='.$rand['last'].'" title="'.$rand['last'].'">'.$rand['last'].'</a>';
        $re['online'] = implode('<br/>', $numeon);
      }
    }
    else {
      // if 0 returned rows, perform another Select for total users (when 'useron' is empty, the "nrusers" also returns 0)
      $sql = "SELECT `nume` AS last, (SELECT count(*) FROM `users`) AS nrusers FROM `users` WHERE `rank`>0 ORDER BY `id` DESC LIMIT 1";
      $result = $this->conn->query($sql);
      if($result->num_rows>0) {
        $rand = $result->fetch_assoc();
        $re['total'] = $rand['nrusers'];
        $re['last'] = '<a href="'.$this->dir.$this->file_log.'?usr='.$rand['last'].'" title="'.$rand['last'].'">'.$rand['last'].'</a>';
      }
    }

    $this->users = $re;          // adds in the $users property the array stored in $re
  }

  // the method for LogOut
  private function logOut($rd=1) {
    // if there are cookies for name and password, sets to remove them
    if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
      setcookie("cookname", "", time()-60*60*24*100, "/");
      setcookie("cookpass", "", time()-60*60*24*100, "/");
    }

    // if $rd parameter different from 2, delete the user from the onlinwe-users
    if($rd!==2) {
      $sql = "DELETE FROM `useron` WHERE `nume`='".$_SESSION['nume']."'";
      $this->conn->query($sql);
    }

    // if exist sessions with: nume, parola, idusr, and datavisit, delete them, then perform redirect
    if(isset($_SESSION['nume'])) unset($_SESSION['nume']);
    if(isset($_SESSION['parola'])) unset($_SESSION['parola']);
    if(isset($_SESSION['idusr'])) unset($_SESSION['idusr']);
    if(isset($_SESSION['rank'])) unset($_SESSION['rank']);
    if(isset($_SESSION['datavisit'])) unset($_SESSION['datavisit']);

      // if $rd=1, auto-redirect to avoid resending data on refresh
    if($rd===1) echo '<meta http-equiv="Refresh" content="1;url=/"><script type="text/javascript">alert("Logged Out");</script>';
    else if($rd===2) echo '<meta http-equiv="Refresh" content="1;url=/"><script type="text/javascript">alert("Logged Out\nThere is another Login with this account.\nYou can re-login.");</script>';
    exit;
  }
}
?>