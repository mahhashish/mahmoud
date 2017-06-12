<?php 
		//formate the date 
		function formatDate($date){
			return date('F j, Y, g:i a',strtotime($date));
		}
		
		//make the text short
		function shortenText($text, $chars = 450){
			$text = $text." ";
			$text = substr($text, 0, $chars);
			$text = $text."...";
			return $text;
		}
?>