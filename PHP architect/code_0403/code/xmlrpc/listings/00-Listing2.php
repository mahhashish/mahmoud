<?php

require_once ("AppWatcher.php");

$l_oAppWatcher = new AppWatcher();

// ------------------------------------------------------------------------
// L O A D   D A T A   F O R   D A P H N E W E B
// ------------------------------------------------------------------------

$l_oAppWatcher->doAddAppDetails
(
	"daphneWeb", 
	"1.0", 
	APPWATCHER_F_USELATESTSTABLE | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/daphneweb/1.0/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.0/", 
		"http://www.example.com/mirror/daphneweb/1.0/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"daphneWeb", 
	"1.1", 
	APPWATCHER_F_USENAMEDVERSION, 
	array 
	(
		"http://www.example.com/daphneweb/1.1/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.1/", 
		"http://www.example.com/mirror/daphneweb/1.1/"
	), 
	"1.2"
);

$l_oAppWatcher->doAddAppDetails
(
	"daphneWeb", 
	"1.2", 
	APPWATCHER_F_NONE, 
	array 
	(
		"http://www.example.com/daphneweb/1.2/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.2/", 
		"http://www.example.com/mirror/daphneweb/1.2/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"daphneWeb", 
	"1.3.1", 
	APPWATCHER_F_USELATESTUNSTABLE | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/daphneweb/1.1.1/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.3.1/", 
		"http://www.example.com/mirror/daphneweb/1.3.1/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"daphneWeb", 
	"1.3.2", 
	APPWATCHER_F_NONE, 
	array 
	(
		"http://www.example.com/daphneweb/1.3.2/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.3.2/", 
		"http://www.example.com/mirror/daphneweb/1.3.2/"
	)
);

$l_oAppWatcher->setStableVersion("daphneWeb", "1.2");
$l_oAppWatcher->setUnstableVersion("daphneWeb", "1.3.2");

// ------------------------------------------------------------------------
// L O A D   D A T A   F O R   T H E L M A W E B
// ------------------------------------------------------------------------

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.0_rc1", 
	APPWATCHER_F_USELATESTSTABLE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc1/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc1/", 
		"http://www.example.com/mirror/thelmaweb/3.0_rc1/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.0_rc2", 
	APPWATCHER_F_USELATESTSTABLE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc2/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc2/", 
		"http://www.example.com/mirror/thelmaweb/3.0_rc2/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.0_rc3", 
	APPWATCHER_F_USELATESTSTABLE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc3/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.0_rc3/", 
		"http://www.example.com/mirror/thelmaweb/3.0_rc3/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.0", 
	APPWATCHER_F_USELATESTSTABLE | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.0/"
	), 
	array 
	(
		"http://www.example.com/daphneweb/1.0/", 
		"http://www.example.com/mirror/daphneweb/1.0/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.1", 
	APPWATCHER_F_USELATESTSTABLE | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.1/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.1/", 
		"http://www.example.com/mirror/thelmaweb/3.1/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.2", 
	APPWATCHER_F_USELATESTSTABLE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.2/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.2/", 
		"http://www.example.com/mirror/thelmaweb/3.2/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.3_rc1", 
	APPWATCHER_F_USENAMEDVERSION | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.3_rc1/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.3_rc1/", 
		"http://www.example.com/mirror/thelmaweb/3.3_rc1/"
	),
	"3.3_rc2"
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.3_rc2", 
	APPWATCHER_F_USELATESTSTABLE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.3_rc2/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.3_rc2/", 
		"http://www.example.com/mirror/thelmaweb/3.3_rc2/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.0", 
	APPWATCHER_F_USELATESTSTABLE | APPWATCHER_F_ISINSECURE, 
	array 
	(
		"http://www.example.com/thelmaweb/3.0/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.0/", 
		"http://www.example.com/mirror/thelmaweb/3.0/"
	)
);

$l_oAppWatcher->doAddAppDetails
(
	"thelmaWeb", 
	"3.3", 
	APPWATCHER_F_NONE,
	array 
	(
		"http://www.example.com/thelmaweb/3.3/"
	), 
	array 
	(
		"http://www.example.com/thelmaweb/3.3/", 
		"http://www.example.com/mirror/thelmaweb/3.3/"
	)
);

$l_oAppWatcher->setStableVersion("thelmaWeb", "3.3");

?>