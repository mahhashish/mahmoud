<?php

/*******************************************************************************
 * Aliro - the modern, accessible content management system
 *
 * Aliro is open source software, free to use, and licensed under GPL.
 * You can find the full licence at http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * The author freely draws attention to the fact that Aliro derives from Mambo,
 * software that is controlled by the Mambo Foundation.  However, this section
 * of code is totally new.  If it should contain any fragments that are similar
 * to Mambo, please bear in mind (1) there are only so many ways to do things
 * and (2) the author of Aliro is also the author and copyright owner for large
 * parts of Mambo 4.6.
 *
 * Tribute should be paid to all the developers who took Mambo to the stage
 * it had reached at the time Aliro was created.  It is a feature rich system
 * that contains a good deal of innovation.
 *
 * Your attention is also drawn to the fact that Aliro relies on other items of
 * open source software, which is very much in the spirit of open source.  Aliro
 * wishes to give credit to those items of code.  Please refer to
 * http://aliro.org/credits for details.  The credits are not included within
 * the Aliro package simply to avoid providing a marker that allows hackers to
 * identify the system.
 *
 * Copyright in this code is strictly reserved by its author, Martin Brampton.
 * If it seems appropriate, the copyright will be vested in the Aliro Organisation
 * at a suitable time.
 *
 * Copyright (c) 2007 Martin Brampton
 *
 * http://aliro.org
 *
 * counterpoint@aliro.org
 *
 * aliroParameters is the class that implements objects that are held internally as
 * associative arrays, but externally as serialized, encoded strings.  The definition
 * of what a particular set of parameters consists of is normally provided as XML.
 *
 * aliroAdminParameters is used largely, but not exclusively, on the admin side to
 * create parameter groups from XML and serialized data.
 *
 */

class aliroXMLParams {
	protected $xmlobject = null;
	protected $paramcount = 0;
	public $html = array();
	protected $translations = array();

	public function __construct () {
		$this->xmlobject = new aliroXML;
	}
	
	private function helpXgettext () {
		T_('There are no Parameters for this item');
	}

	public function paramsFromFile ($xmlfile, $pobject, $controlname) {
		try {
			$this->xmlobject->loadFile($xmlfile);
			return $this->analyseXML($pobject, $controlname);
		} catch (aliroXMLException $exception) {
	    		aliroRequest::getInstance()->setErrorMessage ($exception->getMessage(), _ALIRO_ERROR_FATAL);
	    		$this->xmlobject = null;
		}
	}

	public function paramsFromString ($xmlstring, $xmlfile, $pobject, $controlname) {
		try {
			$this->xmlobject->loadString($xmlstring);
			return $this->analyseXML($pobject, $controlname);
		} catch (aliroXMLException $exception) {
	    		aliroRequest::getInstance()->setErrorMessage ($exception->getMessage(), _ALIRO_ERROR_FATAL);
	    		$this->xmlobject = null;
		}
	}

	private function analyseXML ($pobject, $controlname) {
		$this->html[] = '<table class="paramlist">';
		$params = $this->xmlobject->getXML('params->param');
		if ($params) foreach ($params as $aparam) {
			$this->processParam($aparam, $pobject, $controlname);
		}
		if ($this->paramcount == 0) {
			return <<<NULL_HTML

				<table class="paramlist">
					<tr>
						<td colspan="2">
							<i>{$this->T_('There are no Parameters for this item')}</i>
						</td>
					</tr>
				</table>

NULL_HTML;

		}
		$this->html[] = '</table>';
		$this->paramcount = 0;
		return implode("\n", $this->html);
	}

	protected function T_ ($string) {
		return T_($string);
	}

