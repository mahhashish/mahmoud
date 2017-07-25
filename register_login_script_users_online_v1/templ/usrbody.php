<?php
// define variables with data to be added in user page code
$imgusr = (isset($ar_usrdat['imgusr']) && strlen($ar_usrdat['imgusr'])>1 && file_exists('../'.$ar_usrdat['imgusr'])) ? $ar_usrdat['imgusr'] : $imgup['dir'].'noimg.gif';
$datareg = isset($ar_usrdat['datareg']) ? $ar_usrdat['datareg'] : '';
$usrmail = isset($ar_usrdat['usrmail']) ? $ar_usrdat['usrmail'] : '';
$datvisit = isset($ar_usrdat['datvisit']) ? $ar_usrdat['datvisit'] : '';
// for the page of the logged user, get the last visit time from session becouse in table it is updated
if(isset($_SESSION['nume']) && strtolower($_SESSION['nume'])==strtolower($usr)) $datvisit = $_SESSION['datavisit'];
$visits = isset($ar_usrdat['visits']) ? $ar_usrdat['visits'] : '';
$usrnume = isset($ar_usrdat['usrnume']) ? $ar_usrdat['usrnume'] : '';
$usrpronoun = isset($ar_usrdat['usrpronoun']) ? $ar_usrdat['usrpronoun'] : '';
$country = isset($ar_usrdat['country']) ? $ar_usrdat['country'] : '';
$city = isset($ar_usrdat['city']) ? $ar_usrdat['city'] : '';
$adres = isset($ar_usrdat['adres']) ? $ar_usrdat['adres'] : '';
$bday = (isset($ar_usrdat['bday']) && $ar_usrdat['bday']!='00-00-0000') ? $ar_usrdat['bday'] : '';
$usrym = isset($ar_usrdat['usrym']) ? $ar_usrdat['usrym'] : '';
$usrmsn = isset($ar_usrdat['usrmsn']) ? $ar_usrdat['usrmsn'] : '';
$usrsite = isset($ar_usrdat['usrsite']) ? $ar_usrdat['usrsite'] : '';
$ocupation = isset($ar_usrdat['ocupation']) ? $ar_usrdat['ocupation'] : '';
$interes = isset($ar_usrdat['interes']) ? $ar_usrdat['interes'] : '';
$transmit = isset($ar_usrdat['transmit']) ? $ar_usrdat['transmit'] : '';

// start the variable with the html code for the user page
$usrhtml = '<section id="center">
<div id="usrdat">
 <img src="'. $imgusr. '" alt="Image '. $usr. '" id="imgusr" class="fl" />
 <div id="datusr">
  <div class="fl">
   Registered date: <span class="spndat">'. $datareg. '</span><br/>
   Last logged date: <span class="spndat">'. $datvisit. '</span><br/>
   Visits number: <span class="spndat">'. $visits. '</span><br/><br/>
   <h5>- Contact -</h5>
   Yahoo Messenger: <span class="spndat">'. $usrym. '</span><br/>
   MSN Messenger: <span class="spndat">'. $usrmsn. '</span><br/>
   Web Site: <span class="spndat">'. $usrsite.'</span>
  </div>
  <div class="fr">
   Name: <span class="spndat">'.$usrnume.'</span><br/>
   Pronoun: <span class="spndat">'.$usrpronoun.'</span><br/>
   Birthday: <span class="spndat">'. $bday.'</span>
   <h5>Location:</h5>
   <ul>
    <li>Country: <span class="spndat">'.$country.'</span></li>
    <li>City: <span class="spndat">'.$city.'</span></li>
    <li>Address: '. $adres. '</li>
   </ul>
  </div><br class="clr" />
 </div>';

// If its the page of the logged user ($_SESSION['nume'] exists), add image upload
if(isset($_SESSION['nume']) && strtolower($_SESSION['nume'])==strtolower($usr)) {
 $usrhtml .= '<form action="" id="usrupimg" enctype="multipart/form-data" target="sendimg" method="post">
  <input type="hidden" name="usr" value="'. $_SESSION['nume']. '" />
  <input type="hidden" name="ajax" value="1" />
  (<i>Maxim: '.$imgup['width'].'/'.$imgup['height'].' pixels, '.$imgup['maxsize'].' KB, '. strtoupper(implode(', ', $imgup['allowext'])).'</i>)<br/>
  <label for="usrimg">Add image:</label> <input type="file" id="usrimg" name="usrimg" />
  <input type="submit" name="submit" value="Upload" />
 </form><div id="ifrmup"></div>
 <button id="forupimg">Upload / Change image</button>';
}
$usrhtml .= '</div>
 <div id="userss">
Number of registered users: <b>'.$objLogare->users['total'].'</b><br/>
Newest User:'.$objLogare->users['last'].
 '<h5>Online users:</h5>
 <div id="useron">'.$objLogare->users['online'].'</div>
</div>';

// If its the page of the logged user ($_SESSION['nume'] exists), add UL for Tabs-effect
if(isset($_SESSION['nume']) && strtolower($_SESSION['nume'])==strtolower($usr)) {
$usrhtml .= '<ul id="ultabs">
 <li title="dateopt">About me</li>
 <li title="usrform1">Change E-mail /Parola</li>
 <li title="usrform2">Edit optional data</li>
</ul><div id="usreror"></div>';
}

// continue ading html with data in $usrhtml
$usrhtml .= '<div id="dateopt">
 <h3 class="usrh3">Occupation:</h3>
 <div class="usropt">'. nl2br($ocupation). '</div>
 <h3 class="usrh3">Interests / Hobbies:</h3>
 <div class="usropt">'. nl2br($interes). '</div>
 <h3 class="usrh3">Things I want to say:</h3>
 <div class="usropt">'. nl2br($transmit). '</div>
