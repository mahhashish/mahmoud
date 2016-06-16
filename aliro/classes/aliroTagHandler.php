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
 * aliroTagHandler ...
 *
 */

class aliroTagHandler extends cachedSingleton {
	protected static $instance = __CLASS__;
	private $alltags = array();
	private $tagsByTypeOrder = array();
	private $typeByID = array();

	protected function __construct () {
		$this->alltags = aliroDatabase::getInstance()->doSQLget("SELECT id, ordering, frequency, published, hidden, type, name FROM #__tags ORDER BY ordering", 'stdClass', 'id');
		foreach ($this->alltags as $id=>$tag) {
			$this->tagsByTypeOrder[$tag->type][] = $id;
			$this->typeByID[$id] = $tag->type;
		}
	}

	public static function getInstance () {
	    return is_object(self::$instance) ? self::$instance : (self::$instance = parent::getCachedSingleton(self::$instance));
	}
	
	public function getTypes () {
		return array_keys($this->tagsByTypeOrder);
	}
	
	public function getTagsOrder ($type) {
		$ids = empty($this->tagsByTypeOrder[$type]) ? array() : $this->tagsByTypeOrder[$type];
		return $this->findTagObjects($ids);
	}
	
	public function getTagsFreq ($type) {
		trigger_error('This does not work');
		$ids = empty($this->tagsByTypeFreq[$type]) ? array() : $this->tagsByTypeFreq[$type];
		return $this->findTagObjects($ids);
	}
	
	private function findTagObjects ($ids) {
		$result = array();
		foreach ($ids as $id) $result[] = isset($this->alltags[$id]) ? $this->alltags[$id] : null;
		return $result;
	}
	
	public function findTagNames ($ids) {
		$objects = $this->findTagObjects($ids);
		foreach ($objects as $object) $result[] = is_object($object) ? $object->name : '';
		return (isset($result)) ? $result : array();
	}
	
	public function namesToIds ($nameList) {
		$names = explode(',', $nameList);
		$ids = array();
		foreach ($names as &$name) $name = trim($name);
		foreach ($this->alltags as $id=>$tag) if (in_array($tag->name, $names)) $ids[] = $id;
		return implode(',', $ids);
	}
	
	public function makeSelectList ($type, $name, $addnone=false, $multiple=false, $nulltext='') {
		if (isset($this->tagsByTypeOrder[$type])) {
			if ($multiple) {
				$multitext = 'multiple="multiple"';
				if ('[]' != substr($name,-2)) $name .='[]';
			}
			else $multitext = '';
			if (empty($nulltext)) $nulltext = T_('None of these');
			if ($addnone) $optionlist = <<<NULL_OPTION
			
				<option value="0">$nulltext</option>
			
NULL_OPTION;

			else $optionlist = '';
			foreach ($this->tagsByTypeOrder as $sub) $optionslist .= <<<TAG_OPTION
			
				<option value="{$this->alltags[$sub]->id}">{$this->alltags[$sub]->name}</option>
			
TAG_OPTION;

			if ($optionlist) return <<<TAG_SELECT
			
			<select name="$name" $multitext>
			$optionlist
			</select>
			
TAG_SELECT;

		}
		return '';
	}
	
	public function getAdjacentByOrder ($id, $up=true) {
		$keys = $this->getKeysArray($id, $this->tagsByTypeOrder);
		$sub = array_search($id, $keys);
		return $this->findAdjacent($sub, $keys, $up);
	}
	
	public function getAdjacentByFreq ($id, $up=true) {
		$keys = $this->getKeysArray($id, $this->tagsByTypeFreq);
		$sub = array_search($id, $keys);
		return $this->findAdjacent($sub, $keys, $up);
	}
	
	private function getKeysArray ($id, $tagsArray) {
		if (!isset($this->typeByID[$id]) OR empty($tagsArray[$this->typeByID[$id]])) return array();
		return array_keys($tagsArray[$this->typeByID[$id]]);
	}
	
	private function findAdjacent ($sub, $keys, $up) {
		$adjacent = $sub + ($up ? 1 : -1);
		return isset($keys[$adjacent]) ? $adjacent : 0;
	}
	
}