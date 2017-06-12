<?php
$id             = "set";
//
$result         = $conn->query("SELECT * FROM $t_settings");
$row            = $result->fetch(PDO::FETCH_ASSOC);
//
$failed_logins  = $row['failed_logins'];
$per_page       = $row['per_page'];
$auto_lock      = $row['auto_lock'];
$display_count  = $row['display_count'];
$new_window     = $row['new_window'];
$client_banners = $row['client_banners'];
// check if the form has been submitted. If it has, process the form and save it to the database
if (!empty($_POST['settings'])) {
    // confirm that the 'id' value is a valid integer before getting the form data
    if ($id == "set") {
        // get form data, making sure it is safe
        $email          = $_POST['email'];
        $per_page       = $_POST['per_page'];
        $auto_lock      = $_POST['auto_lock'];
        $failed_logins  = $_POST['failed_logins'];
        $display_count  = $_POST['display_count'];
        $new_window     = $_POST['new_window'];
        $client_banners = $_POST['client_banners'];
        $month1         = $_POST['month1'];
        $month3         = $_POST['month3'];
        $month6         = $_POST['month6'];
        $paypal_email   = $_POST['paypal_email'];
        $item_name      = $_POST['item_name'];
        // save the data to the database
        $conn->query("UPDATE $t_settings SET email='$email', per_page='$per_page', display_count='$display_count', new_window='$new_window', auto_lock='$auto_lock',failed_logins='$failed_logins', client_banners='$client_banners'");
        $conn->query("UPDATE $t_paypal SET 1month='$month1', 3month='$month3', 6month='$month6', paypal_email='$paypal_email', item_name='$item_name'");
        // once saved, redirect back to the index page
       //header("Location: index.php");
    } else {
        // if the 'id' isn't valid, display an error
        echo 'Error invalid ID!';
    }
} else {
    // if the form hasn't been submitted, get the data from the db and display the form
    // get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)
    if ($id == "set") {
        // query db
        //
        $result = $conn->query("SELECT * FROM $t_settings");
        $row    = $result->fetch(PDO::FETCH_ASSOC);
        // check that the 'id' matches up with a row in the database
        if ($row) {
            // get data from db
            $email          = $row['email'];
            $per_page       = $row['per_page'];
            $auto_lock      = $row['auto_lock'];
            $failed_logins  = $row['failed_logins'];
            $display_count  = $row['display_count'];
            $new_window     = $row['new_window'];
            $client_banners = $row['client_banners'];
            // show form
        } else {
            // else if no match, display error 
            echo "No results found!";
        }
    } else {
        // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
        echo 'Error invalid ID or No ID Found!';
    }
}
?>
<form id="setForm" action="" method="post"> 
<p>
	<input type="hidden" name="id" value="set" />
</p>
	<div class="close"><a href="#" class="closeimg">X</a></div>

<fieldset>
        <legend>Settings</legend>
<p>Control Panel</p><hr/>

<label for="email">Email Address <span>(To send alerts too)</span></label> 
	<input type='text' name='email' id='email' size='20' value='<?php echo $email; ?>' />

<label for="per_page">Banners <span>(To show per page?)</span></label> 
<select name="per_page" id="per_page">
		<option value='5'<?php if ($per_page == '5') echo ' selected="selected">5'; else echo '>5'; ?></option>
		<option value='10'<?php if ($per_page == '10') echo ' selected="selected">10'; else echo '>10'; ?></option>
		<option value='15'<?php if ($per_page == '15') echo ' selected="selected">15'; else echo '>15'; ?></option>
		<option value='20'<?php if ($per_page == '20') echo ' selected="selected">20'; else echo '>20'; ?></option>
		<option value='25'<?php if ($per_page == '25') echo ' selected="selected">25'; else echo '>25'; ?></option>
		<option value='30'<?php if ($per_page == '30') echo ' selected="selected">30'; else echo '>30'; ?></option>
		<option value='40'<?php if ($per_page == '40') echo ' selected="selected">40'; else echo '>40'; ?></option>
		<option value='50'<?php if ($per_page == '50') echo ' selected="selected">50'; else echo '>50'; ?></option>
</select>

<label for="auto_lock">Auto-Lock <span>(After x attempts?)</span></label>        
<select name="auto_lock" id="auto_lock">
		<option value='0'<?php if ($auto_lock == '0') echo ' selected="selected">0'; else echo '>0'; ?></option>
		<option value='1'<?php if ($auto_lock == '1') echo ' selected="selected">1'; else echo '>1'; ?></option>
		<option value='2'<?php if ($auto_lock == '2') echo ' selected="selected">2'; else echo '>2'; ?></option>
		<option value='3'<?php if ($auto_lock == '3') echo ' selected="selected">3'; else echo '>3'; ?></option>
		<option value='4'<?php if ($auto_lock == '4') echo ' selected="selected">4'; else echo '>4'; ?></option>
		<option value='5'<?php if ($auto_lock == '5') echo ' selected="selected">5'; else echo '>5'; ?></option>
		<option value='6'<?php if ($auto_lock == '6') echo ' selected="selected">6'; else echo '>6'; ?></option>
