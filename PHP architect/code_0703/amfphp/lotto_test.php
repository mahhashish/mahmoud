<?php
/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

 Project Name : mySecondRemoting
 Filename :		lotto_test.php
 Written by :	Seth Wilson, P.Eng
 Date :			May 2003

 Description:	This lotto_test script shows how one can use the code within the PHP object
				originally intended for Flash Remoting to create a non-Flash web page
 				It grabs lotto numbers from an information website and displays them in HTML
				

 Revisions:
   1.
   2.
   3.

 Future Upgrades
   1.
   2.
   .


 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */

// include the class
require_once("services/lotto_class.php");

// create the new object
$test = new lotto_class;

// get the date
$lottoDate =$test->getLottoDate();

// get the lotto numbers
$numbers = array();
$numbers = $test->getLottoNumbers();


// Display the output
echo ("Winning Numbers for Date  ".$lottoDate."<br /> \n");

for ($i=0;$i<7;$i++) {
	if ($i < 6) {
		echo ("Number  ".$numbers[$i]."<br /> \n");
	}
	else {
		echo ("Bonus  ".$numbers[$i]."<br /> \n");
	}
}
?>