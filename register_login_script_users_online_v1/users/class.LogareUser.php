<?php
// LogareUser class (that sets data for User's page) extended from LogareReg
class LogareUser extends LogareReg {
  private $usr;         // for username
  // constructor
  public function __construct($conn_datas, $imgup=array()) {
    // if $conn_datas is an array, and data via POST, or GET['usr'], sets 'conn_datas' property. Otherwise, error
    if(is_array($conn_datas) && (isset($_POST['submit']) || isset($_GET['usr']))) {
      // sets the properties
      $this->usr = $_REQUEST['usr'];
      $this->conn_datas = $conn_datas;
      $this->site = $_SERVER['SERVER_NAME'];
      $this->setConn();    // create the MySQL connection

      // if 'conn' is set
      if($this->conn!==false) {
        // if SESSIONs with user data, and POST['submit']: calls modfMailPass(), uploadImg(), addOptDat()
        if(isset($_SESSION['idusr']) && isset($_SESSION['nume']) && isset($_SESSION['parola']) && isset($_POST['submit'])) {
          $_POST = array_map("trim", $_POST);        // remove external whitespace
          if(isset($_POST['usradres']) && isset($_POST['usrbday'])) $this->result = $this->addOptDat($_POST);
          else if(isset($_FILES['usrimg'])) $this->result = $this->uploadImg($_FILES['usrimg'], $imgup);
          else if(isset($_POST['modf']) && $_POST['modf']==$_SESSION['nume'] && isset($_POST['pass'])) $this->result = $this->modfMailPass($_POST);
          else $this->eror = 'Error: Incomplete fields from form';
        }
      }
      else $this->eror = 'Error: Cannot connect to database';
    }
    else $this->eror = 'Error: Date incorrect data for object instance LogareUser';

    if($this->eror!==false) $this->result = $this->eror;        // if there is a error, adds it in the returned data
    echo $this->result;        // return / output the data stored in $result
  }

  // this method gets the user data from database
  public function getUser($user) {
    $nume = $this->conn->real_escape_string($this->usr);        // filter for SQL

    $sql = "SELECT `users`.`email`, `users`.`visits`, `usersdat`.`nume`, `usersdat`.`pronoun`, `usersdat`.`country`, `usersdat`.`city`, `usersdat`.`adres`, `usersdat`.`ym`, `usersdat`.`msn`, `usersdat`.`site`, `usersdat`.`img`, `usersdat`.`ocupation`, `usersdat`.`interes`, `usersdat`.`transmit`, DATE_FORMAT(`users`.`datereg`, '%M %D, %Y, %H:%i') AS datareg, DATE_FORMAT(`users`.`datevisit`, '%M %D, %Y, %H:%i') AS datvisit, DATE_FORMAT(`usersdat`.`bday`, '%M %D, %Y') AS bday FROM `users` LEFT JOIN `usersdat` ON `usersdat`.`id`=(SELECT `id` FROM `users` WHERE `nume`='$nume' LIMIT 1) WHERE `users`.`nume`='$nume' LIMIT 1";
    $result = $this->conn->query($sql);

    if($result->num_rows>0) {
      while ($rand = $result->fetch_assoc()) {
        $userdat['usrmail'] = $rand['email'];
        $userdat['visits'] = $rand['visits'];
        $userdat['usrnume'] = $rand['nume'];
        $userdat['usrpronoun'] = $rand['pronoun'];
        $userdat['country'] = $rand['country'];
        $userdat['city'] = $rand['city'];
        $userdat['adres'] = $rand['adres'];
        $userdat['usrym'] = $rand['ym'];
        $userdat['usrmsn'] = $rand['msn'];
        $userdat['usrsite'] = $rand['site'];
        $userdat['imgusr'] = $rand['img'];
        $userdat['ocupation'] = $rand['ocupation'];
        $userdat['interes'] = $rand['interes'];
        $userdat['transmit'] = $rand['transmit'];
        $userdat['datareg'] = $rand['datareg'];
        $userdat['datvisit'] = $rand['datvisit'];
        $userdat['bday'] = $rand['bday'];
      }
      $userdat = array_map("stripslashes", $userdat);        // remove slashes added to the filtration
    }
    else $userdat = array();
    $this->conn->close();

    return $userdat;
  }

  // this method Update the User data
  private function modfMailPass($ar_post) {
    $ar_post = array_map("strip_tags", $ar_post);       // remove tags

    // if there are data from 'email', 'pass', 'passnew'
    if(isset($ar_post['email']) && isset($ar_post['pass']) && isset($ar_post['passnew'])) {
      // check password length and email address
      if(strlen($ar_post['passnew'])<7 || strlen($ar_post['passnew'])>18 || !$this->checkStr($ar_post['passnew'])) {
        $this->eror = 'Error: The password must contains between 7 and 18 characters<br/>Only letters, numbers, "-" and "_"';
      }
      if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $ar_post['email'])) {
        $this->eror = 'Error: Incorrect email adress';
      }

