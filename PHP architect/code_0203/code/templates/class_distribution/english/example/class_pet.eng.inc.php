<?
/**
* P.E.T.: Processor Engine for Templates
*
* This class is published under the GNU license. Verbatim copying and dis-
* tribution is permitted in any medium, as long as this notice is preserved
* and the original author is mentioned. The author is always curious in which
* projects this class is used, so sending a short email would be great.
*
* @version 1.4
* @author Andreas Demmer <andreas@demmer-online.de>
* @link http://php-pet.sourceforge.net
* @copyright GNU-GPL
* @package P.E.T. english
*/

class pet
{
	//contains the content to be parsed into $this->file
	var $content;
	
	//contains the template after a file was specified by read_file($filename)
	var $file;
	
	//set TRUE if substitute symbols without equivalent content should
	//not be replaced with blank but marked for debugging by default.
	var $debug = FALSE;
		
	/**
	 * @access public
	 * @return void
	 * @param content string content, can also be an array
	 * @param name string
	 * @desc assigns content to a content tag
	 */	
	function add_content($content, $name)
	{
		if($name)
		{
			$this->content[$name] = $content;
		}
		else
		{
			$this->content = array_merge($this->content, $content);
		}
	}	
	
	/**
	 * @access public
	 * @return void
	 * @param content string content, can also be an array
	 * @param name string
	 * @desc alias for add_content
	 */	
	function assign($content, $name)
	{
		$this->add_content($content, $name);
	}

	/**
	 * @access public
	 * @return void
	 * @param switch boolean TRUE oder FALSE for on or off
	 * @desc turns debugging on or of
	 */	
	function debugging($switch)
	{
		$this->debug = $switch;
	}
	
	/**
	 * @access public
	 * @return void
	 * @param filename string
	 * @desc writes the tempalte to a new file at $filename
	 */	
	function dump($filename)
	{
		$fp = fopen($filename, 'wb');
		fwrite($fp, $this->file);
		fclose($fp);
		
		return file_exists($filename);
	}
	
	/**
	 * @access public
	 * @return string
	 * @desc returns the template
	 */
	function get()
	{
		return $this->file;
	}
	
