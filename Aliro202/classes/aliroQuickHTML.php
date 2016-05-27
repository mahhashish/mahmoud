<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * This code is copyright (c) Aliro Software Ltd - please see the notice in the
 * index.php file for full details or visit http://aliro.org/copyright
 *
 * Some parts of Aliro are developed from other open source code, and for more
 * information on this, please see the index.php file or visit
 * http://aliro.org/credits
 *
 * Author: Martin Brampton
 * counterpoint@aliro.org
 *
 * aliroQuickHTML is a simple HTML form creation tool.
 */

class aliroQuickHTML {
	private $sourcecode = '';
	private $fieldnames = array();
	private $blocked = array();
	private $parsed = false;
	
	public function __construct ($sourcecode='') {
		$this->setSourceCode($sourcecode);
	}
	
	public function setSourceCode ($sourcecode) {
		$this->sourcecode = trim($sourcecode);
	}
	
	public function setBlockedNames ($names) {
		$this->blocked = (array) $names;
	}
	
	public function newDatabaseFields ($tablename) {
		if (!$this->parsed) $this->parseForOutput();
		$dbfields = $this->getFields($tablename);
		return array_diff($this->fieldnames, $dbfields);
	}
	
	public function oldDatabaseFields ($tablename) {
		if (!$this->parsed) $this->parseForOutput();
		$dbfields = $this->getFields($tablename);
		return array_diff($dbfields, $this->fieldnames, $this->blocked);
	}
	
	private function getFields ($tablename) {
		$database = aliroDatabase::getInstance();
		return $database->getAllFieldNames($tablename);
	}

	public function parseForOutput () {
		if (!$this->sourcecode) return '';
		$coderegex = '/{([a-z0-9_]*):([0-9]*:[0-9]*|[0-9]*|(radio:)[^}]*|(checkbox:)[^}]*|(timezone:)[^}]*|[^}^:]*)?}/i';
		$bits = preg_split($coderegex, $this->sourcecode);
		preg_match_all($coderegex, $this->sourcecode, $matches, PREG_SET_ORDER);
		$text = $bits[0];
		$this->fieldnames = array();
		$outstring = '';
		foreach ($matches as $i=>$match) {
			$fieldname = $match[1];
			if (in_array($fieldname, $this->blocked)) continue;
			$this->fieldnames[] = $fieldname;
			if (isset($match[3])) {
 				if (substr($match[2],0,6) == 'radio:') {
  				// Make radio button item using field name and substr($match[2],6)
  				$outstring .= $this->makeRadio($text, $fieldname, substr($match[2],6));
 				}
 				elseif (substr($match[2],0,9) == 'checkbox:') {
 					// Make tickbox item using field name and substr($match[2],9)
 					$outstring .= $this->makeBoxes($text, $fieldname, substr($match[2],9));
 				}
 				elseif (substr($match[2],0,9) == 'timezone:') {
 					// Make timezone item using field name and substr($match[2],9)
 					$outstring .= $this->makeTimeZone($text, $fieldname, substr($match[2],9));
				}
 				else $text .= $match[0];
			}
			elseif (strpos($match[2], '&&')) {
				// Make menu allowing multiple items using field name and $match[2]
				$outstring .= $this->makeMenu ($text, $fieldname, $match[2], true);
			}
			elseif (strpos($match[2], '||')) {
				// Make menu (single items) using field name and $match[2]
				$outstring .= $this->makeMenu ($text, $fieldname, $match[2], false);
			}
			else {
				// Make input or textarea with numbers in $match[2], pass $match[0] in case not numeric
				$outstring .= $this->makeInput ($text, $fieldname, $match[2], $match[0]);
			}
			$text .= $bits[$i+1];
		}
		$this->parsed = true;
		return $outstring;
	}

	// Make radio button item using field name and options
	public function makeRadio (&$text, $field, $options) {
		$html = '';
		$choices = explode('||', $options);
		foreach ($choices as $choice) {
			$html .= "\n".'<p><input type="radio" name="'.$field.'" value="'.$choice.'" />'.$choice.'</p>';
		}
		return $html;
	}

	public function makeBoxes (&$text, $field, $options) {
		$html = '';
		$choices = explode('&&', $options);
		foreach ($choices as $choice) {
			$html .= "\n".'<p><input type="checkbox" name="'.$field.'[]" value="'.$choice.'" />'.$choice.'</p>';
		}
		return $html;
	}

	public function makeMenu (&$text, $field, $options, $multiple) {
		if ($multiple) {
			$field .= '[]';
			$suffix = 'multiple="multiple"';
		}
		else $suffix = '';
		$html = "\n".'<p><select class="inputbox" name="'.$field.'" '.$suffix;
		$html .= '>';
		if ($multiple) $separator = '&&';
		else $separator = '||';
		$choices = explode($separator, $options);
		foreach ($choices as $choice) {
			$html .= "\n".'<option value="'.$choice.'">'.$choice.'</option>';
		}
		$html .= "\n".'</select></p>';
		return $html;
	}

	public function makeInput (&$text, $field, $sizes, $default) {
		$numbers = explode(':', $sizes);
		foreach ($numbers as $number) if ($number != intval($number)) {echo 'not an integer'.$number; return $default;}
		$unique = aliroRequest::getInstance()->getUnique();
		if (count($numbers) > 1) $html = <<<INPUT_AREA
		
		<p>
			<label for="l$unique">$text</label>
			<textarea id="l$unique" name='$field' rows='$numbers[0]' cols='$numbers[1]' class='inputbox'></textarea>
		</p>
		
INPUT_AREA;

		elseif (count($numbers == 1)) $html = <<<INPUT_BOX
		
		<p>
			<label for="l$unique">$text</label>
			<input type='text' id="l$unique" name='$field' size='$numbers[0]' class='inputbox' />
		</p>
		
INPUT_BOX;

		else $html = $default;
		$text = '';
		return $html;
	}
	
	public function makeTimeZone ($field, $sizes, $default) {
		
	}
	
}