<?php

/**
 * Part of Aliro SEF Manager - see root index.php for copyright etc.
 *
 */

class sefAdminTransform extends sefAdminControllers {
	protected static $instance = null;

	public static function getInstance ($manager) {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = new self($manager));
	}
	
	public function getRequestData () {
	}

	public static function taskTranslator () {
		return array (
		'panel' => T_('SEF CP'),
		'list' => T_('Cancel'),
		'save' => T_('Save'),
		'savecomponent' => T_('Save'),
		'remove' => T_('Delete')
		);
	}
	
	public function toolbar () {
		$task = $this->getParam($_REQUEST, 'task');
		if ('component' == $task) {
			$this->toolBarButton('savecomponent');
			$this->toolBarButton('list');
		}
		else {
			$this->toolBarButton('save');
			$this->toolBarButton('panel');
		}
	}

	public function panelTask () {
		$this->redirect('index.php?core=cor_sef');
	}
	
	public function listTask () {
		$this->getData();
		echo "\n<div id='remosefadmin'>";
		$this->showSubstitutions();
		$this->showSubstitutionsIn();
		$this->showSubstitutionsOut();
		$this->showCharacters();
		$this->showComponents();
		$this->showContentTasks();
		?>
		<input type="hidden" name="core" value="cor_sef" />
		<input type="hidden" name="act" value="transform" />
		<?php
		echo "\n</div>";
		echo "\n<!-- End of code from Remosef -->";
	}

	private function getData () {
		$configs = $this->database->doSQLget ("SELECT * FROM #__remosef_config");
		$vars = get_object_vars($this);
		foreach ($configs as $item) {
			if ('components' == $item->type) {
				$this->custom_code[] = $item->name;
				$this->custom_name[] = $item->modified;
				$this->custom_retrieve[] = $item->flags;
			}
			elseif ('characters' == $item->type) {
				$this->sef_name_chars[] = $item->name;
				$this->sef_translate_chars[] = $item->modified;
			}
			elseif ('substitutions' == $item->type) {
				$this->sef_substitutions_exact_name[$item->id] = $item->name;
				$this->sef_substitutions_exact_mod[$item->id] = $item->modified;
			}
			elseif ('substitutions_in' == $item->type) {
				$this->sef_substitutions_in[$item->name] = $item->modified;
			}
			elseif ('substitutions_out' == $item->type) {
				$this->sef_substitutions_out[$item->name] = $item->modified;
			}
			elseif ('content' == $item->type) $this->sef_content_task[$item->name] = $item->modified;
			else $this->component_details[$item->type][$item->name] = $item->modified;
		}
		unset($configs);
	}

	private function showSubstitutions () {
		$link = $this->getCfg('admin_site').'/index2.php?option=com_sef&amp;act=config&amp;task=metadata&amp;cid=';
		$this->headingLine(T_('URI Substitutions - Exact'));
		echo "\n\t<p>".T_('The left box should be the exact standard CMS URI (e.g. /index.php?option=com_text&amp;task=display&amp;id=21), the right box what it is to be translated to (e.g. /about/john-smith/).  The translation will be exact and will be applied immediately a URI is received.');
		foreach ($this->sef_substitutions_exact_name as $id=>$name) {
			$linkmeta = <<<LINK_META

			<a href="$link$id">Edit metadata</a>

LINK_META;
			$this->translateLine('subst', $name, $this->sef_substitutions_exact_mod[$id], 50, $linkmeta);
		}
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst', '', '',50);
	}

	private function showSubstitutionsIn () {
		$this->headingLine('URI Substitutions - Inbound');
		echo "\n\t<p>The left box must be a valid regular expression that is to be applied to an incoming URI.  The right box must be what it is to be translated to.  The substitution will be applied immediately on receipt of a URI unless the URI has an exact match in the table above.</p>";
		echo "\n\t<p>This is designed to handle transformation of old URIs and care must be taken to avoid disrupting the normal decoding process.";
		echo "\n\t<p>Remember that regular expressions use metacharacters such as * or ? or ].  To use them as ordinary characters, they must be escaped with backslash.";
		foreach ($this->sef_substitutions_in as $key=>$name) $this->translateLine('subst_in', $key, $name, 50);
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst_in', '', '',50);
	}
	
	private function showSubstitutionsOut () {
		$this->headingLine('URI Substitutions - Outbound');
		echo "\n\t<p>The left box must be a valid regular expression.  The right hand box will be substituted for it.  The substitution will be applied after all other outgoing URI processing.";
		echo "\n\t<p>Remember that regular expressions use metacharacters such as * or ? or ].  To use them as ordinary characters, they must be escaped with backslash.";
		foreach ($this->sef_substitutions_out as $key=>$name) $this->translateLine('subst_out', $key, $name, 50);
		for ($i = 0; $i < 5; $i++) $this->translateLine('subst_out', '', '',50);
	}
	
	private function showComponents () {
		$link = 'index.php?core=cor_sef&amp;act=transform&amp;task=component&amp;component=';
		$this->headingLine('Component names');
		echo "\n\t<p>If a component is listed here, its sef_ext.php (if any) will be used; the name will be translated in any case.  Make sure the translated name does not conflict with content component tasks.  Editing details depends on the sef_ext file being present and supporting the extended Remosef interface.</p>";
		foreach ($this->custom_code as $i=>$key) {
			$name = $this->custom_name[$i];
			$linkhtml = <<<COMP_LINK

			<a href="$link$key">Edit details</a>

COMP_LINK;

			$this->translateLinePlus('comp', $key, $name, $this->custom_retrieve[$i], 30, $linkhtml);
		}
		for ($i = 0; $i < 5; $i++) $this->translateLinePlus('comp', '', '', '', 30, '<span>Edit details</span>');
	}

	private function showCharacters () {
		$this->headingLine('Character string translations');
		echo "\n\t<p>The box on the left must be a string of one or more characters; so must the box on the right.  Any occurrences of left hand strings will be substituted by the corresponding right string.  Character string translations are applied to names used to build the SEF URI.</p>";
		echo "\n\t<p>The dash (often created by MS Word) is always translated to hyphen, and single quote is always translated to hyphen</p>";
		echo "\n\t<p>Accents are automatically removed for accented characters in the Latin1 set; URL encoding is used if necessary</p>";
		foreach ($this->sef_name_chars as $key=>$name) $this->translateLine ('char', $name, $this->sef_translate_chars[$key]);
		for ($i = 0; $i < 5; $i++) $this->translateLine('char', '', '');
	}

	private function showContentTasks () {
		$content_tasks = array ('findkey',
		'view',
		'section',
		'category',
		'blogsection',
		'blogcategorymulti',
		'blogcategory',
		'archivesection',
		'archivecategory',
		'save',
		'cancel',
		'emailform',
		'emailsend',
		'vote',
		'showblogsection'
		);
		$this->headingLine('Content task translations');
		echo "\n\t<p>You can give alternative names for the content component task words.  Make sure that they do not conflict with component names.</p>";
		foreach ($this->sef_content_task as $key=>$name) $this->translateLine ('ctask', $key, $name);
		foreach ($content_tasks as $task) if (!isset($this->sef_content_task[$task])) $this->translateLine ('ctask', $task, '');
		for ($i = 0; $i < 3; $i++) $this->translateLine('ctask', '', '');
	}

	private function headingLine ($title) {
		echo <<<HEADING

		<div class="remosefhead">$title</div>

HEADING;

	}

	private function inputBox ($title, $name, $value, $width=25) {
		echo <<<INPUT_BOX

			<div class="remosefinput">
				<label for="$name">$title</label>
				<input class="inputbox" type="text" id="$name" name="$name" size="$width" value="$value" />
			</div>

INPUT_BOX;

	}

	private function yesnoBox ($title, $name, $value) {
		$no = $yes = '';
		if ($value) $yes = "selected='selected'";
		else $no = "selected='selected'";
		echo <<<YES_NO

			<div class="remosefinput">
				<label for="$name">$title</label>
				<select id="$name" name="$name">
					<option value="0" $no>No</option>
					<option value="1" $yes>Yes</option>
				</select>
			</div>

YES_NO;

	}

	private function translateLine ($type, $name, $modified, $size=30, $link='') {
		echo <<<TRANSLATE_LINE

		<div class="remosefboxes">
			<input name="{$type}[]" value="$name" class="inputbox" size="$size"" />
			<input name="{$type}mod[]" value="$modified" class="inputbox" size="$size" />
			$link
		</div>

TRANSLATE_LINE;

	}
	
	private function translateLinePlus ($type, $name, $modified, $flag, $size=30, $link='') {
		echo <<<TRANSLATE_LINE

		<div class="remosefboxes">
			<input name="{$type}[]" value="$name" class="inputbox" size="$size"" />
			<input name="{$type}mod[]" value="$modified" class="inputbox" size="$size" />
			<input name="{$type}flag[]" value="$flag" class="inputbox" size="4" />
			$link
		</div>

TRANSLATE_LINE;

	}

	public function saveTask () {
		$this->database->doSQL("DELETE FROM #__remosef_config WHERE type IN ('components', 'options', 'characters', 'content', 'substitutions', 'substitutions_in', 'substitutions_out')");
		$this->custom_code = $this->custom_name = array();
		$this->storeDataGroup ('components','comp','compmod');
		$this->sef_name_chars = $this->sef_translate_chars = array();
		$this->storeDataGroup ('characters', 'char', 'charmod');
		$this->sef_content_task = array();
		$this->storeDataGroup ('content', 'ctask', 'ctaskmod');
		$this->sef_substitutions_exact = array();
		$this->storeDataGroup ('substitutions', 'subst', 'substmod');
		$this->sef_substitutions_in = array();
		$this->storeDataGroup ('substitutions_in', 'subst_in', 'subst_inmod');
		$this->sef_substitutions_out = array();
		$this->storeDataGroup ('substitutions_out', 'subst_out', 'subst_outmod');
		aliroSEF::getInstance()->clearCache();
		$this->redirect('index.php?core=cor_sef', T_('SEF configuration saved'));
	}
	
	private function storeDataGroup ($type, $namecode, $modcode, $flagcode='') {
		$codes = $this->getParam($_POST, $namecode, array());
		$names = $this->getParam($_POST, $modcode, array());
		if ($flagcode) $flags = $this->getParam($_POST, $flagcode, array());

		foreach ($codes as $key=>$code) {
			// Probably better not to strip magic quotes - also removes deliberate escaping of regex characters
			// Should not be quotes in a URI
			/*
			if (get_magic_quotes_gpc()) {
				$code = stripslashes($code);
				if (!empty($names[$key])) $names[$key] = stripslashes($names[$key]);
			}
			*/
			$code = $this->database->getEscaped($code);
			$name = !empty($names[$key]) ? $this->database->getEscaped($names[$key]) : '';
			if ($flagcode) {
				$flag = intval($flags[$key]);
				if ($name AND $code) $this->database->doSQL("INSERT INTO #__remosef_config (type, name, modified, flags) VALUES ('$type', '$code', '$name', $flag)");
			}
			else if ($name AND $code) $this->database->doSQL("INSERT INTO #__remosef_config (type, name, modified) VALUES ('$type', '$code', '$name')");
		}
	}
	
	public function componentTask () {
		$this->getData();
		$component = $this->getParam($_REQUEST, 'component');
		if (false !== strpos($component, '..')) die ('Illegal component specified');
		echo "\n\t\t<h3>Function codes for $component</h3>";
		$sefext = _ALIRO_ABSOLUTE_PATH.'/components/'.$component.'/sef_ext.php';
		if (file_exists($sefext)) {
			require_once($sefext);
			$class_name = str_replace('com', 'sef', $task);
			if (class_exists($class_name, false)) {
				if (method_exists($class_name, 'getInstance')) {
					$sef = call_user_func(array($class_name, 'getInstance'));
					if (method_exists($sef,'tags')) $tags = $sef->tags();
				}
				else {
					if (method_exists($class_name, 'tags')) $tags = call_user_func(array($class_name, 'tags'));
				}
			}
		}
		echo "\n<div id='remosefadmin'>";
		if (isset($tags)) {
			foreach ($tags as $tag) {
				if (isset($this->component_details[$task][$tag])) $translated = $this->component_details[$task][$tag];
				else $translated = '';
				$this->translateLine ($task, $tag, $translated);
			}
		}
		else echo "<p>No function codes found</p>";
		echo <<<END_FORM
				<input type="hidden" name="core" value="cor_sef" />
				<input type="hidden" name="act" value="transform" />
			</div>
			<!-- End of code from Remosef -->

END_FORM;

	}
	
}