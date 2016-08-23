<?
/**
* P.E.T.: Processor Engine for Templates
*
* Diese Klasse ist unter der Öffentlichen GNU Lizenz (GPL) veröffentlicht.
* Weiterveröffentlichungen (auch in veränderter Form) sind für jedes Medium
* ausdrücklich erlaubt, sofern diese Copyright-Klauseln übernommen werden.
* Der Autor ist neugierig, in welchen Projekten seine Klasse zum Einsatz kommt,
* und freut sich über jede Email, welche ihn darauf hinweist.
*
* @version 1.4
* @author Andreas Demmer <andreas@demmer-online.de>
* @link http://php-pet.sourceforge.net
* @copyright GNU-GPL
* @package P.E.T. deutsch
*/

class pet
{
	//enthält den Content für $this->file
	var $content;
	
	//enthält das Template
	var $file;
	
	//TRUE setzten. falls Template Tags ohne Inhalt standardmäßig
	//markiert werden sollen, um das Debugging zu erleichtern.
	var $debug = FALSE;
	
	/**
	 * @access public
	 * @return void
	 * @param content string Content, kann auch Content-Array sein
	 * @param name string
	 * @desc weisst einem Content Tag im Template Content zu
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
	 * @param content string Content, kann auch Content-Array sein
	 * @param name string
	 * @desc Alias für add_content
	 */	
	function assign($content, $name)
	{
		$this->add_content($content, $name);
	}
	
	/** 
	 * @access public
	 * @return void
	 * @param switch boolean TRUE oder FALSE für an oder aus
	 * @desc schaltet das Debugging ein oder aus
	 */
	function debugging($switch)
	{
		$this->debug = $switch;
	}
		
	/**
	 * @access public
	 * @return void
	 * @param filename string
	 * @desc schreibt das Template nach $filename
	 */
	function dump($filename)
	{
		$this->
		$fp = fopen($filename, 'wb');
		fwrite($fp, $this->file);
		fclose($fp);
		
		return file_exists($filename);
	}
	
	/**
	 * @access public
	 * @return string
	 * @desc liefert das Template zurück
	 */
	function get()
	{
		return $this->file;
	}
		
	/**
	 * @access private
	 * @return array
	 * @desc liefert den bisher via add_content gelieferten Content zurück
	 */
	function get_content()
	{
		return $this->content;
	}
	
	/**
	 * @access private
	 * @return string
	 * @param string string String, in welchem sich ein Loop befinden könnte
	 * @desc liefert den ersten Loop in $string zurück
	 */
	function get_loop($string)
	{
		$subloop = 1;
		
		//macht die Funktion unempfindlich gegen Gross-/Kleinschreibung
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
				
				
				//Loop im Loop, ignoriere den nächsten end loop Tag
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
	 * @param string string String, in welchem sich ein Loop befinden könnte
	 * @desc liefert den Namen des ersten Loops in $string zurück
	 */
	function get_loopname($string)
	{
		//macht die Funktion unempfindlich gegen Gross-/Kleinschreibung
		$lowercase = strtolower($string);
		
		$begin     = strpos($lowercase, '<!-- begin loop {');
		$end       = strpos($lowercase, '} -->', $begin);
		$loopname  = substr($string, $begin + 17, $end - $begin - 17);
		
		return $loopname;
	}
	
	/**
	 * @access public
	 * @return void
	 * @desc liefert das Template an den Browser zur Ausgabe
	 */
	function output()
	{
		echo $this->file;
	}
	
	/**
	 * @access public
	 * @return boolean
	 * @desc verarbeitet das Template: fügt Content in Template ein
	 */
	function parse()
	{
		if(!$this->file)
		{
			$verification = FALSE;
		}
		else
		{
			//verarbeitet Server Side Includes
			while(eregi('<!--#include virtual="', $this->file))
			{
				$this->process_ssis();
			}
			
			//verarbeitet Loop-Tags
			if(eregi('<!-- begin loop {', $this->file))
			{
				$this->file = $this->process_loop($this->file, $this->content);
			}
			
			//verarbeitet Content-Tags
			$this->file = $this->replace_template_tags($this->content, $this->file);
			
			$verification = TRUE;
		}
		
		return $verification;
	}
	
	/**
	 * @access public
	 * @return void
	 * @desc Klassen-Konstruktor
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
	 * @param string string String, in welchem sich Loops befinden könnten
	 * @param content array assoziatives Content-Array
	 * @desc verarbeitet Loops
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
					
					//Rekursion um Loops in Loops zu finden
					$loopcopy     = $this->process_loop($loopcopy, $lap_content);
					
					$parsed_loop .= $this->replace_template_tags($lap_content, $loopcopy);
				}
			}
			
			//ersetzte Loop durch Content
			$string = str_replace($loop, $parsed_loop, $string);
		}
		
		return $string;
	}
	
	/**
	 * @access private
	 * @return void
	 * @desc verarbeitet Server Side Includes
	 */
	function process_ssis()
	{
		//macht die Funktion unempfindlich gegen Gross-/Kleinschreibung
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
	 * @param filename string Dateiname inklusive relativem Dateipfad
	 * @desc liest die angegebene Datei als Template ein
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
	 * @param content array assoziatives Array, muss nicht Content für alle Content Tags im String enthalten
	 * @param string string String, in welchem Content Tags enthalten sein könnten
	 * @desc ersetzt alle Content-Tags in $string durch Content
	 */
	function replace_template_tags($content, $string)
	{
		while(strpos($string, '<!-- {'))
		{
			$begin   = strpos($string, '<!-- {') + 6;
			$end     = strpos($string, ' -->', $begin) - 1;
			$tagname = substr($string, $begin, $end - $begin);
			$tag     = '<!-- {'.$tagname.'} -->';
			
			//falls das Debugging aktiviert ist, werden leere
			//Content-Tags markiert um deren Auffinden zu erleichtern
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
	 * @param loopname string Name des Loops, dessen Tags entfernt werden sollen
	 * @param string string String, in welchem sich der Loop befindet
	 * @desc entfernt die Loop-Tags des Loops $loopname in $string
	 */
	function strip_looptag($loopname, $string)
	{
		$string = eregi_replace('<!-- begin loop {'.$loopname.'} -->', '', $string);
		$string = substr($string, 0, strlen($string) - 17);
		
		return $string;
	}
}
?>