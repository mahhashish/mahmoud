<?php

/*************************************************

ArchieHTTP - the PHP net client
Developed by Martin Brampton as PHP5 from work by:
	Author: Monte Ohrt <monte@ispi.net>
	Copyright (c): 1999-2000 ispi, all rights reserved
	Version: 1.01
This version copyright (c): 2008 Martin Brampton
Version 1.0

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation,
 * version 2.1.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

You may contact the author of ArchieHTTP by e-mail at:
martin@black-sheep-research.com
http://www.black-sheep-research.com

The latest version of ArchieHTTP can be obtained from:
http://www.phpguru.co.uk

*************************************************/

class ArchieHTTP
{
	/**** Formerly public variables ****/

	/* user definable vars */

	public $host			=	"www.php.net";		// host name we are connecting to
	public $port			=	80;					// port we are connecting to
	public $proxy_host		=	"";					// proxy host to use
	public $proxy_port		=	"";					// proxy port to use
	public $proxy_user		=	"";					// proxy user to use
	public $proxy_pass		=	"";					// proxy password to use

	public $agent			=	"Archie v1.2.3";	// agent we masquerade as
	public $referer		=	"";					// referer info to pass
	public $cookies		=	array();			// array of cookies to pass
												// $cookies["username"]="joe";
	public $rawheaders		=	array();			// array of raw headers to send
												// $rawheaders["Content-type"]="text/html";

	public $maxredirs		=	5;					// http redirection depth maximum. 0 = disallow
	public $lastredirectaddr	=	"";				// contains address of last redirected address
	public $offsiteok		=	true;				// allows redirection off-site
	public $maxframes		=	0;					// frame content depth maximum. 0 = disallow
	public $expandlinks	=	true;				// expand links to fully qualified URLs.
												// this only applies to fetchlinks()
												// submitlinks(), and submittext()
	public $passcookies	=	true;				// pass set cookies back through redirects
												// NOTE: this currently does not respect
												// dates, domains or paths.

	public $user			=	"";					// user for http authentication
	public $pass			=	"";					// password for http authentication

	// http accept types
	public $accept			=	"image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*";

	public $results		=	"";					// where the content is put

	public $error			=	"";					// error messages sent here
	public $response_code	=	"";					// response code returned from server
	public $headers		=	array();			// headers returned from server sent here
	public $maxlength		=	500000;				// max return data length (body)
	public $read_timeout	=	0;					// timeout on read operations, in seconds
												// supported only since PHP 4 Beta 4
												// set to 0 to disallow timeouts
	public $timed_out		=	false;				// if a read operation timed out
	public $status			=	0;					// http request status

	public $temp_dir		=	"/tmp";				// temporary directory that the webserver
												// has permission to write to.
												// under Windows, this should be C:\temp

	public $curl_path		=	"/usr/local/bin/curl";
												// Archie will use cURL for fetching
												// SSL content if a full system path to
												// the cURL binary is supplied here.
												// set to false if you do not have
												// cURL installed. See http://curl.haxx.se
												// for details on installing cURL.
												// Archie does *not* use the cURL
												// library functions built into php,
												// as these functions are not stable
												// as of this Archie release.

	/**** Private variables ****/

	protected $maxlinelen	=	4096;				// max line length (headers)

	protected $httpmethod	=	"GET";				// default http request method
	protected $httpversion	=	"HTTP/1.0";			// default http request version
	protected $submit_method	=	"POST";				// default submit method
	protected $submit_type	=	"application/x-www-form-urlencoded";	// default submit type
	protected $mime_boundary	=   "";					// MIME boundary for multipart/form-data submit type
	protected $redirectaddr	=	false;				// will be set if page fetched is a redirect
	protected $redirectdepth	=	0;					// increments on an http redirect
	protected $frameurls		= 	array();			// frame src urls
	protected $framedepth	=	0;					// increments on frame depth

	protected $isproxy		=	false;				// set if using a proxy server
	protected $fp_timeout	=	30;					// timeout for socket connection

	function __construct () {
	}

/*======================================================================*\
	Function:	fetch
	Purpose:	fetch the contents of a web page
				(and possibly other protocols in the
				future like ftp, nntp, gopher, etc.)
	Input:		$URI	the location of the page to fetch
	Output:		$this->results	the output text from the fetch
\*======================================================================*/

