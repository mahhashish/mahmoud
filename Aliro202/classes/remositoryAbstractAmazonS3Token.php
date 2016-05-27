<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-10 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

if (basename(@$_SERVER['REQUEST_URI']) == basename(__FILE__)) die ('This software is for use within a larger system');

final class S3Requestor extends eacHttpRequest {
	private $bucket = '';
	private $uri = '';
	private $resource = '';
	private $parameters = array();
	private $amzHeaders = array();
	private $headers = array(
		'Host' => '',
		'Date' => '',
		'Content-MD5' => '',
		'Content-type' => ''
	);
	private $fp = false;
	private $size = 0;
	private $data = false;
	private $response = null;
	private $useSSL = true;
	private $secretkey = '';
	private $accesskey = '';

	public function __construct ($defaultHost = 's3.amazonaws.com') {
		plgSystemJaliro::setAmazonS3Params($this->accesskey, $this->secretkey, $this->bucket);

		//$this->accesskey = '11T08SQ3DJ6M08W0JS82';
		//$this->secretkey = 'b+0lqHfNhLaQzdgZ1T4LYaWOqvOXmm9al2CTYqlG';
		//$this->bucket = 'remository.martin.brampton';

		$this->bucket = strtolower($this->bucket);
		$this->headers['Host'] = $this->bucket ? $this->bucket.'.'.$defaultHost : $defaultHost;
		$this->headers['Date'] = gmdate('D, d M Y H:i:s T');
		$this->response = new stdClass;
		$this->response->error = false;
		parent::__construct();
	}

	public function head ($objectname) {
		return $this->plugin->head($this->getURL('HEAD', $objectname));
	}

	public function delete ($objectname) {
		return $this->plugin->delete($this->getURL('DELETE', $objectname));
	}

	public function copy ($objectname, $toname) {
		$this->setAmzHeader('x-amz-copy-source', '/'.$this->bucket.'/'.$objectname);
		return $this->plugin->put($this->getURL('PUT', $toname));
	}

	public function put ($objectname, $data) {
		return $this->plugin->put($this->getURL('PUT', $objectname), $data);
	}

	public function get ($objectname='') {
		return $this->plugin->get($this->getURL('GET', $objectname));
	}

	public function setParameter ($key, $value) {
		$this->parameters[$key] = $value;
	}

	public function setHeader ($key, $value) {
		$this->headers[$key] = $value;
	}

	public function getHeader ($key) {
		return isset($this->headers[$key]) ? $this->headers[$key] : '';
	}

	public function setAmzHeader($key, $value) {
		$this->amzHeaders[$key] = $value;
	}

	public function getAuthenticatedURL($uri, $lifetime, $hostBucket = false, $https = false) {
		$expires = time() + $lifetime;
		$uri = str_replace('%2F', '/', rawurlencode($uri)); // URI should be encoded (thanks Sean O'Dea)
		return sprintf(($https ? 'https' : 'http').'://%s/%s?AWSAccessKeyId=%s&Expires=%u&Signature=%s',
		$hostBucket ? $this->bucket : $this->bucket.'.s3.amazonaws.com', $uri, $this->accesskey, $expires,
		urlencode($this->getHash("GET\n\n\n{$expires}\n/{$this->bucket}/{$uri}")));
	}

	public function notSSL () {
		$this->useSSL = false;
	}

	private function getURL ($verb, $uri) {
		$this->setURIandResource($uri);
		$this->setHeaders($verb);
		return (($this->useSSL AND extension_loaded('openssl')) ? 'https://':'http://').$this->getHeader('Host').$this->uri;
	}

	private function setURIandResource ($uri) {
		$this->uri = $uri ? '/'.str_replace('%2F', '/', rawurlencode($uri)) : '/';
		$this->resource = $this->bucket ? '/'.$this->bucket.$this->uri : $this->uri;
		if (count($this->parameters)) {
			foreach ($this->parameters as $var => $value) {
				$querypart[] = $value ? $var.'='.str_replace('%2F', '/', rawurlencode($value)) : $var;
			}
			$query = (substr($this->uri, -1) !== '?' ? '?' : '&').implode('&', $querypart);
			$this->uri .= $query;
			if (count(array_intersect(array_keys($this->parameters), array('acl', 'location', 'torrent', 'logging')))) {
				$this->resource .= $query;
			}
		}
	}

