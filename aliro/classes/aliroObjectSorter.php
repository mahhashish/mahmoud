<?php

/**
* Sorts an Array of objects
*/
class aliroObjectSorter {
    var $_keyname = '';
    var $_direction = 0;
    var $_object_array = array();

    public function __construct (&$a, $k, $sort_direction=1) {
        $this->_keyname = $k;
        $this->_direction = $sort_direction;
        $this->_object_array =& $a;
        $this->sort();
    }

    // This is not genuinely public, but has to be declared so for the callback
    public function aliroObjectCompare (&$a, &$b) {
        $key = $this->_keyname;
        if ($a->$key > $b->$key) return $this->_direction;
        if ($a->$key < $b->$key) return -$this->_direction;
        return 0;
    }

    private function sort () {
        usort($this->_object_array, array($this,'aliroObjectCompare'));
    }

}
