<?php
class word_count {
    var $word_list;
    function word_count() {
        $this -> word_list = array();
    }

    function add_text($text) {
        $words = explode(' ', $text);
        foreach ($words as $word) {
            $word_object = new word($word);
            $this -> word_list = $word_object -> count($this -> word_list);
        }
    }

}

class word {
    var $count;
    var $word;
    function word($word) {
        $this -> count = 0;
        $this -> word = $word;
    }

    function count($word_list) {
        $added = false;
        foreach ($word_list as $list_entry) {
            if ($list_entry -> get_word() == $this -> get_word()) {
                $list_entry -> increment();
                $added = true;
            }
        }
        if (!$added) {
            $this -> increment();
            $word_list[] = $this;
        }
        return ($word_list);
    }

    function get_word() {
        return ($this -> word);
    }

    function increment() {
        $this -> count++;
    }

}

$word_counter = new word_count;
$word_counter -> add_text('cows eat grass');
$word_counter -> add_text('horses eat grass');
echo "<pre>";
print_r($word_counter);
echo "</pre>";
?>
