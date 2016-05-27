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
		$names = array_map('trim', explode(',', $nameList));
		$ids = array();
		foreach ($this->alltags as $id=>$tag) if (in_array($tag->name, $names)) $ids[] = $id;
		return implode(',', $ids);
	}
	
	public function makeSelectList ($type, $name, $addnone=false, $multiple=false, $nulltext='') {
		if (isset($this->tagsByTypeOrder[$type])) {
			$optionlist = $addnone ? $this->showNoneOption($nulltext) : '';
			$optionlist .= $this->showTagIDSet($this->tagsByTypeOrder[$type]);
			if ($optionlist) return $this->tagSelect($name, $multiple, $optionlist);
		}
	}
	
	public function makeSelectListAllTypes ($name, $values=null, $addnone=false, $multiple=false, $nulltext='') {
		$optionhtml = $addnone ? $this->showNoneOption($nulltext) : '';
		foreach ($this->getTypes() as $type) $optionhtml .= $this->showTagIDSet($this->tagsByTypeOrder[$type], $values, $type);
		if ($optionhtml) return $this->tagSelect($name, $multiple, $optionhtml);
	}
	
	protected function tagSelect ($name, $multiple, $optionhtml) {
		if ($multiple) {
			$multitext = ' multiple="multiple"';
			if ('[]' != substr($name,-2)) $name .='[]';
		}
		else $multitext = '';
		return <<<TAG_SELECT
			
			<select name="$name"$multitext>
			$optionhtml
			</select>
			
TAG_SELECT;
		
	}
	
	protected function showNoneOption ($nulltext) {
		if (empty($nulltext)) $nulltext = T_('None of these');
		return <<<NULL_OPTION
			
				<option value="0">$nulltext</option>
			
NULL_OPTION;
		
	}
	
	protected function showTagIDSet ($tagids, $values=null, $type='') {
		$html = '';
		foreach ($tagids as $tagid) {
			$selected = in_array($tagid, (array) $values) ? ' selected="selected"' : '';
			$name = ($type ? $type.' - ' : '').$this->alltags[$tagid]->name;
			$html .= <<<TAG_OPTION
			
				<option value="{$this->alltags[$tagid]->id}"$selected>$name</option>
			
TAG_OPTION;

		}
		return $html;
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