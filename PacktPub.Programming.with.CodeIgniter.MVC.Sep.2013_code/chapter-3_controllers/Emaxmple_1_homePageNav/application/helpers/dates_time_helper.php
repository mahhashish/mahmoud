<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter dates and time Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Date & Time Manipulators
 * @author		Eli Orr 
 * @twitter		@eliorr
 * @Email		eliorr1961@gmail.com
 
 
 Helpers: 
 ********
 getAgeAccurate ( $db_bdate, $percision  )
 isFuture 		( $db_bdate  )
 now_date_time  ( );  
 full_date_info ( $timestamp ) // with weekday 
 HebrewDateFor  ( $date, $mode )
 HebrewWeekDay  ( $db_date )
 ui_date 		( $db_date )
 ui_date_time 	( $db_date )
    
*/
 

if (!function_exists('getAgeAccurate')) {
  
  function getAgeAccurate ( $db_bdate, $percision = 2 ) {
	// 
	// $db_bdate  = 2007-08-11 08:01:10 
	// $percision = 3 
	// returns  e.g :   5.127 (Years Unit)
	
	if (substr($db_bdate, 4,1) != '-' ) return 'ERR'; 
	
	$t1_sec = strtotime ( $now = gmdate("Y-m-d", time()));
	$t2_sec = strtotime ( $db_bdate );

	$age_days = ( $t1_sec - $t2_sec )/ ( 60 * 60 * 24);  // seconds in a day
	$years_past = number_format (  (((float)  $age_days ) / (365.00)),  $percision );   
	
    // return formatted string : 11.27 etc.. 
	return $years_past; 
	//return $age_days.' - '.$this->ui_date (gmdate("Y-m-d", time())).' - '.$this->ui_date ( $db_bdate ); 
    }
	
}

if (!function_exists('HebrewDateFor')) {

	 function HebrewDateFor  ($date, $mode ){
     //  
     //  2012-09-28  mode: day 		==> י"א תשרי ה'תשע"ג
	 //  2012-09-28  mode: night  	==> י"ב תשרי ה'תשע"ג 
     //	 

			if ($mode == 'day')  $add_days = 1;  // if user ressed on daty in become night in UI so we make night!! 
            else                 $add_days = 0;
			
			list    ($day,     $month,  $year) = split('[/.-]', $date); 
			$date = $month.'/'.$day.'/'.$year; 

			$time = strtotime( $date );
			$time = $time + (int) $add_days*(24*3600); // cal. next days if $add_days isset and !=0 !!!
			
			@$gregorianMonth = date(n, $time);
			@$gregorianDay = date(j, $time);
			@$gregorianYear = date(Y, $time);
			
			$jdDate = gregoriantojd($gregorianMonth,$gregorianDay,$gregorianYear);
			
			$j_format = mb_convert_encoding( jdtojewish( $jdDate, true , CAL_JEWISH_ADD_GERESHAYIM  + CAL_JEWISH_ADD_ALAFIM_GERESH), "UTF-8", "ISO-8859-8");
			return  $j_format;			
	}	
}

if (!function_exists('ui_date')) { 
	
	function ui_date ( $db_date )
    {
	  //1992-02-12 18:22 == > 12/02/1992 
      //01234567890123   	  
	  return   substr ($db_date, 8,2).'/'.substr ($db_date, 5,2).'/'.substr ($db_date, 0,4);   
    }
	
}

if (!function_exists('ui_date_time')) { 
	
	function ui_date_time ( $db_date )
    {
	  //1992-02-12 18:22 == > 12/02/1992 18:22 
      //01234567890123   	  
	  return   substr ($db_date, 8,2).'/'.substr ($db_date, 5,2).'/'.substr ($db_date, 0,4).' '.substr ($db_date, 11,5);   
    }
}

if (!function_exists('full_date_info')) {  
function full_date_info ($timestamp = null) {
// full date and time info including the weekday 

    if (is_null($timestamp)) { $timestamp = time(); }

    $dateParts = array(
        'mday'    => 'j',
        'wday'    => 'w',
        'yday'    => 'z',
        'mon'     => 'n',
        'year'    => 'Y',
        'hours'   => 'G',
        'minutes' => 'i',
        'seconds' => 's',
        'weekday' => 'l',
        'month'   => 'F',
        0         => 'U'
    );

    while (list($part, $format) = each($dateParts)) {
        $GMdateParts[$part] = @gmdate($format, $timestamp);
    }
    return $GMdateParts;
  }
}

if (!function_exists('HebrewWeekDay')) {  
function HebrewWeekDay ( ) // date in YYYY-MM-DD HH:MM  DB FMT!  
 {  
  // $db_date : 2012-09-28 (also can be weith/without time part...) 
  // => שישי
  $full_date = full_date_info (); 
  
	  $heb_day = Array ( 
	      'Sunday' => 'ריאשון', 
		  'Monday' => 'שני', 
		  'Tuesday' => 'שלישי', 
		  'Wednesday' => 'רביעי', 
		  'Thursday' => 'חמישי', 
		  'Friday' => 'שישי', 
		  'Saturday' => 'שבת'
	    );

	 //1992-02-12

	 $week_day = $full_date['weekday']; 
	 
    //echo "$year-$month-$day-$week_day  <BR/>"; 	 
	return $heb_day [ $week_day ]; 	
 } 
}

if (!function_exists('isFuture')) {
  
  function isFuture ( $db_bdate  ) {
	// 
	// $db_bdate  = 2007-08-11 08:01:10 
		
	if (substr($db_bdate, 4,1) != '-' ) 
	exit ( " helper dates_time_helper - isFuture invalidate date format!"); 
	
	$t1_sec = strtotime ( $now = gmdate("Y-m-d", time()));
	$t2_sec = strtotime ( $db_bdate );

	return (($t2_sec - $t1_sec) > 0 ); 
	}
	
	
	
}
if (!function_exists('now_date_time')) {
  
    function now_date_time  ( ) {
	// 
	// $db_bdate  = 2007-08-11 08:01:10 
		
	return  gmdate("Y-m-d H:i:s", time());  // UTC
	
	}
	
}

 

	
	