	public function fetch($URI) {
		$this->redirectdepth = 0;
		return $this->inner_fetch($URI);
	}

	private function inner_fetch ($URI) {
		//preg_match("|^([^:]+)://([^:/]+)(:[\d]+)*(.*)|",$URI,$URI_PARTS);
		$URI_PARTS = parse_url($URI);
		if (!empty($URI_PARTS["user"])) $this->user = $URI_PARTS["user"];
		if (!empty($URI_PARTS["pass"])) $this->pass = $URI_PARTS["pass"];
		if (empty($URI_PARTS["query"])) $URI_PARTS["query"] = '';
		if (empty($URI_PARTS["path"])) $URI_PARTS["path"] = '';
		$this->host = $URI_PARTS["host"];
		if(!empty($URI_PARTS["port"])) $this->port = $URI_PARTS["port"];

		$method = strtolower($URI_PARTS["scheme"]).'_fetch';
		if (method_exists($this, $method)) return $this->$method($URI_PARTS);
		else trigger_error('Archie: Unsupported protocol '.$URI_PARTS["scheme"]);
		return false;
	}

	private function http_fetch ($URI_PARTS) {
		if (!$this->connect($fp)) return false;
		// if using proxy, send entire URI
		if($this->isproxy) $this->httprequest($URI,$fp,$URI,$this->httpmethod);
		else {
			$path = $URI_PARTS["path"].($URI_PARTS["query"] ? "?".$URI_PARTS["query"] : "");
			// no proxy, send only the path
			$this->httprequest($path, $fp, $URI, $this->httpmethod);
		}
		$this->disconnect($fp);
		if ($this->redirectaddr AND !$this->fetch_redirect()) return false;
		$this->handleFrames();
		return true;
	}

	private function https_fetch ($URI_PARTS) {
		if (!$this->curl_path OR (function_exists("is_executable") AND !is_executable($this->curl_path))) return false;
		// If using proxy, send entire URI
		if($this->isproxy) $this->httpsrequest($URI,$URI,$this->httpmethod);
		else {
			$path = $URI_PARTS["path"].($URI_PARTS["query"] ? "?".$URI_PARTS["query"] : "");
			// no proxy, send only the path
			$this->httpsrequest($path, $URI, $this->httpmethod);
		}
		if ($this->redirectaddr AND !$this->fetch_redirect()) return false;
		$this->handleFrames();
		return true;
	}

	private function fetch_redirect () {
		/* url was redirected, check if we've hit the max depth */
		if($this->maxredirs > $this->redirectdepth) {
			// only follow redirect if it's on this site, or offsiteok is true
			if($this->offsiteok OR preg_match("|^http://".preg_quote($this->host)."|i",$this->redirectaddr)) {
				/* follow the redirect */
				$this->redirectdepth++;
				$this->lastredirectaddr=$this->redirectaddr;
				return $this->inner_fetch($this->redirectaddr);
			}
		}
		return false;
	}

	private function handleFrames () {
		foreach ($this->frameurls as $frameurl) {
			if ($this->framedepth < $this->maxframes) {
				$this->inner_fetch($frameurl);
				$this->framedepth++;
			}
			else break;
		}
		$this->frameurls = array();
	}

/*======================================================================*\
	Function:	submit
	Purpose:	submit an http form
	Input:		$URI	the location to post the data
				$formvars	the formvars to use.
					format: $formvars["var"] = "val";
				$formfiles  an array of files to submit
					format: $formfiles["var"] = "/dir/filename.ext";
	Output:		$this->results	the text output from the post
\*======================================================================*/

	public function submit ($URI, $formvars="", $formfiles="") {
		$this->redirectdepth = 0;
		return $this->inner_submit($URI, $formvars, $formfiles);
	}

