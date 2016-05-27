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
 * aliroSearchParser will take a search string including logical operators
 * and return a string suitable for use with the MySQL text search with the
 * parameter IN BOOLEAN MODE.
 */

class aliroSearchParser {
	private $tokens = array('');
	private $tokentext = array(false);
	private $track = array();
	private $tid = 0;
	private $next = 0;

	public function parse ($text) {
		$inquote = false;
		while ($text != ($text = str_replace('  ', ' ', $text)));
		for ($i=0; $i<strlen($text); $i++) {
			$char = $text[$i];
			if ('"' == $char) {
				if ($inquote) $this->tid = array_pop($this->track);
				else $this->startNext(true);
				$inquote = !$inquote;
			}
			elseif (!$inquote AND '(' == $char) $this->startNext(false);
			elseif (!$inquote AND ')' == $char) {
				if (count($this->track)) $this->tid = array_pop($this->track);
			}
			else $this->tokens[$this->tid] .= $char;
		}
		$this->addAnds();
		$this->addBrackets();
		foreach ($this->tokens as $i=>$token) if (!$this->tokentext[$i]) $this->tokens[$i] = $this->convertLogic($token);
		$text = $this->recombine();
		$text = $this->finishOff($text);
		return $text;
	}

	private function startNext ($isText) {
		$this->next++;
		$this->tokentext[$this->next] = $isText;
		$this->tokens[$this->tid] .= "#$this->next#";
		array_push($this->track,$this->tid);
		$this->tid = $this->next;
		$this->tokens[$this->tid] = '';
	}

	private function addAnds () {
		$logic = array('NOT', 'OR', 'AND');
		foreach ($this->tokens as $i=>$token) {
			if ($this->tokentext[$i]) continue;
			$anded = array();
			$words = explode (' ',$token);
			$preceding = '';
			foreach ($words as $sub=>$word) {
				if (!in_array($word, $logic) AND $sub > 0 AND !in_array($words[$sub-1], $logic)) array_push($anded, 'AND');
				array_push($anded, $word);
			}
			$this->tokens[$i] = implode(' ', $anded);
		}
	}

	private function addBrackets () {
		$logic = array('NOT', 'OR', 'AND');
		$priority = 'OR';
		$nonpriority = 'AND';
		foreach ($this->tokens as $i=>$token) {
			$pmode = false;
			$nmode = false;
			$opened = false;
			$words = explode(' ', $token);
			foreach ($words as $j=>$word) {
				if ($priority == $word) {
					if ($nmode AND $j>0) {
						$words[$j-1] = '('.$words[$j-1];
						$opened = true;
					}
					$pmode = true;
					$nmode = false;
				}
				if ($nonpriority == $word) {
					if ($pmode AND $j>0) $words[$j-1] = $words[$j-1].')';
					if (!$opened) $words[0] = '('.$words[0];
					$pmode = false;
					$nmode = true;
				}
			}
			if ($pmode AND $opened) $words[j] .= ')';
			$this->tokens[$i] = implode(' ', $words);
		}
	}

	private function convertLogic ($text) {
		$regexes = array(
			'/\bOR\b/',
			'/\bAND\b/',
			'/\bNOT\b/'
		);
		$replace = array(
			' ',
			'+',
			'-'
		);

		$text = preg_replace($regexes, $replace, $text);

		$newword = true;
		$parsed = $nonblank = '';
		$depth = 0;
		for ($i=0; $i<strlen($text); $i++) {
			$char = $text[$i];
			if (' ' != $char) $nonblank .= $char;
			if ('(' == $char) {
				$newword = true;
				$start[$depth] = strlen($parsed);
				$parsed .= ' (';
				$depth++;
			}
			elseif (')' == $char) {
				$newword = true;
				$parsed .= $char;
				if (isset($start[$depth])) unset($start[$depth]);
				$depth--;
			}
			elseif (' ' == $char) {
				$newword = true;
				$parsed .= $char;
			}
			elseif ('+' == $char) {
				$newword = true;
				if (isset($start[$depth])) $parsed[$start[$depth]] = '+';
				$parsed .= '+';
			}
			else {
				if ($newword) {
					$start[$depth] = strlen($parsed);
					$parsed .= ' '.$char;
				}
				else $parsed .= $char;
				$newword = false;
			}
		}
		return $parsed;
	}

	private function recombine () {
		$text = isset($this->tokens[0]) ? $this->tokens[0] : '';
		for ($i=1; $i<count($this->tokens); $i++) {
			if ($this->tokentext[$i]) $text = str_replace("#$i#", '"'.$this->tokens[$i].'"', $text);
			else $text = str_replace("#$i#", '('.$this->tokens[$i].')', $text);
		}
		return $text;
	}

	private function finishOff ($parsed) {
		$mess = array('  ', '+ +', '++', '( ', ' )', '+ ', '- ');
		$tidy = array(' ', '+', '+', '(', ')', '+', '-');
		while ($parsed != ($parsed = str_replace($mess, $tidy, $parsed)));
		return $parsed;
	}

}
