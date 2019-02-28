<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/*
 * socket收发数据
 * @host(string) socket服务器IP
 * @post(int) 端口
 * @str(string) 要发送的数据
 * @back 1|0 socket端是否有数据返回
 * 返回true|false|服务端数据
 */
function send_socket_msg($host, $port, $str, $back = 0) {
	$socket = socket_create ( AF_INET, SOCK_STREAM, 0 );
	if ($socket < 0)
		return false;
	$result = @socket_connect ( $socket, $host, $port );
	if ($result == false)
		return false;
	socket_write ( $socket, $str, strlen ( $str ) );

	if ($back != 0) {
		$input = socket_read ( $socket, 1024 );
		socket_close ( $socket );
		return $input;
	} else {
		socket_close ( $socket );
		return true;
	}
}