	private function inner_submit($URI, $formvars="", $formfiles="") {
		$postdata = $this->prepare_post_body($formvars, $formfiles);

		$URI_PARTS = parse_url($URI);
		if (!empty($URI_PARTS["user"])) $this->user = $URI_PARTS["user"];
		if (!empty($URI_PARTS["pass"])) $this->pass = $URI_PARTS["pass"];
		if (empty($URI_PARTS["query"])) $URI_PARTS["query"] = '';
		if (empty($URI_PARTS["path"])) $URI_PARTS["path"] = '';
		$this->host = $URI_PARTS["host"];
		if(!empty($URI_PARTS["port"])) $this->port = $URI_PARTS["port"];

		$method = strtolower($URI_PARTS["scheme"]).'_submit';
		if (method_exists($this, $method)) return $this->$method($URI, $URI_PARTS, $formvars, $formfiles, $postdata);
		else trigger_error('Archie: Unsupported protocol '.$URI_PARTS["scheme"]);
		return false;
	}

	private function http_submit ($URI, $URI_PARTS, $formvars, $formfiles="", $postdata) {
		if (!$this->connect($fp)) return false;
			// If using proxy, send entire URI
			if($this->isproxy) $this->httprequest($URI,$fp,$URI,$this->submit_method,$this->submit_type,$postdata);
			else {
				$path = $URI_PARTS["path"].($URI_PARTS["query"] ? "?".$URI_PARTS["query"] : "");
				// no proxy, send only the path
				$this->httprequest($path, $fp, $URI, $this->submit_method, $this->submit_type, $postdata);
			}
			$this->disconnect($fp);
			if($this->redirectaddr) $this->submit_redirect($URI_PARTS, $formvars, $formfiles);
			$this->handleFrames();
		return true;
	}

	private function https_submit ($URI, $URI_PARTS, $formvars, $formfiles="", $postdata) {
		if(!$this->curl_path OR (function_exists("is_executable") AND !is_executable($this->curl_path))) return false;
		// If using proxy, send entire URI
		if($this->isproxy) $this->httpsrequest($URI, $URI, $this->submit_method, $this->submit_type, $postdata);
		else {
			$path = $URI_PARTS["path"].($URI_PARTS["query"] ? "?".$URI_PARTS["query"] : "");
			// no proxy, send only the path
			$this->httpsrequest($path, $URI, $this->submit_method, $this->submit_type, $postdata);
		}
		if($this->redirectaddr) $this->submit_redirect($URI_PARTS, $formvars, $formfiles);
		$this->handleFrames();
		return true;
	}

	private function submit_redirect ($URI_PARTS, $formvars, $formfiles) {
		/* url was redirected, check if we've hit the max depth */
		if($this->maxredirs > $this->redirectdepth) {
			if(!preg_match("|^".$URI_PARTS["scheme"]."://|", $this->redirectaddr)) {
				$this->redirectaddr = $this->expandlinks($this->redirectaddr,$URI_PARTS["scheme"]."://".$URI_PARTS["host"]);
			}
			// only follow redirect if it's on this site, or offsiteok is true
			if($this->offsiteok OR preg_match("|^http://".preg_quote($this->host)."|i",$this->redirectaddr)) {
				/* follow the redirect */
				$this->redirectdepth++;
				$this->lastredirectaddr = $this->redirectaddr;
				if (strpos($this->redirectaddr, "?") > 0 ) {
					$this->inner_fetch($this->redirectaddr); // the redirect has changed the request method from post to get
				}
				else $this->inner_submit($this->redirectaddr, $formvars, $formfiles);
			}
		}
	}


/*======================================================================*\
	Function:	fetchlinks
	Purpose:	fetch the links from a web page
	Input:		$URI	where you are fetching from
	Output:		$this->results	an array of the URLs
\*======================================================================*/

	public function fetchlinks($URI)
	{
		if ($this->fetch($URI))
		{
			if($this->lastredirectaddr)
				$URI = $this->lastredirectaddr;
			$this->results = $this->fixResults($this->results, $URI, 'striplinks', false);
			if ($this->expandlinks) $this->results = $this->expandlinks($this->results, $URI);
			return true;
		}
		else return false;
	}

/*======================================================================*\
	Function:	fetchform
	Purpose:	fetch the form elements from a web page
	Input:		$URI	where you are fetching from
	Output:		$this->results	the resulting html form
\*======================================================================*/

	public function fetchform($URI)
	{

		if ($this->fetch($URI))
		{
			$this->results = $this->fixResults($this->results, $URI, 'stripform', false);
			return true;
		}
		else return false;
	}


/*======================================================================*\
	Function:	fetchtext
	Purpose:	fetch the text from a web page, stripping the links
	Input:		$URI	where you are fetching from
	Output:		$this->results	the text from the web page
\*======================================================================*/

