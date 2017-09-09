<?php

$string = "It's a string with \"badword\" & the ä character & <this>";

$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);

unset($trans['<']);
unset($trans['>']);
$trans['badword'] = '*******';

$new_string = strtr($string,$trans);

echo $new_string;

?>
