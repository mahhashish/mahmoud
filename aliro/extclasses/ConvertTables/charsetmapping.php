<?php

class charsetmapping {
private static $instance = __CLASS__;
private $maps = array();

public static function getInstance () {
	return is_object(self::$instance) ? self::$instance : (self::$instance = new self::$instance);
}

public function map () {
	return $this->maps;
}

private function __construct () {	
// you can add convert table for you language from http://www.haible.de/bruno/charsets/conversion-tables/
//$this->maps[(browser charset system)]=(convertCharset system);

//iso-8859
$this->maps["iso-8859-1"]="iso-8859-1";
$this->maps["iso-8859-2"]="iso-8859-2";
$this->maps["iso-8859-3"]="iso-8859-3";
$this->maps["iso-8859-4"]="iso-8859-4";
$this->maps["iso-8859-5"]="iso-8859-5";
$this->maps["iso-8859-6"]="iso-8859-6";
$this->maps["iso-8859-7"]="iso-8859-7";
$this->maps["iso-8859-8"]="iso-8859-8";
$this->maps["iso-8859-9"]="iso-8859-9";
$this->maps["iso-8859-10"]="iso-8859-10";
$this->maps["iso-8859-11"]="iso-8859-11";
$this->maps["iso-8859-12"]="iso-8859-12";
$this->maps["iso-8859-13"]="iso-8859-13";
$this->maps["iso-8859-14"]="iso-8859-14";
$this->maps["iso-8859-15"]="iso-8859-15";
$this->maps["iso-8859-16"]="iso-8859-16";

//windows
$this->maps["windows-1250"]="windows-1250";
$this->maps["windows-1251"]="windows-1251";
$this->maps["windows-1252"]="windows-1252";
$this->maps["windows-1253"]="windows-1253";
$this->maps["windows-1254"]="windows-1254";
$this->maps["windows-1255"]="windows-1255";
$this->maps["windows-1256"]="windows-1256";
$this->maps["windows-1257"]="windows-1257";
$this->maps["windows-1258"]="windows-1258";

//utf-8
$this->maps["utf-8"]="utf-8";

//Arabic
$this->maps["cp864"]="cp864"; 

//Bulgarian
$this->maps["cp1251"]="windows-1256";
$this->maps["koi8-r"]="koi8-r";

//Chinese
$this->maps["gb2312"]="gb2312";
$this->maps["gb18030"]="gb18030";
$this->maps["gbk"]="gbk";
$this->maps["big5-hkscs"]="big5hkscs";
$this->maps["big5"]="big5";
$this->maps["euc-tw"]="euc-tw";

//Georgian
$this->maps["georgian-ps"]="georgian-ps";

//Hebrew Israel
$this->maps["iso-8859-8-i"]="iso-8859-8";

//Japanese
$this->maps["euc"]="cp949";
$this->maps["sjis"]="shift_jis";
$this->maps["euc-jp"]="euc-jp";

//Korean
$this->maps["euc-kr"]="euc-kr";

//Russian
$this->maps["cp-866"]="cp866";
$this->maps["koi8-r"]="koi8-r";
$this->maps["koi8-u"]="koi8-u";

//thai
$this->maps["tis-620"]="iso-8859-11";
$this->maps["windows-874"]="cp874";
$this->maps["cp874"]="cp874";

//vietnamese
$this->maps["viscii"]="viscii";
$this->maps["tcvn"]="tcvn";

}

}