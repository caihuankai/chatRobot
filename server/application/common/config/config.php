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

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace' => 'app',
    // 应用调试模式
    'app_debug' => true,
    // 应用Trace
    'app_trace' => false,
    // 应用模式状态
    'app_status' => '',
    // 是否支持多模块
    'app_multi_module' => true,
    // 入口自动绑定模块
    'auto_bind_module' => false,
    // 注册的根命名空间
    'root_namespace' => [],
    // 扩展配置文件
    //'extra_config_list'      => BIND_MODULE == 'index' ? ['database', 'validate', 'index.config'] : ['database', 'validate'],
    // 扩展函数文件
    'extra_file_list' => [
        APP_PATH . DS . 'common' . DS . 'helper' . EXT,
        THINK_PATH . 'helper' . EXT
    ],
    // 默认输出类型
    'default_return_type' => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler' => 'callback',
    // 默认时区
    'default_timezone' => 'PRC',
    // 是否开启多语言
    'lang_switch_on' => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => '',
    // 默认语言
    'default_lang' => 'zh-cn',
    // 应用类库后缀
    'class_suffix' => false,
    // 控制器类后缀
    'controller_suffix' => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module' => 'index',
    // 禁止访问模块
    'deny_module_list' => ['common'],
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 默认验证器
    'default_validate' => \ThinkPHP\Validate::class,
    // 默认的空控制器名
    'empty_controller' => 'Error',
    // 操作方法后缀
    'action_suffix' => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态后缀
    //'url_html_suffix'        => 'html',
    'url_html_suffix' => '',
    // URL普通方式参数 用于自动生成
    'url_common_param' => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否开启路由
    'url_route_on' => true,
    // 路由配置文件（支持配置多个）
    'route_config_file' => ['route'],
    // 是否强制使用路由
    'url_route_must' => false,
    // 域名部署
    'url_domain_deploy' => false,
    // 域名根，如thinkphp.cn
    'url_domain_root' => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert' => true,
    // 默认的访问控制器层
    'url_controller_layer' => 'controller',
    // 表单请求类型伪装变量
    'var_method' => '_method',

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => '',
        // 模板后缀
        //'view_suffix'  => 'html',
        'view_suffix' => 'php',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '<{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}>',
        // 标签库标签开始标记
        'taglib_begin' => '<{',
        // 标签库标签结束标记
        'taglib_end' => '}>',
        'taglib_build_in' => 'cx,\SysCx',

        'layout_on' => true,
        'layout_name' => 'layout',
        'layout_item' => '{__CONTENT__}'
    ],

    // 视图输出字符串内容替换
    'view_replace_str' => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => APP_PATH . 'admin' . DS . 'view' . DS . 'message.php',//THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => APP_PATH . 'admin' . DS . 'view' . DS . 'message.php',//THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl' => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle' => '',

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__ROOT__'=>str_replace('/index.php','',\think\Request::instance()->root()),
        '__APP__'=>\think\Request::instance()->root(),
        '__STATIC__'=>str_replace('/index.php','',\think\Request::instance()->root()).'/static'
    ],
    
    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type' => 'File',
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别
        'level' => [],
        'apart_level' => ['rpc', 'sms', 'pay', 'sql', 'urlSubmit', 'error', 'oauth','cashout', 'message', 'debug', 'request', 'cache', 'vauth']
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    /*
    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    */
    'cache' => [
        'type' => 'Redis',
        'host' => '47.244.25.135',
        'port' => 6379,
        'password' => 'q123456',
        'prefix' => '',
        'clearBool' => false, // 改成true即可\DclCache::get直接返回[]
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'SESSION_AUTO_START' => true,
    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'adminSess',
        // 驱动方式 支持redis memcache memcached
        'type' => 'redis',
        // 是否自动开启 SESSION
        'auto_start' => true,
        'host' => '47.244.25.135',//可改为主服务器IP:172.17.123.123，不用担心备用服务器redis只读的问题
        'port' => '6379',
        'password' => 'q123456',
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate' => [
        'type' => 'bootstrap',
        'var_page' => 'page',
        'list_rows' => 15,
    ],

    // +----------------------------------------------------------------------
    // | business server 业务数据库配置    测试库
    // +----------------------------------------------------------------------
    'bs_db_config' => [
        'type' => 'mysql',
        'hostname' => '47.244.25.135',
        'database' => 'talk',
        'username' => 'root',
        'password' => '123456',
        'hostport' => '3306',
        'prefix' => 'talk_',
        // Query类
        'query'          => \ThinkPHP\Query::class,
        
        'debug' => true,
    ],
        
    // +----------------------------------------------------------------------
    // | business server 业务数据库配置    测试库
    // +----------------------------------------------------------------------
    'mongo_online_room_info_stat' => [
            'type' => '\MongoConnection',
            'hostname' => '123.103.74.8',
            'hostport' => '27019',
            'database' => 'online_room_info_stat',
            'debug' => true,
            'params' => [
                    'connectTimeoutMS' => 5000,
                    'socketTimeoutMS' => 10000
            ]
    ],

    // +----------------------------------------------------------------------
    // | 搜索引擎 配置文件名称
    // +----------------------------------------------------------------------
    'xs_config_ini' => [
        'information' => 'dcl_information',
    ],
    
    // 课程评论
    'mongo_course_msg' => [
        'type' => '\MongoConnection',
        'hostname' => '123.103.74.8',
        'hostport' => '27019',
        'database' => 'group_pchat_msg',
        'debug' => true,
        'params' => [
            'connectTimeoutMS' => 5000,
            'socketTimeoutMS' => 10000
        ]
    ],
        
    // mongoDB talk数据库
    'mongo_talk' => [
            'type' => '\MongoConnection',
            'hostname' => '123.103.74.8',
            'hostport' => '27019',
            'database' => 'talk',
            'debug' => true,
            'params' => [
                    'connectTimeoutMS' => 5000,
                    'socketTimeoutMS' => 10000
            ]
    ],


    // magoc_cube
    'mongo_group_msgid_config' => [
        'type' => '\think\mongo\Connection',
        'hostname' => '123.103.74.8',
        'hostport' => '27018',
        'database' => 'group_chat_stat',
        'params' => [
            'connectTimeoutMS' => 5000,
            'socketTimeoutMS' => 10000
        ]
    ],

    //mongdoDB online_room_stat库
    'mongo_online_room_stat' => [
            'type' => '\MongoConnection',
            'hostname' => '123.103.74.8',
            'hostport' => '27019',
            'database' => 'online_room_stat',
            'debug' => true,
            'params' => [
                    'connectTimeoutMS' => 5000,
                    'socketTimeoutMS' => 10000
            ]
    ],

    // 是否自动转换URL中的控制器和操作名
    'url_convert' => false,

    'UPLOAD_URL' => 'http://183.60.136.3:32389/index.php?a=File&c=uploadChatPic',
    'PIC_DOMAIN' => 'http://183.60.136.3:32389', // 图片地址
    
    
    'QnPIC_DOMAIN' => 'http://qnfile.ogod.xin', // 7牛图片地址
    'WX_DOMAIN' => 'http://test.talk.99cj.com.cn', // h5域名
    'ADMIN_DOMAIN' => 'http://test.admin.talk.99cj.com.cn', // h5域名
    'WEB_DOMAIN' => 'http://csgw.99cj.com.cn',//官网域名
    'MINI_PROGRAM' => 'https://ts.talk.99cj.com.cn',//小程序域名
    'JH_DOMAIN' => 'http://jhkt.99cj.com.cn',//家华课堂域名
    
    

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => [
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 1 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'exts_js' => '*.jpg; *.png; *.gif;', //js插件上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        'minWith' => 534,//图片最大宽度（此配置为自定义属性，TP上传类中并未提供相关功能）
        'minHigh' => 300,//图片最大宽度（此配置为自定义属性，TP上传类中并未提供相关功能）
        'service_url' => 'http://pic.dks.dacelue.com.cn/index.php?a=File&c=index',//上传地址
        'service_domain' => 'http://pic.dks.dacelue.com.cn',
    ],
    /* 视频上传相关配置 */
    'VIDEO_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 200 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'mp4', //允许上传的文件后缀
        'exts_js' => '*.mp4;', //js插件上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/pastVideo/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        'service_url' => 'http://pic.dks.dacelue.com.cn/index.php?a=File&c=index',//上传地址
        'service_domain' => 'http://pic.dks.dacelue.com.cn',
        //'service_url' => 'http://58.210.107.57/index.php?a=File&c=index',//上传地址
        //'service_domain' => 'http://58.210.107.57',
    ),
    /* 附件上传相关配置 */
    'ATTACHMENT_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 10 * 1024 * 1024, //上传的文件大小限制 (0-不做限制) 10M
        'exts' => 'txt,doc,docx,xlsx,xls,ppt,pptx,zip,rar,pdf', //允许上传的文件后缀
        //'exts'     => 'txt,doc,docx,xlsx,xls,ppt,zip,rar,jpg,png,jpeg,bmp,pdf,pptx,alg,tne,mp4', //允许上传的文件后缀
        'exts_js' => '*.txt;*.doc;*.docx;*.xlsx;*.xls;*.ppt;*.zip;*.rar; *.jpg; *.png;*.bmp;*.pdf;*.pptx;*.alg;*.tne;*.mp4', //js插件上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/pastVideo/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => '', //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => true, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        'service_url' => 'http://pic.dks.dacelue.com.cn/index.php?a=File&c=index',//上传地址
        'service_domain' => 'http://pic.dks.dacelue.com.cn',
        //'service_url' => 'http://58.210.107.57/index.php?a=File&c=index',//上传地址
        //'service_domain' => 'http://58.210.107.57',
    ),

    'TOPIC_MAX_INFORMATION' => 20, //专题最多挂多少个精选内容
    'TOPIC_MAX_RECOMMEND' => 3, //专题最多可推荐数

    //socket推送
    'SOCKET_CONFIG' => [
        //直播室web_socket
        'WEB_SOCKET' =>['host' => '123.103.74.8','port' => '6263'],
        //直播室另一个web_socket
        'WEB_SOCKET_1' =>['host' => '123.103.74.8','port' => '6263'],
        
        //其他邮箱通知，专家观点消息推送
        'PUSH_SERVER' => ['host' => '125.94.214.3', 'port' => '7879'],

        //新资讯通知推送、新关注消息推送、新公告推送
        'PUSH_SERVER_INFO' => ['host' => '123.103.74.8', 'port' => '7871'],

        //网页端新weibo推送
        'PUSH_WEIBO_INFO' => ['host' => '123.103.74.8', 'port' => '7272'],

        //后台新公告推送
        'PUSH_NOTIC_INFO' => ['host' => '123.103.74.8', 'port' => '7871'],
    ],
    'captcha' => [
        // 验证码字符集合
        'codeSet' => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 13,
        // 是否画混淆曲线
        'useCurve' => false,
        // 是否添加杂点
        'useNoise' => false,
        // 验证码图片高度
        'imageH' => 30,
        // 验证码图片宽度
        'imageW' => 100,
        // 验证码位数
        'length' => 4,
        // 验证成功后是否重置
        'reset' => true
    ],
    //测试版支付配置
    'pay' => [
        'alipay' => [
            //appid
            'app_id' => '2017050907176804',
            //支付宝网关
            'ali_gateway' => 'https://openapi.alipay.com/gateway.do',
            //开发者私钥
            'rsaPrivateKey' => 'MIIEpAIBAAKCAQEArIYDl+rWPjMrLsrOtEVEjpE5BoUtVqLRUclW0vBFYGEtbqEzkbcPPoH1eeGBFPBOb0o8jFVZ9JJGBaanQzGGWri4NppFu9/K0MQlg+GDXjQ1Ck4EpVUjj/kKRCQEBU5ZTUkCM/Lqho4k/sUgOo2352NP6mkkguR9Ks0AUa2ES4EkS7DWIk+ZDFnoq2iIJ0ekNkmXXVjrqLGNKHDsMGrtX7MW3H3Fu6BASuZzUPxxZOPqIvYA+2qQe5ap0BCRgUaDhl0O154JVy3l6GKvWghZXVNHlvGlMVU94yeO/gmaQNUqeBTTEuRnaQJEg47rAMypxMW440jei5ZqugJzNabFUwIDAQABAoIBAQCkVXsyyBSPZvopNjGy8ZAeJSKmnVDUh37PYvN7Njc/WRGHobGXiUHSDsSe9jiYk1aDF7mZCuwG1RYx587HaHNME0wVZtop3UA5n70EZFlX8G7dg/dU7UZUq6olwhC6ZSkXPwYEKc1PojKHI4pRfBDDLYxAAysuKys3C9LT+m5b4/Oory1nXUczOOqpoJmrJTD/yYc4FAmSsD1C5PFcUIMyoHAhSd/drjPbW5yM2/q/3erJB2q5ZQlRxA8AYHCVOQuqnUfZZHwq3f3emPLXgNs0VSTvPzQtX7J+LoPU1xD2e7uIq9rVn815fhe2K/ZpX5Z9jrlyJtSvzUbyNGhqc/whAoGBAOUzWwFo1+rI4SSR0KCtM1esyPYViQRb51lxs923EE2C2BQ2V4X4eBHN/ERDE2Ijh6uvmnT5cD810wJfKsIdpfKc0ab7r4giOiPDa2WZc0ytaptwio0KjeL2cCEatmTN328FG3rvj4aYGSA9lRLBKG1xjiutgDJNzoREaCzblPC7AoGBAMCyJZBDOHv13d51Qjtl15bdfngE8lXyPmVWdTEts7JxcG8opZWHiddc7A3eR+fhKOPuwnzSsifocqBP2vSPimr0xupYdw6KmALSF6/pMsYU+iUl83gPVri8gLL9c7aFb3yOhquEzE6XNIsUqRBNQ7wgrE6aU9geLgh3PuFvOGBJAoGAaW3AFNvnRgZ1Gd9A+kfxcAj4v8mUJz8nIgudUwVcKGthfZqpk+SBRp9bxQKdrezuAVlbUSdULJ9Tmqmv/26n4PulrIvlaFdmKwE/K5L0aHb0rN+Nu+b48UeuuGuTLrdMacMNqvT7LkxUcnIhWmkjcYRkg07hw0HrFwhoawnubvUCgYEAt7pkFHvzZEYWSOB2yRmeiIsh5Z+ZzBbQJWFdFgnkV1TQBjy0PLFh3UUNEWAKIW5OxZ1GNSvTkDeS64WYWCxwpvBCpPi6c9PzgmT9Ds4Dilg/9aGM1cGSR0v8Ti1Y4gOyPAnNH8bb4mdLQvztAc0Zs0tX3w5IuTx1Wb8Q5nNcRnkCgYAtGfSd28V0hOH9oI/TfXkh/fZpLXRAFvAYbRtffy8yvkijaVzcH+stGxUi7i1HWXX+Ae9PSbFHz1Xv0BRQ8/11OX3Zl3lxN/o3QCb9CZZq3JsUSPkeAz/olXY/kw7l3nQGoVA5aClAEfU6WDi2HD/NTnbtbg7VWf+Zlhp18lDoMg==',
            //支付宝公钥
            'alipayrsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5j4WNPnv/Qz8csfOZ6rLYIF0/bm3rwIi421yb8Mf4sICB/eN9qZ3zgUuA3E8utnTAglPJ2lRVvvS1SWrrhl4I3VCF6gR0AK9XSGqjUn21t05CmMffP4KvM+UvilCxSGiwaKe0uBCTRudnr+SictQSlzI0pvYOLJQEzV59DkKoFvDsy3I9rgzc+RkW87tBSWWQym1pdwta2YJawlRSXfegmsMz8UXiobDm7UaNl8izvMwesHQ7J2oZ5d0rN2uwyRYvM1S077aPRjFdgfBOqQO56sniY/6DgGFKfaBN49picClE/7KB0va1Ejce4MRLCgoD5gAE3wNH1SN2YqtnADrEwIDAQAB',
            //api版本
            'apiVersion' => '1.0',
            //交易超时时间
            'timeout_express' => '30m',//m-分钟，h-小时，d-天，1c-当天
            //异步回调地址
            'place_order_callback_url' => '/Pay/callback',
            //卖家支付宝用户号
            // 'seller_id' => '2088802790267072',
            //卖家支付宝账号
            'seller_email' => '18620621961'
        ],
        'wxpay' => [
            'appid' => 'wxb895b8067f425478',//app支付
            'mch_id' => 1489293092,//app支付
            'key' => 'ffer8h5w5d4t6s598t5w5cd5ftgw4f5r'//app_key
        ],
        'applepay' => [],
        'lianlianPay' => [
            'oid_partner' => '201712290001333864',
            'RSA_PRIVATE_KEY' => '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDNj/EQjSRW1x4ruVbKpv6iPpIPVKSH5D/ZLcvWXgzlMYNGp6+D
/2v3bHcjRCAw+/vOssuppeYJMEdy+GKWzxuWKUBzBOPah/UIGkwi0tlSuysU7SQf
puZ04CuUQjojPliwZ95UcHoG3nRo3sNYdaXWzWIha+n2EXrxfM7sWjqAewIDAQAB
AoGAF6cpcOMcvFVSZmuUHgtrH1Ydzl/J8s0Dv8SyQL9fsnupBFdFLeYVEUpMxyUO
ozRLfDQ8lQ++0W3ZutPz3DCGluLB4k35zgjhx+LpjY93LbcljRtFAWN/5ewptej4
tVEKkZMY+paaWhSVDi4hcBeJFypLVKL8VwHBwvx8v7j8i5ECQQD4G1ttSyYJNMW2
qUK2tsDNkjGGEyT518QK5/KR/shfNxW7YVunm77LsSbR4oJ8XFdjcOX16VKfgsMt
HWfMFWWDAkEA1BoXSplq6DLxVRd4TAtPRxMOwo09VQ4ZbIw+TNiF9bXd+DTyAmC4
NHfePNNRK8uPzCuXPrPQoFmV8gGx/H//qQJAAvPwZqCaV0m1gLMLBDmwmcG/rSTV
L9QNlUOlc29g2yFAtPY3rQsBflMhbyYO/4Pp1lklo4OfZB6eTA8piRhIGQJBALb5
fyha65A/ClSm959ajly5Qx1xLPzoOeSbo881Z3NOHpxWSITmnWKeGfmNL1RBut6e
qE5uX0dFoYZyEfLLFWkCQAiVtD8g4Rve709fHLgYGUiQKsZnTIL3Xq8uXZZMILly
jNXIgKiei3yVc40HNPmFcq5cGnId6wuRZ1WMYNPJ5rM=
-----END RSA PRIVATE KEY-----',
            'key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQkPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB',
            'version' => '1.2',
            'app_request' => '3',
            'sign_type' => strtoupper('RSA'),
            'valid_order' => '10080',
            'input_charset' => strtolower('utf-8'),
            'transport' => 'http'
        ]
    ],
    

    //静态资源版本
    'public_version' => 1,

    //用户默认头像
    'USER_HEAD_ADD' => '/images/default/userDefault.png',
        
    //助教默认二维码
    'ASSISTANT_QRCODE' => '/images/default/assistantDefaultQrCode.png',

    //用戶密碼加盐
    'USER_PASSWORD_SALT' => 'salt:2014',


    'WEB_GATE' => '123.103.74.8',

    'WEB_GATE_PORT' => '7272',

    'OAUTH' => [
        //微博第三方登录
        'WB_KEY' => '3708044694',
        'WB_SECRET' => '852f9d4a19bd7fcd46402ae33ae2705f',

        //QQ第三方登录
        'QQ_APP_ID' => '1105884871',
        'QQ_APP_KEY' => 'yAaLgY0DdozwAqGU',

        //微信第三方登录
        'WX_APP_ID' => 'wxaec19a9e4fde12b2',
//        'WX_APP_KEY' => '093a4cac4d272f34fbe0c2f4cc9db139',
        'WX_APP_SECRET' => '093a4cac4d272f34fbe0c2f4cc9db139'
    ],


    /*視頻流地址*/
    'RTMPIP' => 'testpull.99ducaijing.cn',
    //视频流拉流地址
    'pull_url' => 'http://pull.test.99cj.com.cn/live/',
    'push_url' => 'rtmp://push.test.99cj.com.cn/live/',

    /*RPCip以及端口*/
    'RPC_IP' => '123.103.74.8',
    'RPC_PORT' => '16050',
    /*赞赏RCP端口*/
    'ADMIRE_RPC_PORT' => '16110',

    /* DB_AREA 数据库区域 */
    'DB_AREA' => 1,
    
    /* 是否开启提现 0 关闭, 1 开启 */
    'WITHDRAW_DEPOSIT_OPEN' => 1,
    
    /* 提现比例 */
    'WITHDRAW_DEPOSIT_RATE' => 0.03,

    /* 是否开启短信认证  0 关闭, 1 开启 */
    'SMS_CHECK_OPEN' => 0,

    /* 通知 maindb, gzdb, bjdb*/
    'TCP_DB_HOST' => 'maindb',
    'QINIU' => [
        'ACCESS_KEY' => '-A_E2mEZ14NOTYAq9dSR-Uhg4tAtT-HVJWX1m4m2',
        'SECRET_KEY' => 'AgNMfRQT12HrKoSOKKwsYe8N49TBlvaTNe9Pjtjh',
        'BUCKET' => '0531',
        'LIVE_BUCKET' => 'qiaoliaohd',
        'CALLBACK_URL' => 'http://183.60.136.3:9981/Index/fileUploadCallback',
        'persistentPipeline_mp3' => 'bndytestts', // 异步队列名称--已用于转mp3
        'FILE_DOMAIN' => 'http://qnfile.ogod.xin'
    ],
    //微信配置
    'wechat' => [
        'app_id'  => 'wx2b76dd0059adc7a4',         // AppID
        'secret'  => 'e3a5aa57b9c621a7a38522f57a3818cf',     // AppSecret
        'token'   => 'weixin',          // Token
        'aes_key' => 'e3a5aa57b9c621a7a38522f57a3818cf',
        'mini_program_app_id' => 'wx1ad34c22b1537da9',  //小程序AppID
        'mini_program_app_secret' => 'cb72012af7045f8dc8e94e99d495bc93',    //小程序AppSecret
        'MCHID' => '1508517661',
    ],
    //模板id推送
    'wechat_template' =>[
        'apply_template' => 'Z3-qM0bXQ1XiIdilOPy4fVo_9LlA8ibtopBiUD99LNo',  //申请模板
        'success_template' => 'Ia7UMtZM8d1vruUnkMF91dKrtv7iqtiQSkalo9wey5w',    //到帐模板
        'fail_template' => 'd7lfmjBehhM93PdeU0IB2t6nPkvZdpy1A7ptwR8j6OY'    //失败模板
    ],
    
    
    
    
    'test_controller_pass_ip' => [ // 允许访问test控制器的ip
        '219.135.128.114',
        '219.135.128.85',
        '219.135.128.101',
        '123.103.74.9',
        '123.103.74.10',
        '127.0.0.1',
    ],


    /* 视频推流拉流配置 %s为播放标识 暂时不暴露推流地址*/
    'video_stream' => [
        'base' => 'pull.test.99cj.com.cn',
        'push' => 'rtmp://push.test.99cj.com.cn/live/%s?user=99cj&passwd=hc992017win',//推流
//        'play' => 'rtmp://pull.test.99cj.com.cn/%s',//播放路径
        'pull' => 'http://pull.test.99cj.com.cn/live/%s/playlist.m3u8'//拉流
    ],
    //官方账号id
    'official_id' => 1706761,
    //appdownload_qrcode
    'appdownload_qrcode' => '/images/downapp_qrcode.jpg',
    //focus_qrcode
    'focus_qrcode' => '/images/focus_qrcode.jpg',
    //miniProgram_qrcode
    'miniProgram_qrcode' => '',
    //大策略订阅素材media_id
    'material_media_id' => '_PgYYXCVbXwlFFrBt40re4HlRDKknh9TmzlrVTdggbQ',
    //公众号二维码
    'qrcode' => '/images/index/test_qrcode.jpg',
    //公众号客服二维码
    'kefucode' => '_PgYYXCVbXwlFFrBt40re5rPErOLZ5D6NIJ3oFteHHo',
];
