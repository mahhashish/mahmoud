<?php
include('admin.php');
// create the object instance of Logare class
$objLogare = new Logare($mysql);
$logare = $objLogare->logat;            // get data from the "logat" property

// sets a variable which determines whether or not the request is by Ajax via POST
$is_ajax = (isset($_POST['ajax'])) ? 1 : 0;

// if data via GET or POST with index 'submit', 'mp', 'rc' (Confirm / Recover), 'usr' (User data)
if(isset($_REQUEST['submit']) || isset($_GET['mp']) || isset($_REQUEST['rc']) || isset($_GET['usr'])) {
  // sets to not include login data to Registration or data Recovery request
  if(isset($_REQUEST['submit']) || isset($_REQUEST['rc'])) $logare = '';

  if($is_ajax===0) include('../templ/head.php');        // if not Ajax request, include head.php (from templ/)
  include('class.LogareReg.php');        // include the class for Register (used also for Recovery data)

  // if 'submit'=Register, create objet of LogareReg (for Register)
  // if 'rc' or 'mp' (for Recover-Confirma), uses LogareReCon class
  // if 'usr', create object of LogareUser class (for user page data)
  if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Register') {
    $objRe = new LogareReg($mysql, $rank);
  }
  else if(isset($_REQUEST['rc']) || isset($_GET['mp'])) {
    include('class.LogareReCon.php');
    $objRe = new LogareReCon($mysql, $rank);
  }
  else if(isset($_REQUEST['usr'])) {
    include('class.LogareUser.php');
    $objUsr = new LogareUser($mysql, $imgup);

    // if not Submit, calls the getUser() method, that returns an Array with user data
    if(!isset($_REQUEST['submit'])) $ar_usrdat = $objUsr->getUser($_REQUEST['usr']);
  }

  if(isset($_GET['usr']) && $is_ajax===0) include('../templ/usrbody.php');        // include usrbody.php, for user page
  if($is_ajax===0) include('../templ/footer.php');       // if not Ajax request, include footer.php (from templ/)
}

// if is Ajax request, with index ajax=log output the response
if(isset($_POST['ajax'])) if($_POST['ajax']=='log_form') echo $logare;
?>