<?php

    include_once('proteus/smarty/Smarty.class.php');
    
function smarty($file, $vars) {
    global $smarty_debugging, $error_log, $debug_log;
    $smarty=new Smarty;
    $smarty->template_dir='templates';
    $smarty->compile_dir='templates_c';
    $smarty->plugins_dir=array('my_plugins', 'plugins');
    $smarty->debug_tpl='smarty_debug.htm';
    $smarty->debugging=$smarty_debugging;
    $smarty->load_filter('pre', 'dreamweaver_template');
    $smarty->load_filter('pre', 'literal_style');
    $smarty->load_filter('pre', 'literal_script');
    $smarty->load_filter('output', 'literal_cleanup');
    $smarty->assign($vars);
    $smarty->assign('error_log', $error_log);
    $smarty->assign('debug_log', $debug_log);
    $smarty->display($file); 
}

?>