<?php

	function mosGetParam ($source, $definer, $default='', $mask='') {
		return aliroRequest::getInstance()->getParam($source, $definer, $default, $mask);
	}

	function sefRelToAbs ($string) {
		return aliroSEF::getInstance()->sefRelToAbs($string);
	}

	function initEditor () {
		$editor = aliroEditor::getInstance();
		return $editor->initEditor();
	}

	function getEditorContents( $editorArea, $hiddenField ) {
		aliroEditor::getInstance()->getEditorContents ($editorArea, $hiddenField);
	}

	function editorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
		$editor = aliroEditor::getInstance();
		$editor->editorArea( $name, $content, $hiddenField, $width, $height, $col, $row );
	}

	function mosTreeRecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 ) {
		if (@$children[$id] AND $level <= $maxlevel) {
			$newindent = $indent.($type ? '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '&nbsp;&nbsp;');
			$pre = $type ? '<sup>L</sup>&nbsp;' : '- ';
			foreach ($children[$id] as $v) {
				$id = $v->id;
				$list[$id] = $v;
				$list[$id]->treename = $indent.($v->parent == 0 ? '' : $pre).$v->name;
				$list[$id]->children = count( @$children[$id] );
				$list = mosTreeRecurse( $id, $newindent, $list, $children, $maxlevel, $level+1, $type );
			}
		}
		return $list;
	}

	/**
	* Sorts an Array of objects
	* sort_direction [1 = Ascending] [-1 = Descending]
	*/
	function SortArrayObjects( &$a, $k, $sort_direction=1 ) {
		$sorter = new aliroObjectSorter($a, $k, $sort_direction);
	}

/**
* Sends mail to admin
* Deprecated - not used in Mambo (code copied into weblinks.php)
* Could do with a better facility that works out who to send it to as well
* Note the "email" parameter was not used in the earlier version
*/
function mosSendAdminMail( $adminName, $adminEmail, $email, $type, $title, $author ) {
	$mosConfig_live_site = mamboCore::get('mosConfig_live_site');
	$from = mamboCore::get('mosConfig_mailfrom');
	$fromname = mamboCore::get('mosConfig_fromname');
	$subject = sprintf(T_("User Submitted '%s'"), $type);
	$message = T_("Hello %s,\n\n
A user submitted %s:\n[ %s ]\n
has been just been submitted by user:\n[ %s ]\n\n
for %\n\nPlease go to %s/administrator to view and approve this %s\n\n
Please do not respond to this message as it is automatically generated and is for information purposes only.");
	$message = sprintf($message, $adminName ,$type, $title, $author, $mosConfig_live_site, $mosConfig_live_site, $type);
	require_once(mamboCore::get('mosConfig_absolute_path').'/includes/phpmailer/class.phpmailer.php');
	$mail = new mosMailer ($from, $fromname, $subject, $message);
	return $mail->mosMail($adminEmail);
}

/*
* Includes pathway file
* Needed by templates
*/
function mosPathWay() {
	$pathway = aliroPathway::getInstance();
	echo $pathway->makePathway();
}

/**
* Displays a not authorised message
*
* If the user is not logged in then an addition message is displayed.
*/
function mosNotAuth() {
	$request = aliroUserRequest::getInstance();
	$request->notAuthorised();
}

/**
* Encodes any unencoded ampersands
*
* Needed to handle unicode conflicts due to unicode conflicts
* Deprecated - simply code the line below
*/
function ampReplace( $text ) {
	return preg_replace('/(&(?!(#[0-9]{1,5};))(?!([0-9a-zA-Z]{1,10};)))/', '&amp;', $text);
}

/**
* Chmods files and directories recursively to given permissions. Available from 4.5.2 up.
* @param path The starting file or directory (no trailing slash)
* @param filemode Integer value to chmod files. NULL = dont chmod files.
* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
* @return TRUE=all succeeded FALSE=one or more chmods failed
*/
function mosChmodRecursive($path, $filemode=NULL, $dirmode=NULL)
{
	$fileman = aliroFileManager::getInstance();
	return $fileman->mosChmodRecursive($path, $filemode, $dirmode);
}

/**
* Chmods files and directories recursively to mos global permissions. Available from 4.5.2 up.
* @param path The starting file or directory (no trailing slash)
* @param filemode Integer value to chmod files. NULL = dont chmod files.
* @param dirmode Integer value to chmod directories. NULL = dont chmod directories.
* @return TRUE=all succeeded FALSE=one or more chmods failed
*/
function mosChmod($path)
{
	$fileman = aliroFileManager::getInstance();
	return $fileman->mosChmod($path);
} // mosChmod

/**
 * Function to convert array to integer values
 * Deprecated - not used within Aliro
 */
function mosArrayToInts( &$array, $default=null ) {
	if (is_array( $array )) {
		$n = count( $array );
		for ($i = 0; $i < $n; $i++) {
			$array[$i] = intval( $array[$i] );
		}
	} else {
		if (is_null( $default )) {
			return array();
		} else {
			return array( $default );
		}
	}
}

/**
* Strip slashes from strings or arrays of strings
* @param value the input string or array
*/
function mosStripslashes(&$value)
{
	$database = mamboDatabase::getInstance();
	return $database->mosStripslashes($value);
}

/**
* Copy the named array content into the object as properties
* only existing properties of object are filled. when undefined in hash, properties wont be deleted
* @param array the input array
* @param obj byref the object to fill of any class
* @param string
* @param boolean
*/
function mosBindArrayToObject( $array, &$obj, $ignore='', $prefix=NULL, $checkSlashes=true ) {
	$database = mamboDatabase::getInstance();
	return $database->mosBindArrayToOBject($array, $obj, $ignore='', $prefix=NULL, $checkSlashes=true);
}

/**
* Utility function to read the files in a directory
* @param string The file system path
* @param string A filter for the names
* @param boolean Recurse search into sub-directories
* @param boolean True if to prepend the full path to the file name
*/
function mosReadDirectory( $path, $filter='.', $recurse=false, $fullpath=false  ) {
	if (@is_dir($path)) {
		$dir = new aliroDirectory($path);
		$arr = $dir->listFiles ($filter, $type='both', $recurse, $fullpath);
	}
	else $arr = array();
	return $arr;
}

/**
* Utility function redirect the browser location to another url
*
* Can optionally provide a message.
* @param string The file system path
* @param string A filter for the names
*/
function mosRedirect( $url, $msg='' ) {
	aliroRequest::getInstance()->redirect($url, $msg);
}

/**
* Function to strip additional / or \ in a path name
* @param string The path
* @param boolean Add trailing slash
*/
function mosPathName ($p_path, $p_addtrailingslash=true) {
	$fileman = aliroFileManager::getInstance();
	return $fileman->mosPathName($p_path, $p_addtrailingslash);
}

/**
* Checks the user agent string against known browsers
*/
function mosGetBrowser( $agent ) {
	return 'Unknown';
}

/**
* Checks the user agent string against known operating systems
*/
function mosGetOS( $agent ) {
	return 'Unknown';
}

/**
* Makes a variable safe to display in forms
*
* Object parameters that are non-string, array, object or start with underscore
* will be converted
* @param object An object to be parsed
* @param int The optional quote style for the htmlspecialchars function
* @param string|array An optional single field name or array of field names not
*                     to be parsed (eg, for a textarea)
*/
function mosMakeHtmlSafe( &$mixed, $quote_style=ENT_QUOTES, $exclude_keys='' ) {
	mosRequest::mosMakeHtmlSafe($mixed, $quote_style, $exclude_keys);
}

/**
* Checks whether a menu option is within the users access level
* @param int Item id number
* @param string The menu option
* @param int The users group ID number
* @param database A database connector object
* @return boolean True if the visitor's group at least equal to the menu access
*/
function mosMenuCheck( $Itemid, $menu_option, $task, $gid ) {
	// Needs to do something with new permissions system for backwards compatibility
	return true;
}

/**
* Returns formated date according to current local and adds time offset
* @param string date in datetime format
* @param string format optional format for strftime
* @param offset time offset if different than global one
* @returns formated date
*/
function mosFormatDate( $date, $format="", $offset="" ){
	return aliroHTML::getInstance()->formatDate($date, $format, $offset);
}

/**
* Returns current date according to current local and time offset
* @param string format optional format for strftime
* @returns current date
*/
function mosCurrentDate ($format='') {
	$offset = aliroCore::getInstance()->getCfg('offset');
	$language = aliroLanguage::getInstance();
	if (!$format) $format = $language->getDateFormat();
	return $language->getDate($format,  time() + ($offset*60*60));
}

/**
* Utility function to provide ToolTips
* @param string ToolTip text
* @param string Box title
* @returns HTML code for ToolTip
*/
function mosToolTip( $tooltip, $title='', $width='', $image='tooltip.png', $text='', $href='#' ) {
	mosHTML::mosToolTip ($tooltip, $title, $width, $image, $text, $href);
}

/**
* Utility function to provide Warning Icons
* @param string Warning text
* @param string Box title
* @returns HTML code for Warning
*/
function mosWarning($warning, $title=null) {
    if (is_null($title)) $title = T_('Aliro Warning');
	mosHTML::mosToolTip ($warning, $title, '', 'warning.png', '', '#');
}

function mosCreateGUID(){
	$r = rand ;
	$u = uniqid(getmypid() . $r . (double)microtime()*1000000,1);
	$m = md5 ($u);
	return($m);
}

function mosCompressID( $ID ){
	return(Base64_encode(pack("H*",$ID)));
}

function mosExpandID( $ID ) {
	return ( implode(unpack("H*",Base64_decode($ID)), '') );
}

/**
* Mail function (uses phpMailer)
* @param string From e-mail address
* @param string From name
* @param string/array Recipient e-mail address(es)
* @param string E-mail subject
* @param string Message body
* @param boolean false = plain text, true = HTML
* @param string/array CC e-mail address(es)
* @param string/array BCC e-mail address(es)
* @param string/array Attachment file name(s)
* @param string/array Reply-to e-mail address
* @param string/array Reply-to name
*/
function mosMail($from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
	$mail = new mosMailer ($from, $fromname, $subject, $body);
	return $mail->mosMail($recipient, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
} // mosMail

/**
* Create mail object
* @return mail object
*/
function mosCreateMail ($from, $fromname, $subject, $body) {
	return new mosMailer ($from, $fromname, $subject, $body);
}

/**
* Random password generator
* @return password
*/
function mosMakePassword() {
	return aliroAuthenticator::makePassword();
}

/**
* @param string
* @return string
* Deprecated - use the code within this function instead - not used in Mambo
*/
function mosParseParams( $txt ) {
	echo aliroBase::trace();
	die ('The functioning of parameters is different in Aliro, please review your extensions');
}

?>
