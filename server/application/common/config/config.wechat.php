<?php



return [
    
    // 自定义配置
    'adminLoginExpire' => 60 * 5, // 后台登录时间
    
    
    
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => true,
    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => 'wx2b76dd0059adc7a4',         // AppID
    'secret'  => 'e3a5aa57b9c621a7a38522f57a3818cf',     // AppSecret
    'token'   => 'weixin',          // Token
    'aes_key' => 'TKgf4xaa7IEJwxqrhgB6EZEAegKIdCtde5QpDH58tHa',                    // EncodingAESKey，安全模式下请一定要填写！！！
    
    
    'open-web' => [
        'app_id'  => 'wxd9fe5cf00b6871d2',         // AppID
        'secret'  => '9ba57476acd53be20de873e6b092a40d',     // AppSecret
    ],
    
    'open-app' => [
        'app_id'  => 'wxb895b8067f425478',         // AppID
        'secret'  => '49c418dd3f53b01dc1b454a2cd5d37ae',     // AppSecret
    ],
		
	'open-web-jiahua' => [
			'app_id'  => 'wxd9fe5cf00b6871d2',         // AppID
			'secret'  => '9ba57476acd53be20de873e6b092a40d',     // AppSecret
	],
    
    
    /**
     * 日志配置
     *
     * level: 日志级别, 可选为：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => LOG_PATH . 'weChat/easyWeChat.log',
    ],
    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_base'],
        'callback' => '/User/registerUser',
    ],
    /**
     * 微信支付
     */
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
        // 'device_info'     => '013467007045764',
        // 'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
    ],
    /**
     * Guzzle 全局设置
     *
     * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
     */
    'guzzle' => [
        'timeout' => 3.0, // 超时时间（秒）
        //'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
    ],
    
    
    
    'jsSDK' => [ // https://easywechat.org/zh-cn/docs/js.html
        'debug' => true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        'apps' => [
            "onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone",
            "startRecord","stopRecord","onVoiceRecordEnd","playVoice","pauseVoice","stopVoice","onVoicePlayEnd",
            "uploadVoice","downloadVoice","chooseImage","previewImage","uploadImage","downloadImage","translateVoice",
            "getNetworkType","openLocation","getLocation","hideOptionMenu","showOptionMenu","hideMenuItems","showMenuItems",
            "hideAllNonBaseMenuItem","showAllNonBaseMenuItem","closeWindow","scanQRCode","chooseWXPay","openProductSpecificView",
            "addCard","chooseCard","openCard",
        ], // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        'beta' => false,
        'json' => true, // 当 $json 为 false 时返回数组，你可以直接使用到网页中
    ],
    
    
];