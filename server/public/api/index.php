<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
 
// 定义应用目录
define('APP_PATH', __DIR__ . '/../../application/');
define('BIND_MODULE','api');
define('MODULE_PATH', APP_PATH . DIRECTORY_SEPARATOR . BIND_MODULE . DIRECTORY_SEPARATOR);
define('CONF_PATH',	APP_PATH.'common/config/');
// 加载框架引导文件
require APP_PATH . 'common/controller/C.php';
require APP_PATH . 'common/controller/R.php';
require APP_PATH . 'common/controller/Protocol.php';
require __DIR__ . '/../../thinkphp/start.php';