	private function setHeaders ($verb) {
		foreach ($this->amzHeaders as $header => $value) if (strlen($value) > 0) $this->plugin->header($header.': '.$value);
		foreach ($this->headers as $header => $value) if (strlen($value) > 0) $this->plugin->header($header.': '.$value);
		$this->plugin->header($this->getAuthHeader($verb));
	}

	private function getAMZ () {
		foreach ($this->amzHeaders as $header => $value) {
			if ($value) $amz[] = strtolower($header).':'.$value;
		}
		if (empty($amz)) return '';
		// AMZ headers must be sorted
		sort($amz);
		return "\n".implode("\n", $amz);
	}

	private function getAuthHeader ($verb) {
		return 'Authorization: ' . $this->getSignature(
			$this->getHeader('Host') == 'cloudfront.amazonaws.com' ? $this->getHeader('Date') :
			$verb."\n".$this->getHeader('Content-MD5')."\n".
			$this->getHeader('Content-Type')."\n".$this->getHeader('Date').$this->getAMZ()."\n".$this->resource);
	}

	private function getSignature ($string) {
		return 'AWS '.$this->accesskey.':'.$this->getHash($string);
	}

	private function getHash ($string) {
		return base64_encode(extension_loaded('hash') ?
		hash_hmac('sha1', $string, $this->secretkey, true) : pack('H*', sha1(
		(str_pad($this->secretkey, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
		pack('H*', sha1((str_pad($this->secretkey, 64, chr(0x00)) ^
		(str_repeat(chr(0x36), 64))) . $string)))));
	}
}

abstract class remositoryAbstractAmazonS3Token extends remositoryFileToken {

// To do: Download needs to refer directly to Amazon

	protected static $namelist = array();

	protected $driver_type = 'AmazonS3';
	protected $driver_name = '';
	protected $overwriteable = false;
	protected $downloadable = true;
	protected $copiable = true;
	protected $metadata = array();
	protected $readoffset = 0;
	protected $fmode = '';
	protected $tempfile = null;
	protected $s3name = '';
	protected $log_before = true;

	public function __construct ($id, $identifier, $insertID=false) {
		$this->driver_name = $this->T_('Remository Amazon S3 File Driver');
		$this->validatePath(dirname($identifier));
		if ('/' == $identifier[0]) $identifier = substr($identifier,1);
		parent::__construct($id, $identifier, $insertID);
		$this->s3name = dirname($this->filename).($insertID ? '/fileid'.str_pad($id,9,'0',STR_PAD_LEFT) : '').'/'.cmsapiInterface::nameForURL(basename($this->filename));
	}

	protected function validatePath ($dirname) {
		foreach (explode('/', $dirname) as $direlement) {
			if ($direlement != cmsapiInterface::nameForURL($direlement)) throw new remositoryFileError(sprintf($this->T_('Amazon S3 File: Pseudo directory element %s is not valid'), $direlement));
		}
	}

	protected function open ($mode) {
		$this->fmode = 'w' == $mode ? 'wb' : 'rb';
		if ($this->filename AND $this->id) {
			if ('rb' == $this->fmode) {
				$this->exists(true);
			}
			if ('wb' == $this->fmode) {
				if ($this->exists()) throw new remositoryFileError(sprintf($this->T_('Amazon S3 File: Opening for write %s but already exists'), $this->s3name));
				$this->tempfile = fopen('php://temp', 'rw');
				if (!is_resource($this->tempfile)) throw new remositoryFileError(sprintf($this->T_('Amazon S3 File: Open temporary file for %s failed'), $this->s3name));
			}
		}
		else throw new remositoryFileError ($this->T_('Amazon S3 File: Attempted file open but no file name or no ID'));
	}

	protected function setReadOffset ($offset) {
		$this->readoffset = $offset;
	}

	protected function openForRead () {
		if (!$this->fmode) $this->open('r');
		if ('rb' != $this->fmode) throw new remositoryFileError($this->T_('Amazon S3 File: Attempt to read file opened for write'));
	}

	protected function read () {
		$this->openForRead();
		if ($this->readoffset < $this->getSize()) {
			$request = new S3Requestor();
			$upper = min($this->getSize()-1, $this->readoffset + _REMOSITORY_FILE_BLOCK_SIZE - 1);
			$request->setHeader('Range', sprintf('%d-%d', $this->readoffset, $upper));
			$data = $request->get($this->s3name);
			$this->readoffset += strlen($data);
			return $data;
		}
		throw new remositoryFileEnd ();
	}

	protected function write ($data) {
		if (!($this->fmode)) $this->open('w');
		if ('wb' != $this->fmode) throw new remositoryFileError($this->T_('Amazon S3 File: Attempt to write file opened for read'));
		fwrite($this->tempfile, $data);
	}

	public function delete ($ifPossible=false) {
		if ($this->id AND $this->filename) {
			$request = new S3Requestor();
			$request->delete($this->s3name);
			$response = $request->getHeaders();
			return '204 No Content' == @$response['HTTP/1.1'];
		}
		if ($ifPossible) return;
		throw new remositoryFileError($this->T_('Amazon S3 File: Attempt to delete file with missing file path or ID'));
	}

	public function copy ($totoken) {
		if ($totoken instanceof self) {
			$totoken->md5hash = $this->md5hash;
			$request = new S3Requestor();
			$request->copy($this->s3name, $totoken->s3name);
			$response = $request->getHeaders();
			return '200 OK' == @$response['HTTP/1.1'];
		}
		return $this->copyAcrossType($totoken);
	}

	public function move ($totoken) {
		if ($totoken instanceof self) {
			$totoken->md5hash = $this->md5hash;
			return ($totoken->s3name != $this->s3name) ? ($this->copy($totoken) AND $this->delete()) : true;
		}
		else {
			return ($this->copyAcrossType($totoken)) ? $this->delete() : false;
		}
	}

	public function download ($offset=0) {
		$request = new S3Requestor();
		// PHP will send a status code of 302 automatically
		header('Location: '.$request->getAuthenticatedURL($this->s3name, 60));
		exit;
	}

	public function exists ($errorOnFail=false) {
		$this->loadList();
		if (in_array($this->s3name, self::$namelist)) return true;
		if ($errorOnFail) $this->nonExistenceError ();
		return false;
	}
	
	protected function loadList () {
		if (isset(self::$namelist[0]) AND $this->s3name >= self::$namelist[0] AND $this->s3name <= end(self::$namelist)) return;
		self::$namelist = array();
		$marker = dirname($this->s3name);
		$prefix = dirname($marker);
		$request = new S3Requestor();
		$request->setParameter('prefix', $prefix);
		$request->setParameter('marker', $marker);
		$info = $request->get();
		$response = $request->getHeaders();
		$code = @$response['HTTP/1.1'];
		if ('403' == substr($code,0,3)) throw new remositoryFileError ($this->T_('Amazon S3 File: Access forbidden, please check credentials'));
		if ('200 OK' != $code) throw new remositoryFileError ($this->T_('Amazon S3 File: Access to file list failed'));
		$xmlinfo = simplexml_load_string($info);
		foreach ($xmlinfo->Contents as $item) self::$namelist[] = (string) $item->Key;
	}

	protected function equal ($s3token) {
		return $this->s3name == $s3token->s3name;
	}

	public function getDate () {
		return $this->getMetaDataItem('Last-Modified');
	}

	public function getSize () {
		return (int) $this->getMetaDataItem('Content-Length', 0);
	}

	protected function getMetaDataItem ($name, $default=null) {
		if (empty($this->metadata)) {
			$request = new S3Requestor();
			$request->head($this->s3name);
			$this->metadata = $request->getHeaders();
		}
		return isset($this->metadata[$name]) ? $this->metadata[$name] : $default;
	}

	protected function endOfFile () {
		if (is_resource($this->tempfile)) {
			$request = new S3Requestor();
			$request->setHeader('Content-Length', ftell($this->tempfile));
			if ($this->md5hash) $request->setHeader('Content-MD5', base64_encode(pack("H*",$this->md5hash)));
			$request->put($this->s3name, $this->tempfile);
			$response = $request->getHeaders();
			return '200 OK' == @$response['HTTP/1.1'];
		}
		else return true;
	}

}