<?php /* $Id: config.inc,v 1.1 2002/12/01 03:25:39 marcot Exp $ */

/* Set this to where you config file is located, put it below your document root */
define('MD_CONFIG',	'/apache/email/public/myConfig.conf');

/* Point this to a location above your doucment root */
define('APPLOG',	'/apache/email/public/log.txt');

include_once(dirname(__FILE__) . '/php.inc');
include_once(dirname(__FILE__) . '/applog.inc');
?>