	public function fetchtext($URI)
	{
		if ($this->fetch($URI)) {
			$this->results = $this->fixResults($this->results, $URI, 'striptext', false);
			return true;
		}
		else return false;
	}

/*======================================================================*\
	Function:	submitlinks
	Purpose:	grab links from a form submission
	Input:		$URI	where you are submitting from
	Output:		$this->results	an array of the links from the post
\*======================================================================*/

	public function submitlinks($URI, $formvars="", $formfiles="")
	{
		if ($this->submit($URI,$formvars, $formfiles)) {
			if ($this->lastredirectaddr) $URI = $this->lastredirectaddr;
			$this->results = $this->fixResults($this->results, $URI, 'striplinks', true);
			return true;
		}
		else return false;
	}

/*======================================================================*\
	Function:	submittext
	Purpose:	grab text from a form submission
	Input:		$URI	where you are submitting from
	Output:		$this->results	the text from the web page
\*======================================================================*/

	public function submittext($URI, $formvars = "", $formfiles = "") {
		if ($this->submit($URI,$formvars, $formfiles)) {
			if ($this->lastredirectaddr) $URI = $this->lastredirectaddr;
			$this->results = $this->fixResults($this->results, $URI, 'striptext', true);
			return true;
		}
		else return false;
	}



/*======================================================================*\
	Function:	set_submit_multipart
	Purpose:	Set the form submission content type to
				multipart/form-data
\*======================================================================*/
	public function set_submit_multipart()
	{
		$this->submit_type = "multipart/form-data";
	}


/*======================================================================*\
	Function:	set_submit_normal
	Purpose:	Set the form submission content type to
				application/x-www-form-urlencoded
\*======================================================================*/
	public function set_submit_normal()
	{
		$this->submit_type = "application/x-www-form-urlencoded";
	}




/*======================================================================*\
	Private functions
\*======================================================================*/

	private function fixResults ($results, $URI, $method, $doLinks) {
		if (is_array($results)) {
			foreach ($results as &$oneresult) {
				$oneresult = $this->$method($oneresult);
				if($doLinks AND $this->expandlinks) $oneresult = $this->expandlinks($oneresult,$URI);
			}
		}
		else {
			$results = $this->$method($results);
			if ($doLinks AND $this->expandlinks) $results = $this->expandlinks($results,$URI);
		}
		return $results;
	}

/*======================================================================*\
	Function:	striplinks
	Purpose:	strip the hyperlinks from an html document
	Input:		$document	document to strip.
	Output:		$match		an array of the links
\*======================================================================*/

