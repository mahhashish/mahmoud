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
class aliroParameters {
    protected $params = array();
    protected $raw = null;
	protected $xml = null;

	public function __construct ($text='', $xml='') {
        $this->raw = is_null($text) ? '' : $text;
		if (!is_string($this->raw)) trigger_error (T_('Raw data for aliroParameters not a string'));

        $this->params = @unserialize($this->raw);
        if (!is_array($this->params)) $this->params = array();
		if ($this->raw AND count($this->params) == 0) trigger_error (T_('Raw data for aliroParameters was not null, but did not yield any values'));

        foreach ($this->params as &$param) $param = base64_decode($param);
	    $this->xml = $xml;
    }

	public function getParams () {
        die ('Aliro handles parameters differently, please review what you need');
    }

	public function set( $key, $value='' ) {
        $this->params[$key] = $value;
        return $value;
    }

    public function setAll ($keyedValues) {
    	$this->params = $keyedValues;
    }

    public function def( $key, $value='' ) {
        return $this->set ($key, $this->get($key, $value));
    }

    public function get( $key, $default='' ) {
        if (isset($this->params[$key])) return $this->params[$key] === '' ? $default : $this->params[$key];
        else return $default;
    }

    public function __get ($property) {
    	return $this->get ($property);
	}

	public function processInput ($params) {
		$inarray = (array) $params;
		foreach ($inarray as &$param) if (ini_get('magic_quotes-gpc')) $param = stripslashes($param);
		$this->params = $inarray;
		return $this->asString();
	}
	
	public function asString () {
		$encoded = array();
		foreach ($this->params as $key=>$param) $encoded[$key] = base64_encode($param);
		return serialize($encoded);
	}
	
	public function asPost () {
		return $this->params;
	}

	public function render ($name='params') {
		if ($this->xml) {
			$params = new aliroXMLParams;
			if (is_file($this->xml)) return $params->paramsFromFile($this->xml, $this, $name);
			else return $params->paramsFromString($this->xml, T_('Data passed to aliroParameters'), $this, $name);
		}
		return "<textarea name='$name' cols='40' rows='10' class='text_area'>$this->raw</textarea>";
	}

}

class mosParameters extends aliroParameters {
	// Really just an alias for backwards compatibility
}

class mosAdminParameters extends aliroParameters {

	// Just an alias for aliroParameters

}

// Appears not to be used - certainly not used in Aliro

class mosSpecialAdminParameters extends aliroParameters {

	function __construct ($name, $version='') {
	    $database = aliroDatabase::getInstance();
	    $sql = "SELECT * FROM #__parameters WHERE param_name='$name'";
	    if ($version) $sql .= " AND param_version='$version'";
	    $database->setQuery($sql);
	    $parameters = $database->loadObjectList();
	    if ($parameters) $parameters = $parameters[0];
	    parent::__construct($parameters->params, aliroCore::get('mosConfig_absolute_path').'/parameters/'.$parameters->param_file);
	}
}