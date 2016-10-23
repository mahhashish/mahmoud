<?php

$l_oJava = new Java("org.gnqs.docXP.jsr63");
$l_szXML = implode("", file("x. php.class.docXP_args.xml"));
$l_szXSL = implode("", file("x. php.class.xsl"));

echo $l_oJava->doTransformFromStrings ($l_szXML, $l_szXSL);

?>