</select>

<label>Alert me <span>(of failed login attempts?)</span></label> 
		<input type="radio" name="failed_logins" value="1" class="narrow" <?php if ($failed_logins =='1') echo 'checked="checked"'; ?> /> <em>Yes</em>
		<input type="radio" name="failed_logins" value="0" class="narrow" <?php if ($failed_logins =='0') echo 'checked="checked"'; ?> /> <em>No</em>
<br /><br />

<hr />
<p>The Banner Bar</p><hr/>
<p>
<label for="display_count">Banners to display on bar</label> 
<select name="display_count" id="display_count">
		<option value='3'<?php if ($display_count == '3') echo ' selected="selected">3'; else echo '>3'; ?></option>
		<option value='4'<?php if ($display_count == '4') echo ' selected="selected">4'; else echo '>4'; ?></option>
		<option value='5'<?php if ($display_count == '5') echo ' selected="selected">5'; else echo '>5'; ?></option>
		<option value='6'<?php if ($display_count == '6') echo ' selected="selected">6'; else echo '>6'; ?></option>
		<option value='7'<?php if ($display_count == '7') echo ' selected="selected">7'; else echo '>7'; ?></option>
		<option value='8'<?php if ($display_count == '8') echo ' selected="selected">8'; else echo '>8'; ?></option>
		<option value='9'<?php if ($display_count == '9') echo ' selected="selected">9'; else echo '>9'; ?></option>
		<option value='10'<?php if ($display_count == '10') echo ' selected="selected">10'; else echo '>10'; ?></option>
</select>

<label for="client_banners">Client Banners to display on bar</label> 
<select name="client_banners" id="client_banners">
		<option value='0'<?php if ($client_banners == '0') echo ' selected="selected">0'; else echo '>0'; ?></option>
		<option value='1'<?php if ($client_banners == '1') echo ' selected="selected">1'; else echo '>1'; ?></option>
		<option value='2'<?php if ($client_banners == '2') echo ' selected="selected">2'; else echo '>2'; ?></option>
		<option value='3'<?php if ($client_banners == '3') echo ' selected="selected">3'; else echo '>3'; ?></option>
		<option value='4'<?php if ($client_banners == '4') echo ' selected="selected">4'; else echo '>4'; ?></option>
		<option value='5'<?php if ($client_banners == '5') echo ' selected="selected">5'; else echo '>5'; ?></option>
		<option value='6'<?php if ($client_banners == '6') echo ' selected="selected">6'; else echo '>6'; ?></option>
</select>


<label>Open banners in new window <span>Front end</span></label> 
		<input type="radio" name="new_window" value="1" class="narrow" <?php if ($new_window =='1') echo 'checked="checked"'; ?> /> <em>Yes</em>
		<input type="radio" name="new_window" value="0" class="narrow" <?php if ($new_window =='0') echo 'checked="checked"'; ?> /> <em>No</em>

</p>

<hr />
<p>Paypal (&pound; Pound Sterling)</p><hr/>

<?php
$result       = $conn->query("SELECT * FROM $t_paypal");
$row          = $result->fetch(PDO::FETCH_ASSOC);
$month1       = $row['1month'];
$month3       = $row['3month'];
$month6       = $row['6month'];
$paypal_email = $row['paypal_email'];
$item_name    = $row['item_name'];
?>
<label for="month1">1 Month<span>(Ad Duration)</span></label> 
	<input type='text' name='month1' id='month1' size='20' value='<?php echo $month1; ?>' />

<label for="month3">3 Month<span>(Ad Duration)</span></label> 
	<input type='text' name='month3' id='month3' size='20' value='<?php echo $month3; ?>' />

<label for="month6">6 Month<span>(Ad Duration)</span></label> 
	<input type='text' name='month6' id='month6' size='20' value='<?php echo $month6; ?>' />

<label for="paypal_email">Paypal Email<span>(Money is sent Too)</span></label> 
	<input type='text' name='paypal_email' id='paypal_email' size='20' value='<?php echo $paypal_email; ?>' />

<label for="item_name">Item Name<span>(Shown on Paypal Checkout)</span></label> 
	<input type='text' name='item_name' id='item_name' size='20' value='<?php echo $item_name; ?>' />
</fieldset>

	<div class="submitB">
		<input type="submit" name="settings" id="submit" value="Submit" class="button style1" onclick="return confirm('Are you sure you want to save these Settings?');" />
	</div>
</form>
</div>