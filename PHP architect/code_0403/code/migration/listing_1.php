<?php

/*
* This code implements the searching of a Lucene index
* Using the XML-RPC bundled with PHP 4.3.0, we connect
* to the XML-RPC Web server (which is part of Apache's
* Web Services project: http://ws.apache.org).
*
* We establish a connection to the Web Service, and provide
* the name of the index, the query and the search type
* and are returned with a WDDX packet that we stuff into a
* session variable
*/
$host = "192.168.1.7";
$port = "8888";
$uri = "/search_engine";
$method = "search_engine.search";

$SearchIndex = "/usr/local/diysearch/search_engine/index/links_index";
$args = array($SearchIndex,"OR",$q);
$request = compact('host','port','uri','method','args');
$result = xu_rpc_http_concise($request);
$sess_qrows = $result;
?>