	/** 
	 * @access private
	 * @return array
	 * @desc returns the content
	 */
	function get_content()
	{
		return $this->content;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param string string String, in which a loop could hide
	 * @desc returns first loop in $string
	 */
	function get_loop($string)
	{
		$subloop = 1;
		
		//make function case-insensitive
		$lowercase = strtolower($string);
		
		$begin = strpos($lowercase, '<!-- begin loop {');
		
		if(!$begin)
		{
			$loop = FALSE;
		}
		else
		{
			$next_begin = $begin;
			$next_end   = $begin;
			
			while($subloop > 0)
			{
				$subloop--;
				
				$next_begin = strpos($lowercase, '<!-- begin loop {', $next_begin + 1);
				$next_end   = strpos($lowercase, '<!-- end loop -->', $next_end + 1);
				
				
				//loop(s) within the loop, ignore the next 'end loop' tag
				if($next_begin && ($next_begin < $next_end))
				{
					$subloop++;
				}
			}
			
			$loop = substr($string, $begin , $next_end + 17 - $begin);
		}
		
		return $loop;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param string string String, in which a loop could hide
	 * @desc liefert name of the first loop in $string
	 */
	function get_loopname($string)
	{
		//make function case-insensitive
		$lowercase = strtolower($string);
		
		$begin     = strpos($lowercase, '<!-- begin loop {');
		$end       = strpos($lowercase, '} -->', $begin);
		$loopname  = substr($string, $begin + 17, $end - $begin - 17);
		
		return $loopname;
	}
	
	/** 
	 * @access public
	 * @return void
	 * @desc sends the template to the browser for displaying
	 */
	function output()
	{
		echo $this->file;
	}
	
	/**
	 * @access public
	 * @return boolean
	 * @desc processes the template: inserts content and SSIs
	 */
	function parse()
	{
		if(!$this->file)
		{
			$verification = FALSE;
		}
		else
		{
			//replaces server side include tags with the specified files
			while(eregi('<!--#include virtual="', $this->file))
			{
				$this->process_ssis();
			}
			
			//replaces all loop tags with content
			if(eregi('<!-- begin loop {', $this->file))
			{
				$this->file = $this->process_loop($this->file, $this->content);
			}
			
			//parse template tags not within loops
			$this->file = $this->replace_template_tags($this->content, $this->file);
			
			$verification = TRUE;
		}
		
		return $verification;
	}
	
	/**
	 * @access public
	 * @return void
	 * @desc class constructor
	 */
	function pet()
	{
		$this->content     = array();
		$this->file        = FALSE;
		$this->parsed_loop = FALSE;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param string string String, in which loops could hide
	 * @param content array associative content-array
	 * @desc processes loops
	 */
	function process_loop($string, $content)
	{
		while(eregi('<!-- begin loop {', $string))
		{
			$parsed_loop = '';
			
			$loop          = $this->get_loop($string);
			$loopname      = $this->get_loopname($loop);
			$loop_content  = $content[$loopname];
			
			if(is_array($loop_content))
			{
				foreach($loop_content as $lap_content)
				{
					$loopcopy     = $this->strip_looptag($loopname, $loop);
					
					//recursion to find loops within loops
					$loopcopy     = $this->process_loop($loopcopy, $lap_content);
					
					$parsed_loop .= $this->replace_template_tags($lap_content, $loopcopy);
				}
			}
			
			//replace loop with content
			$string = str_replace($loop, $parsed_loop, $string);
		}
		
		return $string;
	}
	
	/**
	 * @access private
	 * @return void
	 * @desc processes Server Side Includes
	 */
	function process_ssis()
	{
		//make function case-insensitive
		$lowercase = strtolower($this->file);
		
		$begin = strpos($lowercase, '<!--#include virtual="');
		$end   = strpos($lowercase, '" -->', $begin);
		$file  = substr($this->file, $begin + 22, $end - $begin - 22);
		$tag   = '<!--#include virtual="'.$file.'" -->';
		
		if(file_exists($file))
		{
			$fp      = fopen($file, "rb");
			$content = fread($fp, filesize($file));
			fclose($fp);
		}
		else
		{
			$content = '[ERROR: The file "'.$file.'" could not be included!]';
		}
		
		$this->file = eregi_replace($tag, $content, $this->file);
	}
	
	/**
	 * @access public
	 * @return boolean
	 * @param filename string filename incl. relative path
	 * @desc reads the given file as template
	 */
	function read_file($filename)
	{
		if(file_exists($filename))
		{
			$fp = fopen($filename, 'rb');
			$this->file = fread($fp, filesize($filename));
			fclose($fp);
			
			$verification = TRUE;
		}
		else
		{
			$verfication = FALSE;
		}
		
		return $verification;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param content array associative array
	 * @param string string string, in which content tags could hide
	 * @desc replaces all content tags in $string with content
	 */
	function replace_template_tags($content, $string)
	{
		while(strpos($string, '<!-- {'))
		{
			$begin   = strpos($string, '<!-- {') + 6;
			$end     = strpos($string, ' -->', $begin) - 1;
			$tagname = substr($string, $begin, $end - $begin);
			$tag     = '<!-- {'.$tagname.'} -->';
			
			//if debugging is switched on, content tags with
			//no content will be marked for easy bugtracking
			if($this->debug && !$content[$tagname])
			{
				$content[$tagname] = '{'.$tagname.'}';
			}
			
			
			if(is_array($content))
			{
				$string = str_replace($tag, $content[$tagname], $string);
			}
			{
				$string = str_replace($tag, $content, $string);
			}
		}
		
		return $string;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param loopname string name of the loop whose tags to be removed
	 * @param string string string in which the loop could hide
	 * @desc renoves the loop tags of the loop $loop in $string
	 */
	function strip_looptag($loopname, $string)
	{
		$string = eregi_replace('<!-- begin loop {'.$loopname.'} -->', '', $string);
		$string = substr($string, 0, strlen($string) - 17);
		
		return $string;
	}
}
?>