<?php

class HttpResponse {
	public $http_code;
	public $data;
	public $http_info = array ();
	public $url;
	public $headers;
	public $method;
	public $postfields;
	public function __construct($url, $headers, $method = 'GET', $postfields = null) {
		$this->url = $url;
		$this->headers = $headers;
		$this->method = $method;
		if (! empty ( $postfields )) {
			$this->postfields = $postfields;
		}
	}
	public function getData() {
		if (DEBUG) {
			Logger::debug ( "url:" . $this->url );
			Logger::debug ( "data:" . $this->data );
		}
		return $this->data;
	}
	public function toString() {
		$str = "url:" . ($this->url) . "<br/>";
		$str .= "data:" . ($this->data) . "<br/>";
		$str .= "http_code:" . $this->http_code . "<br/>";
		$str .= "method:" . $this->method . "<br/>";
		return $str;
	}
	/**
	 * 获取返回数据转化成Object
	 *
	 * @param boolean $throw
	 *        	是否抛出异常
	 * @throws Exception
	 * @return unknown
	 */
	public function getDataAsObject($throw = false) {
		if (DEBUG) {
			Logger::debug ( "url:" . $this->url );
			Logger::debug ( "data:" . $this->data );
		}
		$obj = json_decode ( $this->data );
		if ($throw) {
			if ($obj->status->status_code != 0) {
				throw new Exception ( '请求url:' . $url . ' errCode:' . $data->status->status_code . ' errMsg:' . $data->status->status_reason );
			}
		}
		return $obj;
	}
}