      // if no errors, continue to filter and update data
      if($this->eror===false) {
        // filter with real_escape_string(), and encript the password
        $pass = $this->conn->real_escape_string($ar_post['passnew']);
        $nume = $this->conn->real_escape_string($_SESSION['nume']);
        $parola = md5($pass);

        // check if the current password is the same as in $_SESSION['parola']
        if($_SESSION['parola']==md5($ar_post['pass'])) {
          // Select to check if there is already the updated e-mail addres to another user
          $sql = "SELECT `email` FROM `users` WHERE `nume`!='$nume' AND `email`='". $ar_post['email']. "'";
          $result = $this->conn->query($sql);

          // if the result contains at least one row, means that email is already used by other user
          if($result->num_rows>0) $re = 'Error: The e-mail '.$ar_post['email'].' is already used by other user';
          else {
            // Otherwise, perform the update and sets the new password in session
            $sql = "UPDATE `users` SET `parola`='$parola', `pass`='$pass', `email`='". $ar_post['email']. "' WHERE `nume`='$nume' AND `parola`='". $_SESSION['parola']."'";

            if (!$this->conn->query($sql)) $re = 'Error Update: '. $this->conn->error;       // message if error on update
            else {
              $_SESSION['parola'] = $parola;
              $re = 'Your data was successfully updated.';
              // sends an message to user's e-mail with the new data
              $subiect = 'Registration data updated';
              $mesaj = "            Hy,<br /><br />
              Your new registration data on the website ". $this->site. " :<br /><br />
        Name = ". $_SESSION['nume']. " <br />
        Password = $pass <br />
        E-mail = ".$ar_post['email']." <br /><br /><br /><br />
  <i>Respectfully,<br /> Admin</i><br /><center>";
              if($this->sendMail($ar_post['email'], $this->from, $this->site, $subiect, $mesaj)) $re .= "\n An email with your new data is sent to: ".$ar_post['email'];
            }
          }
        }
        else $re = 'Error: Incorrect current password';
      }
      else $re = $this->eror;
    }
    else $re = 'Error: Accessing modfMP with incorrect data';
    $this->conn->close();

    return $re;
  }

  // add user optional data (address, birthday ...) in "usersdat" table
  private function addOptDat($ar_post) {
    $usrtransmit = strip_tags($ar_post['usrtransmit'], '<b><i><u><p><ol><ul><li><a><blockquote>');     // store this item to keep some tas
    $ar_post = array_map("strip_tags", $ar_post);       // remove tags

    // filter with real_escape_string()
    $nume = $this->conn->real_escape_string($ar_post['usrnume']);
    $pronoun = $this->conn->real_escape_string($ar_post['usrpronoun']);
    $country = $this->conn->real_escape_string($ar_post['usrcountry']);
    $city = $this->conn->real_escape_string($ar_post['usrcity']);
    $adres = $this->conn->real_escape_string($ar_post['usradres']);
    $ym = $this->conn->real_escape_string($ar_post['usrym']);
    $msn = $this->conn->real_escape_string($ar_post['usrmsn']);
    $site = $this->conn->real_escape_string($ar_post['usrsite']);
    $ocupation = $this->conn->real_escape_string($ar_post['usrocupation']);
    $interes = $this->conn->real_escape_string($ar_post['usrinteres']);
    $transmit = $this->conn->real_escape_string($usrtransmit);
    $id = $_SESSION['idusr'];
    $bday = intval($ar_post['usrbyear']).'-'.intval($ar_post['usrbmonth']).'-'.intval($ar_post['usrbday']);    // an-luna-zi

    // adds data in "usersdat", or Update if there is already a row for this user
    $sql = "INSERT INTO `usersdat` (`id`, `nume`, `pronoun`, `country`, `city`, `adres`, `bday`, `ym`, `msn`, `site`, `ocupation`, `interes`, `transmit`) VALUES ($id, '$nume', '$pronoun', '$country', '$city', '$adres', '$bday', '$ym', '$msn', '$site', '$ocupation', '$interes', '$transmit') ON DUPLICATE KEY UPDATE `nume`='$nume', `pronoun`='$pronoun', `country`='$country', `city`='$city', `adres`='$adres', `bday`='$bday', `ym`='$ym', `msn`='$msn', `site`='$site', `ocupation`='$ocupation', `interes`='$interes', `transmit`='$transmit'";
    if($this->conn->query($sql)) return 'Your data were successfully registered';
    else $this->eror = 'Error: Your optional data could not be saved: '. $this->conn->error;
  }

  // this method Upload the image, save in database its name and path, and return it
  private function uploadImg($ar_fileimg, $imgup) {
    // gets file extension
	  $splitimg = explode('.', strtolower($ar_fileimg['name']));
    $ext = end($splitimg);
    $fileimg = strtolower($imgup['dir']. $_SESSION['nume']. '.'.$ext);          // define image name and path for upload

    list($width, $height) = getimagesize($ar_fileimg['tmp_name']);     // gets image width and height

    // If the file has the extension allowed
    if(in_array($ext, $imgup['allowext'])) {
	    // Check if the file has allowed size
	    if ($ar_fileimg['size']<=$imgup['maxsize']*1000) {
        // check image width and height
        if ($width<=$imgup['width'] && $height<=$imgup['height']) {
          // if the upload is performed
          if(move_uploaded_file($ar_fileimg['tmp_name'], '../'.$fileimg)) {
            // add file path in "usersdat", or update if there is already a record
            $sql = "INSERT INTO `usersdat` (`id`, `img`) VALUES (".$_SESSION['idusr'].", '$fileimg') ON DUPLICATE KEY UPDATE `img`='$fileimg'";
            if($this->conn->query($sql)) $re = $fileimg;
            else $re = 'Error: The image path could not be added: '. $this->conn->error;
          }
          else $re = 'Error: on image Upload';
        }
        else $re = 'Error: image width and height must be maximum '. $imgup['width'].'x'.$imgup['height'];
	    }
	    else $re = 'Error: The file '. $ar_fileimg['name']. ' exceeds the allowed size';
    }
	  else $re = 'Error: The file '. $ar_fileimg['name']. ' has not an allowed extension type';

    // returns the result in a call of a JavaScript function
    return '<body onload="parent.uplImg(\''.$re.'\')">'.$re.'</body>';
  }
}
?>