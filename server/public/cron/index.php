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

set_time_limit(0);
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

if (PHP_SAPI != 'cli') { // cli模式
    die();
}
// 定义应用目录
define('APP_PATH', __DIR__ . '/../../application/');
define('BIND_MODULE','cron');
define('MODULE_PATH', APP_PATH . DIRECTORY_SEPARATOR . BIND_MODULE . DIRECTORY_SEPARATOR);
define('CONF_PATH',	APP_PATH.'common/config/');
define('LOG_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR);

$_SERVER['argv'] = array(
    $argv[0], $argv[1]
);

// 加载框架引导文件
require __DIR__ . '/../../thinkphp/start.php';

// php public/cron/index.php Live/updateLiveContentToUserIntro