</div>';

// If its the page of the logged user, add the forms for editing users data
if(isset($_SESSION['nume']) && strtolower($_SESSION['nume'])==strtolower($usr)) {
  // this function define <option> tags for the Select birthday
  function setBdayOpt($nr, $nrlast, $check) {
    $re = '<option>--</option>';
    for($i=$nr; $i<$nrlast; $i++) {
      // adds the "selected" attribute
      if($i==$check) {
        $re .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        continue;
      }
      $re .= '<option value="'.$i.'">'.$i.'</option>';
    }
    return $re;
  }

  // gets the day, month, and yers set for birthday (to be selected in <option>), and the current year
  $ar_bday = strtotime($bday) ? getdate(strtotime($bday)) : array('mday'=>0, 'mon'=>0, 'year'=>0);
  $set_zi = $ar_bday['mday'];
  $set_luna = $ar_bday['mon'];
  $set_an = $ar_bday['year'];
  $acum_an = date('Y')+1;

  $usrhtml .= '<form action="" method="post" id="usrform1" onsubmit="return usrModf(this);">
 <input type="hidden" name="modf" value="'. $_SESSION['nume']. '" />
 <h4>Edit registration data</h4>
 <label for="pass">Current password:</label> <input type="password" size="18" maxlength="32" name="pass" id="pass" /><br/>
 <label for="passnew">New password:</label> <input type="password" size="18" maxlength="32" name="passnew" id="passnew" /><br/>
 <label for="email">E-mail:</label> <input type="text" size="18" maxlength="32" name="email" id="email" value="'.$usrmail.'" /><br/>
 <input type="submit" name="submit" value="Change" />
</form>

<form action="" method="post" id="usrform2">
<h4>Date optionale</h4>
 <label for="usrnume">Name:</label> <input type="text" size="18" maxlength="32" name="usrnume" id="usrnume" value="'.$usrnume.'" /><br/>
 <label for="usrpronoun">Pronoun:</label> <input type="text" size="18" maxlength="32" name="usrpronoun" id="usrpronoun" value="'.$usrpronoun.'" /><br/>
 <label for="usrcountry">Country:</label> <input type="text" size="18" maxlength="15" name="usrcountry" id="usrcountry" value="'.$country.'" /><br/>
 <label for="usrcity">City:</label> <input type="text" size="18" maxlength="25" name="usrcity" id="usrcity" value="'.$city.'" /><br/>
 <label for="usradres">Address:</label> <input type="text" size="28" maxlength="125" name="usradres" id="usradres" value="'.$adres.'" /><br/>
 Birthday:<br/>
<label for="usrbday">Day: <select id="usrbday" name="usrbday">'.setBdayOpt(1, 32, $set_zi).'</select></label>
<label for="usrbmonth">Month: <select id="usrbmonth" name="usrbmonth">'.setBdayOpt(1, 13, $set_luna).'</select></label>
<label for="usrbyear">Year: <select id="usrbyear" name="usrbyear">'.setBdayOpt(1911, $acum_an, $set_an).'</select></label>
 <fieldset><legend>Contact</legend>
  <label for="usrym">Yahoo Messenger:</label> <input type="text" size="18" maxlength="25" name="usrym" id="usrym" value="'.$usrym.'" /><br/>
  <label for="usrmsn">MSN Messenger:</label> <input type="text" size="18" maxlength="32" name="usrmsn" id="usrmsn" value="'.$usrmsn.'" /><br/>
  <label for="usrsite">Web Site:</label> <input type="text" size="18" maxlength="32" name="usrsite" id="usrsite" value="'.$usrsite.'" />
 </fieldset>
 <fieldset><legend>Diverse</legend>
  <label for="usrocupation">Ocupation:</label> (<span id="ocupchr">Maximum 500 characters</span>)<br/>
  <textarea rows="4" cols="30" name="usrocupation" id="usrocupation" onkeydown="checkNrChr(this, 500, \'ocupchr\');" onkeyup="checkNrChr(this, 500, \'ocupchr\');">'. $ocupation. '</textarea><br/>
  <label for="usrinteres">Interests / Hobbies:</label> (<span id="intereschr">Maximum 500 characters</span>)<br/>
  <textarea rows="4" cols="30" name="usrinteres" id="usrinteres" onkeydown="checkNrChr(this, 500, \'intereschr\');" onkeyup="checkNrChr(this, 500, \'intereschr\');">'. $interes. '</textarea><br/>
  <label for="usrtransmit">Things I want to say:</label> (<span id="transmitchr">Maximum 1000 characters</span>)<br/>
  <textarea rows="5" cols="48" name="usrtransmit" id="usrtransmit" onkeydown="checkNrChr(this, 1000, \'transmitchr\');" onkeyup="checkNrChr(this, 1000, \'transmitchr\');">'. $transmit. '</textarea><br/>
  (<i>You can use these HTML tags: &lt;b&gt;&lt;i&gt;&lt;u&gt;&lt;p&gt;&lt;ol&gt;&lt;ul&gt;&lt;li&gt;&lt;a&gt;&lt;blockquote&gt;</i>)
 </fieldset>
  <input type="submit" name="submit" value="Send" />
</form><script src="'. $objLogare->dir. 'usrloged.js" type="text/javascript"></script>';
}
$usrhtml .= '</section>';

echo $usrhtml;      // output the html code
?>