<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Download Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Yehuda Zadik
 */
 
 // -------------------------------------------------------------------------
 
 /**
 * Download large files 
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	$fullPath
 * @return	void
 */ 
 function download_large_files($fullPath)
 {
	// File Exists? 
	if( file_exists($fullPath) )
	{ 
		// Parse Info / Get Extension 
		$fsize = filesize($fullPath); 
		$path_parts = pathinfo($fullPath); 
		$ext = strtolower($path_parts["extension"]); 

		// Determine Content Type 
		switch ($ext) 
		{ 
			case "pdf": 
				$ctype = "application/pdf"; 
			break; 
		
			case "exe": 
				$ctype = "application/octet-stream"; 
				break; 
		
			case "zip": 
				$ctype = "application/zip"; 
				break; 
		
			case "doc": 
				$ctype = "application/msword"; 
				break; 
		
			case "xls": 
				$ctyp = "application/vnd.ms-excel"; 
				break; 
		
			case "ppt": 
				$ctype = "application/vnd.ms-powerpoint"; 
				break; 
		
			case "wmv": 
				$ctype = "video/x-ms-wmv"; 
				break; 
		
			case "gif": 
				$ctype = "image/gif"; 
				break; 
		
			case "png": 
				$ctype = "image/png"; 
				break; 
		
			case "jpeg": 		
			case "jpg": 
				$ctype = "image/jpg"; 
				break; 			
						
			default: 
				$ctype = "application/force-download"; 
		} 
				
		$file_handle = fopen($fullPath, "rb");
		
		header('Content-Description: File Transfer');
		header("Content-Type: " . $ctype);
		header('Content-Length: ' . $fsize);
		header('Content-Disposition: attachment; filename=' . basename($fullPath));
		
		while(!feof($file_handle)) 
		{
			$buffer = fread($file_handle, 1*(1024*1024));
			echo $buffer;
			ob_flush();
			flush();    //These two flush commands seem to have helped with performance
		}
		
		fclose($file_handle);
	} else
	{
		die('File Not Found');
	} 
 }
 
   
/* End of file my_download_helper.php */
/* Location: ./application/helpers/my_download_helper.php */