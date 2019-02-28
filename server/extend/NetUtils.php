<?php

class NetUtils {
	/**
	 * Set up the API root URL.
	 * @ignore
	 */
	public $host = "https://www.csdn.com/";
	/**
	 * Set timeout default.
	 *
	 * @ignore
	 */
	public $timeout = 30;
	/**
	 * Set connect timeout.
	 *
	 * @ignore
	 */
	public $connecttimeout = 30;
	/**
	 * Verify SSL Cert.
	 *
	 * @ignore
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * Respons format.
	 * @ignore
	 */
	public $format = 'json';
	/**
	 * Decode returned json data.
	 * @ignore
	 */
	public $decode_json = TRUE;
	/**
	 * Contains the last HTTP headers returned.
	 * @ignore
	 */
	public $http_info;
	/**
	 * Set the useragnet.
	 * @ignore
	 */
	private $useragent = 'PHP REQUEST API';
	
	/**
	 * boundary of multipart
	 * @ignore
	 */
	public static $boundary = '';
	public static function getInstance() {
		return new NetUtils ();
	}
	/**
	 * HTTP GET 请求
	 *
	 * @param unknown $url        	
	 * @return HttpResponse
	 */
	public function get($url) {
		if (strrpos ( $url, 'http://' ) !== 0 && strrpos ( $url, 'https://' ) !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}
		return $this->http ( $url, 'GET' );
	}
	/**
	 * POST request
	 *
	 * @param string $url        	
	 * @param array $parameters        	
	 * @return HttpResponse
	 */
	public function post($url, array $parameters) {
		if (strrpos ( $url, 'http://' ) !== 0 && strrpos ( $url, 'https://' ) !== 0) {
			$url = "{$this->host}{$url}.{$this->format}";
		}
		$body = null;
		if (DEBUG) {
			Logger::debug ( "URL->" . $url );
			if (is_array ( $parameters )) {
				while ( list ( $key, $val ) = each ( $parameters ) ) {
					Logger::debug ( $key . "=" . $val );
				}
			}
		}
		$body = http_build_query ( $parameters );
		return $this->http ( $url, 'POST', $body );
	}
	/**
	 * 发送HTTP请求
	 *
	 * @param string $url        	
	 * @param string $method        	
	 * @param unknown $parameters        	
	 * @param bool $multi        	
	 * @return HttpResponse
	 */
	public function request($url, $method, $parameters, $multi = false) {
		if (strrpos ( $url, 'http://' ) !== 0 && strrpos ( $url, 'https://' ) !== 0) {
			// 支持相对路径
			$url = "{$this->host}{$url}.{$this->format}";
		}
		
		switch ($method) {
			case 'GET' :
				$url = $url . '?' . http_build_query ( $parameters );
				return $this->http ( $url, 'GET' );
			default :
				$headers = array ();
				if (! $multi && (is_array ( $parameters ) || is_object ( $parameters ))) {
					$body = http_build_query ( $parameters );
				} else {
					$body = self::build_http_query_multi ( $parameters );
					$headers [] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http ( $url, $method, $body, $headers );
		}
	}
	/**
	 * 上传文件
	 *
	 * @param unknown $url        	
	 * @param unknown $parameters        	
	 * @return Ambigous <string, mixed>
	 */
	public function uploadFile($url, $parameters) {
		$headers = array ();
		$body = self::build_http_query_multi ( $parameters );
		$headers [] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
		return self::http ( $url, 'POST', $body, $headers );
	}
	/**
	 * Make an HTTP request
	 *
	 * @return string API results
	 * @ignore
	 *
	 *
	 *
	 *
	 */
	private function http($url, $method, $postfields = NULL, $headers = array()) {
		$response = new \HttpResponse ( $url, $headers, $method, $postfields );
		$ci = curl_init ();
		/* Curl settings */
		curl_setopt ( $ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
		curl_setopt ( $ci, CURLOPT_USERAGENT, $this->useragent );
		curl_setopt ( $ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout );
		curl_setopt ( $ci, CURLOPT_TIMEOUT, $this->timeout );
		curl_setopt ( $ci, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $ci, CURLOPT_ENCODING, "UTF-8" );
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer );
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYHOST, "1" );
		curl_setopt ( $ci, CURLOPT_HEADERFUNCTION, array (
				$this,
				'getHeader' 
		) );
		curl_setopt ( $ci, CURLOPT_HEADER, FALSE );
		
		switch ($method) {
			case 'POST' :
				curl_setopt ( $ci, CURLOPT_POST, TRUE );
				if (! empty ( $postfields )) {
					curl_setopt ( $ci, CURLOPT_POSTFIELDS, $postfields );
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE' :
				curl_setopt ( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				if (! empty ( $postfields )) {
					$url = "{$url}?{$postfields}";
				}
		}
		$headers [] = "API-RemoteIP: " . $_SERVER ['REMOTE_ADDR'];
		curl_setopt ( $ci, CURLOPT_URL, $url );
		curl_setopt ( $ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ci, CURLINFO_HEADER_OUT, TRUE );
		
		$response->data = curl_exec ( $ci );
		$response->http_code = curl_getinfo ( $ci, CURLINFO_HTTP_CODE );
		$response->http_info = array_merge ( $response->http_info, curl_getinfo ( $ci ) );
		$response->url = $url;
		curl_close ( $ci );
		return $response;
	}
	
	/**
	 *
	 * @ignore
	 *
	 *
	 *
	 */
	private function build_http_query_multi($params) {
		if (! $params)
			return '';
		
		uksort ( $params, 'strcmp' );
		
		$pairs = array ();
		
		self::$boundary = $boundary = uniqid ( '===============' );
		$MPboundary = '--' . $boundary;
		$endMPboundary = $MPboundary . '--';
		$multipartbody = '';
		
		foreach ( $params as $parameter => $value ) {
			
			if (in_array ( $parameter, array (
					'media' 
			) ) && $value {0} == '@') {
				$url = ltrim ( $value, '@' );
				$content = file_get_contents ( $url );
				$array = explode ( '?', basename ( $url ) );
				$filename = $array [0];
				
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"' . "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content . "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value . "\r\n";
			}
		}
		
		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
	
	/**
	 * Get the header info to store.
	 *
	 * @return int
	 * @ignore
	 *
	 *
	 *
	 *
	 *
	 *
	 */
	function getHeader($ch, $header) {
		$i = strpos ( $header, ':' );
		if (! empty ( $i )) {
			$key = str_replace ( '-', '_', strtolower ( substr ( $header, 0, $i ) ) );
			$value = trim ( substr ( $header, $i + 2 ) );
			$this->http_header [$key] = $value;
		}
		return strlen ( $header );
	}
}