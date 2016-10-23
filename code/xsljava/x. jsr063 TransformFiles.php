<?php

$l_oJava = new Java("org.gnqs.docXP.jsr63");
$l_oJava->doTransformFromFiles
(
	"x. php.class.docXP_args.xml",
	"output.html",
	"x. php.class.xsl"
);

?>