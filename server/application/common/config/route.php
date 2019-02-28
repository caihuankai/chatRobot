<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 目前无法通过route_config_file来配置
$routeFile = MODULE_PATH . 'config/route.php';
defined('MODULE_PATH') && file_exists($routeFile) && \think\Route::import(require_once $routeFile);

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
        'userId' => '\d+',
    ],
    /*'__domain__' => [ // url_domain_deploy = true
        'home' => [
            'option/:id' => 'Option/details', // 观点详情
            'userOption/:userId' => 'Option/user', // 用户观点列表
        ]
    ]*/
    //'hello/:id'     => 'index/hello' 
    //'__miss__'		=>	'index/miss'

];