	private function striplinks($document)
	{
		preg_match_all("'<\s*a\s.*?href\s*=\s*			# find <a href=
						([\"\'])?					# find single or double quote
						(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
													# quote, otherwise match up to next space
						'isx",$document,$links);


		// catenate the non-empty matches from the conditional subpattern

		while(list($key,$val) = each($links[2]))
		{
			if(!empty($val))
				$match[] = $val;
		}

		while(list($key,$val) = each($links[3]))
		{
			if(!empty($val))
				$match[] = $val;
		}

		// return the links
		return $match;
	}

/*======================================================================*\
	Function:	stripform
	Purpose:	strip the form elements from an html document
	Input:		$document	document to strip.
	Output:		$match		an array of the links
\*======================================================================*/

	private function stripform($document)
	{
		preg_match_all("'<\/?(FORM|INPUT|SELECT|TEXTAREA|(OPTION))[^<>]*>(?(2)(.*(?=<\/?(option|select)[^<>]*>[\r\n]*)|(?=[\r\n]*))|(?=[\r\n]*))'Usi",$document,$elements);

		// catenate the matches
		$match = implode("\r\n",$elements[0]);

		// return the links
		return $match;
	}



/*======================================================================*\
	Function:	striptext
	Purpose:	strip the text from an html document
	Input:		$document	document to strip.
	Output:		$text		the resulting text
\*======================================================================*/

	private function striptext($document)
	{

		// I didn't use preg eval (//e) since that is only available in PHP 4.0.
		// so, list your entities one by one here. I included some of the
		// more common ones.

		$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
						"'<[\/\!]*?[^<>]*?>'si",			// strip out html tags
						"'([\r\n])[\s]+'",					// strip out white space
						"'&(quot|#34|#034|#x22);'i",		// replace html entities
						"'&(amp|#38|#038|#x26);'i",			// added hexadecimal values
						"'&(lt|#60|#060|#x3c);'i",
						"'&(gt|#62|#062|#x3e);'i",
						"'&(nbsp|#160|#xa0);'i",
						"'&(iexcl|#161);'i",
						"'&(cent|#162);'i",
						"'&(pound|#163);'i",
						"'&(copy|#169);'i",
						"'&(reg|#174);'i",
						"'&(deg|#176);'i",
						"'&(#39|#039|#x27);'",
						"'&(euro|#8364);'i",				// europe
						"'&a(uml|UML);'",					// german
						"'&o(uml|UML);'",
						"'&u(uml|UML);'",
						"'&A(uml|UML);'",
						"'&O(uml|UML);'",
						"'&U(uml|UML);'",
						"'&szlig;'i",
						);
		$replace = array(	"",
							"",
							"\\1",
							"\"",
							"&",
							"<",
							">",
							" ",
							chr(161),
							chr(162),
							chr(163),
							chr(169),
							chr(174),
							chr(176),
							chr(39),
							chr(128),
							"�",
							"�",
							"�",
							"�",
							"�",
							"�",
							"�",
						);

		$text = preg_replace($search,$replace,$document);

		return $text;
	}

/*======================================================================*\
	Function:	expandlinks
	Purpose:	expand each link into a fully qualified URL
	Input:		$links			the links to qualify
				$URI			the full URI to get the base from
	Output:		$expandedLinks	the expanded links
\*======================================================================*/

	private function expandlinks($links,$URI)
	{

		preg_match("/^[^\?]+/",$URI,$match);

		$match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|","",$match[0]);
		$match = preg_replace("|/$|","",$match);
		$match_part = parse_url($match);
		$match_root =
		$match_part["scheme"]."://".$match_part["host"];

		$search = array( 	"|^http://".preg_quote($this->host)."|i",
							"|^(\/)|i",
							"|^(?!http://)(?!mailto:)|i",
							"|/\./|",
							"|/[^\/]+/\.\./|"
						);

		$replace = array(	"",
							$match_root."/",
							$match."/",
							"/",
							"/"
						);

		$expandedLinks = preg_replace($search,$replace,$links);

		return $expandedLinks;
	}

/*======================================================================*\
	Function:	httprequest
	Purpose:	go get the http data from the server
	Input:		$url		the url to fetch
				$fp			the current open file pointer
				$URI		the full URI
				$body		body contents to send if any (POST)
	Output:
\*======================================================================*/

	private function httprequest ($url,$fp,$URI,$http_method,$content_type="",$body="") 	{
		if ($this->passcookies AND $this->redirectaddr) $this->setcookies();
		$headers = array();

		$URI_PARTS = parse_url($URI);
		if (empty($url)) $url = '/';
		$headers[] = $http_method.' '.$url.' '.$this->httpversion;
		if (!empty($this->agent)) $headers[] = "User-Agent: ".$this->agent;
		if (!empty($this->host) AND !isset($this->rawheaders['Host'])) {
			$headers[] = 'Host: '.$this->host.(empty($this->port) ? '' : ':'.$this->port);
		}
		if (!empty($this->accept)) $headers[] = "Accept: ".$this->accept;
		if (!empty($this->referer)) $headers[] = "Referer: ".$this->referer;
		if (!empty($this->cookies)) {
			if(!is_array($this->cookies)) $this->cookies = (array)$this->cookies;
			if (count($this->cookies)) {
				$cookie_headers = array();
				foreach ($this->cookies as $cookieKey => $cookieVal) {
					$cookie_headers[] = $cookieKey.'='.urlencode($cookieVal);
				}
				$headers[] =  'Cookie: '.implode('; ', $cookie_headers);
			}
		}
		if (!empty($this->rawheaders)) {
			foreach ((array) $this->rawheaders as $headerKey=>$headerVal) {
				$headers[] = $headerKey.': '.$headerVal;
			}
		}
		if (!empty($content_type)) {
			$headers[] = "Content-type: $content_type".($content_type == "multipart/form-data" ? "; boundary=".$this->mime_boundary : '');
		}
		if (!empty($body)) $headers[] = "Content-length: ".strlen($body);
		if (!empty($this->user) OR !empty($this->pass))	{
			$headers[] = "Authorization: Basic ".base64_encode($this->user.":".$this->pass);
		}

		//add proxy auth headers
		if(!empty($this->proxy_user)) {
			$headers[] = 'Proxy-Authorization: ' . 'Basic ' . base64_encode($this->proxy_user . ':' . $this->proxy_pass);
		}

		// set the read timeout if needed
		if ($this->read_timeout > 0) socket_set_timeout($fp, $this->read_timeout);
		$this->timed_out = false;

		$message = implode("\r\n", $headers)."\r\n\r\n".$body;
		fwrite($fp,$message,strlen($message));

		$this->redirectaddr = false;
		$this->headers = array();
		while($currentHeader = fgets($fp,$this->maxlinelen)) {
			if ($this->read_timeout > 0 && $this->check_timeout($fp)) {
				$this->status = -100;
				return false;
			}

			if ("\r\n" == $currentHeader) break;

			// if a header begins with Location: or URI:, set the redirect
			if (preg_match("/^(Location:|URI:)/i",$currentHeader)) {
				// get URL portion of the redirect
				preg_match("/^(Location:|URI:)[ ]+(.*)/i",chop($currentHeader),$matches);
				// look for :// in the Location header to see if hostname is included
				if (!preg_match("|\:\/\/|",$matches[2])) {
					// no host in the path, so prepend
					$this->redirectaddr = $URI_PARTS["scheme"]."://".$this->host.":".$this->port;
					// eliminate double slash
					if(!preg_match("|^/|",$matches[2])) $this->redirectaddr .= "/".$matches[2];
					else $this->redirectaddr .= $matches[2];
				}
				else $this->redirectaddr = $matches[2];
			}

			if(preg_match("|^HTTP/|",$currentHeader)) {
                if(preg_match("|^HTTP/[^\s]*\s(.*?)\s|",$currentHeader, $status)) $this->status= $status[1];
				$this->response_code = $currentHeader;
			}

			$this->headers[] = $currentHeader;
		}

		$results = '';
		while ($_data = fread($fp, $this->maxlength)) $results .= $_data;

		if ($this->read_timeout > 0 AND $this->check_timeout($fp))
		{
			$this->status=-100;
			return false;
		}

		// check if there is a a redirect meta tag

		if (preg_match("'<meta[\s]*http-equiv[^>]*?content[\s]*=[\s]*[\"\']?\d+;[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i",$results,$match)) {
			$this->redirectaddr = $this->expandlinks($match[1],$URI);
		}

		// have we hit our frame depth and is there frame src to fetch?
		if (($this->framedepth < $this->maxframes) && preg_match_all("'<frame\s+.*src[\s]*=[\'\"]?([^\'\"\>]+)'i",$results,$match)) {
			$this->results[] = $results;
			for ($x=0; $x<count($match[1]); $x++) {
				$this->frameurls[] = $this->expandlinks($match[1][$x],$URI_PARTS["scheme"]."://".$this->host);
			}
		}
		// have we already fetched framed content?
		elseif (is_array($this->results)) $this->results[] = $results;
		// no framed content
		else $this->results = $results;

		return true;
	}

/*======================================================================*\
	Function:	httpsrequest
	Purpose:	go get the https data from the server using curl
	Input:		$url		the url to fetch
				$URI		the full URI
				$body		body contents to send if any (POST)
	Output:
\*======================================================================*/

	private function httpsrequest ($url,$URI,$http_method,$content_type="",$body="") {
		if ($this->passcookies AND $this->redirectaddr) $this->setcookies();
		$headers = array();

		$URI_PARTS = parse_url($URI);
		if (empty($url)) $url = "/";
		// GET ... header not needed for curl
		//$headers[] = $http_method." ".$url." ".$this->httpversion;
		if (!empty($this->agent)) $headers[] = "User-Agent: ".$this->agent;
		if (!empty($this->host)) {
			if (!empty($this->port)) $headers[] = "Host: ".$this->host.":".$this->port;
			else $headers[] = "Host: ".$this->host;
		}
		if (!empty($this->accept)) $headers[] = "Accept: ".$this->accept;
		if (!empty($this->referer)) $headers[] = "Referer: ".$this->referer;
		if (!empty($this->cookies)) {
			if (!is_array($this->cookies)) $this->cookies = (array)$this->cookies;
			if (count($this->cookies)) {
				$cookie_headers = array();
				foreach ($this->cookies as $cookieKey => $cookieVal) {
					$cookie_headers[] = $cookieKey.'='.urlencode($cookieVal);
				}
				$headers[] =  'Cookie: '.implode('; ', $cookie_headers);
			}
		}
		if (!empty($this->rawheaders)) {
			foreach ((array) $this->rawheaders as $headerKey=>$headerVal) {
				$headers[] = $headerKey.': '.$headerVal;
			}
		}
		if (!empty($content_type)) {
			if ($content_type == "multipart/form-data") $headers[] = "Content-type: $content_type; boundary=".$this->mime_boundary;
			else $headers[] = "Content-type: $content_type";
		}
		if (!empty($body)) $headers[] = "Content-length: ".strlen($body);
		if (!empty($this->user) || !empty($this->pass)) $headers[] = "Authorization: BASIC ".base64_encode($this->user.":".$this->pass);

		foreach ($headers as $curr_header) {
			$safer_header = strtr( $curr_header, "\"", " " );
			$cmdline_params .= " -H \"".$safer_header."\"";
		}

		if (!empty($body)) $cmdline_params .= " -d \"$body\"";

		if ($this->read_timeout > 0) $cmdline_params .= " -m ".$this->read_timeout;

		$headerfile = tempnam($temp_dir, "sno");

		$safer_URI = strtr( $URI, "\"", " " ); // strip quotes from the URI to avoid shell access
		exec($this->curl_path." -D \"$headerfile\"".$cmdline_params." \"".$safer_URI."\"",$results,$return);

		if ($return) {
			$this->error = "Error: cURL could not retrieve the document, error $return.";
			return false;
		}

		$results = implode("\r\n",$results);

		$result_headers = file("$headerfile");

		$this->redirectaddr = false;
		$this->headers = array();

		foreach ($result_headers as $currentHeader) {
			// if a header begins with Location: or URI:, set the redirect
			if (preg_match("/^(Location: |URI: )/i",$currentHeader)) {
				// get URL portion of the redirect
				preg_match("/^(Location: |URI:)\s+(.*)/",chop($currentHeader),$matches);
				// look for :// in the Location header to see if hostname is included
				if (!preg_match("|\:\/\/|",$matches[2])) {
					// no host in the path, so prepend
					$this->redirectaddr = $URI_PARTS["scheme"]."://".$this->host.":".$this->port;
					// eliminate double slash
					if (!preg_match("|^/|",$matches[2])) $this->redirectaddr .= "/".$matches[2];
					else $this->redirectaddr .= $matches[2];
				}
				else $this->redirectaddr = $matches[2];
			}
			if (preg_match("|^HTTP/|",$currentHeader)) $this->response_code = $currentHeader;
			$this->headers[] = $currentHeader;
		}

		// check if there is a a redirect meta tag
		if (preg_match("'<meta[\s]*http-equiv[^>]*?content[\s]*=[\s]*[\"\']?\d+;[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i",$results,$match)) {
			$this->redirectaddr = $this->expandlinks($match[1],$URI);
		}

		// have we hit our frame depth and is there frame src to fetch?
		if (($this->framedepth < $this->maxframes) && preg_match_all("'<frame\s+.*src[\s]*=[\'\"]?([^\'\"\>]+)'i",$results,$match)) {
			$this->results[] = $results;
			for ($x=0; $x<count($match[1]); $x++)
			foreach ($match[1] as $onematch) {
				$this->frameurls[] = $this->expandlinks($onematch,$URI_PARTS["scheme"]."://".$this->host);
			}
		}
		// have we already fetched framed content?
		elseif (is_array($this->results)) $this->results[] = $results;
		// no framed content
		else $this->results = $results;

		unlink("$headerfile");

		return true;
	}

/*======================================================================*\
	Function:	setcookies()
	Purpose:	set cookies for a redirection
\*======================================================================*/

	private function setcookies()
	{
		for($x=0; $x<count($this->headers); $x++)
		{
			if (preg_match('/^set-cookie:[\s]+([^=]+)=([^;]+)/i', $this->headers[$x],$match)) {
				$this->cookies[$match[1]] = urldecode($match[2]);
			}
		}
	}


/*======================================================================*\
	Function:	check_timeout
	Purpose:	checks whether timeout has occurred
	Input:		$fp	file pointer
\*======================================================================*/

	private function check_timeout($fp)
	{
		if ($this->read_timeout > 0) {
			$fp_status = socket_get_status($fp);
			if ($fp_status["timed_out"]) {
				$this->timed_out = true;
				return true;
			}
		}
		return false;
	}

/*======================================================================*\
	Function:	connect
	Purpose:	make a socket connection
	Input:		$fp	file pointer
\*======================================================================*/

	private function connect(&$fp)
	{
		if (!empty($this->proxy_host) AND !empty($this->proxy_port)) {
			$this->isproxy = true;
			$host = $this->proxy_host;
			$port = $this->proxy_port;
		}
		else {
			$host = $this->host;
			$port = $this->port;
		}
		$this->status = 0;
		if ($fp = fsockopen(
					$host,
					$port,
					$errno,
					$errstr,
					$this->fp_timeout
					))
		{
			// socket connection succeeded

			return true;
		}
		else
		{
			// socket connection failed
			$this->status = $errno;
			switch($errno)
			{
				case -3:
					$this->error="socket creation failed (-3)";
				case -4:
					$this->error="dns lookup failure (-4)";
				case -5:
					$this->error="connection refused or timed out (-5)";
				default:
					$this->error="connection failed (".$errno.")";
			}
			return false;
		}
	}
/*======================================================================*\
	Function:	disconnect
	Purpose:	disconnect a socket connection
	Input:		$fp	file pointer
\*======================================================================*/

	private function disconnect($fp)
	{
		return(fclose($fp));
	}


/*======================================================================*\
	Function:	prepare_post_body
	Purpose:	Prepare post body according to encoding type
	Input:		$formvars  - form variables
				$formfiles - form upload files
	Output:		post body
\*======================================================================*/

	private function prepare_post_body ($formvars, $formfiles)
	{
		settype($formvars, "array");
		settype($formfiles, "array");
		$postdata = '';

		if (count($formvars) == 0 AND count($formfiles) == 0) return;

		switch ($this->submit_type) {
			case "application/x-www-form-urlencoded":
				foreach ($formvars as $key=>$val) {
					if (is_array($val) OR is_object($val)) {
						foreach ($val as $cur_key=>$cur_val) {
							$postdata .= urlencode($key)."[]=".urlencode($cur_val)."&";
						}
					} else
						$postdata .= urlencode($key)."=".urlencode($val)."&";
				}
				break;

			case "multipart/form-data":
				$this->mime_boundary = "Archie".md5(uniqid(microtime()));

				foreach ($formvars as $key=>$val) {
					if (is_array($val) OR is_object($val)) {
						foreach ($val as $cur_key=>$cur_val) {
							$postdata .= "--".$this->mime_boundary."\r\n";
							$postdata .= "Content-Disposition: form-data; name=\"$key\[\]\"\r\n\r\n";
							$postdata .= "$cur_val\r\n";
						}
					} else {
						$postdata .= "--".$this->mime_boundary."\r\n";
						$postdata .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
						$postdata .= "$val\r\n";
					}
				}

				foreach ($formfiles as $field_name=>$file_names) {
					settype($file_names, "array");
					foreach ($file_names as $file_name) {
						if (!is_readable($file_name)) continue;
						$fp = fopen($file_name, "r");
						$file_content = fread($fp, filesize($file_name));
						fclose($fp);
						$base_name = basename($file_name);

						$postdata .= "--".$this->mime_boundary."\r\n";
						$postdata .= "Content-Disposition: form-data; name=\"$field_name\"; filename=\"$base_name\"\r\n\r\n";
						$postdata .= "$file_content\r\n";
					}
				}
				$postdata .= "--".$this->mime_boundary."--\r\n";
				break;
		}

		return $postdata;
	}
}