	private function processParam ($param, $pobject, $controlname) {
	    $alirohtml = aliroHTML::getInstance();

	    $type = (string) $param['type'];
		$name = (string) $param['name'];
		// if ($name) $this->html[] = "<tr><td colspan='3'>$name</td></tr>";
		$label = (string) $param['label'];
		$default = (string) $param['default'];
		if ($description = (string) $param['description']) $tooltip = $alirohtml->toolTip($description, $name);
		else $tooltip = '';

		if (is_object($pobject)) $value = $pobject->get($name, $default);
		else $value = $default;

		$this->html[] = '<tr>';
		if ($label == '@spacer') $label = '<hr />';
		elseif ($label) $label .= ':';
		$this->html[] = '<td width="35%" align="right" valign="top">'.$label.'</td>';
		switch ($type) {
			case 'text':
			    $size = (string) $param['size'];
			    $controlstring = '<input type="text" name="'.$controlname.'['.$name.']" value="'.$value.'" class="text_area" size="'.$size.'" />';
				break;
			case 'list':
				$options = array();
				foreach ($param->option as $option) $options[] = $alirohtml->makeOption((string) $option['value'], (string) $option);
			    $controlstring = $alirohtml->selectList($options, $controlname.'['.$name.']', 'class="inputbox"', 'value', 'text', $value);
				break;
			case 'radio':
				$options = array();
				foreach ($param->option as $option) $options[] = $alirohtml->makeOption((string) $option['value'], (string) $option);
			    $controlstring = $alirohtml->radioList($options, $controlname.'['.$name.']', '', $value);
				break;
			case 'imagelist':
			    $directory = new aliroDirectory (_ALIRO_ABSOLUTE_PATH.(string) $param['directory']);
			    $files = $directory->listFiles ('\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$');
			    $options = array();
			    foreach ($files as $file) $options[] = $alirohtml->makeOption($file, $file);
			    if (!isset($param['hide_none'])) array_unshift($options, $alirohtml->makeOption('-1', '- Do not use an image -' ));
			    if (!isset($param['hide_default'])) array_unshift($options, $alirohtml->makeOption('', '- Use Default image -'));
			    $controlstring = $alirohtml->selectList ($options, "$controlname[$name]", 'class="inputbox"', 'value', 'text', $value);
				break;
			case 'textarea':
		        $rows = (string) $param['rows'];
		        $cols = (string) $param['cols'];
		        $value = str_replace ('<br /', "\n", $value);
		        $controlstring = "<textarea name='params[$name]' cols='$cols' rows='$rows' class='text_area'>$value</textarea>";
				break;
			case 'editarea':
				$editor = aliroEditor::getInstance();
				$controlstring = $editor->editorAreaText( $controlname.'['.$name.']',  $value , $controlname.'['.$name.']', '700', '350', '95', '30' ) ;
				break;
			case 'dynamic':
				$class = (string) $param['class'];
				$method = (string) $param['method'];
				if ($class) $object = call_user_func($class, 'getInstance');
				if (is_object($object) AND $method) {
					$controlstring = $object->$method($name, $value, $controlname, $param);
					break;
				}
				$controlstring = sprintf(T_('Dynamic parameter class: %s, method: %s failed'), $class, $method);
				break;
		    case 'spacer':
				$controlstring = $value ? $value : '<hr />';
				break;
			case 'mos_section':
				$controlstring = _form_mos_section($name, $value, $controlname);
				break;
			case 'mos_category':
				$controlstring = _form_mos_category($name, $value, $controlname);
				break;
			case 'mos_menu':
				$controlstring = $this->_form_mos_menu($name, $value, $controlname);
				break;
			default:
				$controlstring = T_('Handler not defined for type').'='.$type;
		}
		$this->html[] = "<td>$controlstring</td>";
		$this->html[] = "<td width='10%' align='left' valign='top'>$tooltip</td>";
		$this->html[] = '</tr>';
		$this->paramcount++;
	}

	/**
	* @param string The name of the form element
	* @param string The value of the element
	* @param object The xml element for the parameter
	* @param string The control name
	* @return string The html for the element
	*/
	protected function _form_mos_section( $name, $value, $control_name ) {
		$database = mamboDatabase::getInstance();
		$query = "SELECT id AS value, title AS text"
		. "\n FROM #__sections"
		. "\n WHERE published='1' AND scope='content'"
		. "\n ORDER BY title"
		;
		$database->setQuery( $query );
		$options = $database->loadObjectList();
		array_unshift($options, aliroHTML::getInstance()->makeOption( '0', '- Select Content Section -' ));
		return aliroHTML::getInstance()->selectList( $options, "$control_name[$name]", 'class="inputbox"', 'value', 'text', $value );
	}
	/**
	* @param string The name of the form element
	* @param string The value of the element
	* @param object The xml element for the parameter
	* @param string The control name
	* @return string The html for the element
	*/
	protected function _form_mos_category( $name, $value, $control_name ) {
		$firstoption = aliroHTML::getInstance()->makeOption('0', '- Select Content Category -');
		$database = mamboDatabase::getInstance();
		$query 	= "SELECT c.id AS value, CONCAT_WS( '/',s.title, c.title ) AS text"
		. "\n FROM #__categories AS c"
		. "\n LEFT JOIN #__sections AS s ON s.id=c.section"
		. "\n WHERE c.published='1' AND s.scope='content'"
		. "\n ORDER BY c.title"
		;
		$database->setQuery( $query );
		$options = $database->loadObjectList();
		if ($options) array_unshift($options, $firstoption);
		else $options = array($firstoption);
		return aliroHTML::getInstance()->selectList( $options, "$control_name[$name]", 'class="inputbox"', 'value', 'text', $value );
	}

	function _form_mos_menu( $name, $value, $control_name ) {
		$handler = aliroMenuHandler::getInstance();
		$menuTypes = $handler->getMenutypes();
		$alirohtml = aliroHTML::getInstance();
		$options[] = $alirohtml->makeOption( '', '- Select Menu -' );
		foreach($menuTypes as $menutype ) $options[] = $alirohtml->makeOption( $menutype, $menutype );
		return $alirohtml->selectList( $options, ''. $control_name .'['. $name .']', 'class="inputbox"', 'value', 'text', $value );
	}
}

class aliroXMLParamsDefault {
	protected $xmlobject = null;

	public function __construct () {
		$this->xmlobject = new aliroXML;
	}

	public function paramsFromFile ($xmlfile) {
		$this->xmlobject->loadFile($xmlfile);
		return $this->analyseXML();
	}

	public function paramsFromString ($xmlstring) {
		$this->xmlobject->loadString($xmlstring);
		return $this->analyseXML();
	}

	private function analyseXML () {
		$pobject = new aliroParameters;
		foreach ($this->xmlobject->getXML('params->param') as $aparam) {
			$name = (string) $aparam['name'];
			$default = (string) $aparam['default'];
			if ($name AND $default) $pobject->set($name, $default);
		}
		return $pobject;
	}

}