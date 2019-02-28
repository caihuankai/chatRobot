<?php

//------------------------
// 扩展 助手函数
//-------------------------

use app\common\controller\JsonResult;
use think\Config;
use think\Db;
use think\Loader;

if (!function_exists('Bs')) {
    /**
     * 实例化数据库类
     * @param string $name 操作的数据表名称（不含前缀）
     * @param array|string $config 数据库配置参数
     * @param bool $force 是否强制重新连接
     * @return \think\db\Query
     */
    function Bs($name = '', $config = [], $force = true)
    {
        empty($config) && $config = Config::get('bs_db_config');
        return Db::connect($config, $force)->name($name);
    }
}



/**
 * 日志文件调试
 *
 * @see tail -f /www/talk/runtime/log/fileDebug.log
 * @param $data
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function fileDebug($data){
    $logPath = LOG_PATH . '/fileDebug.log';
    
    file_put_contents($logPath, var_export($data, true) . PHP_EOL, FILE_APPEND);
}


/**
 * 根据key获取data的数据
 *
 * @param array|\ArrayAccess $data
 * @param       $key
 * @param mixed $defaultKey
 * @return mixed
 */
function getDataByKey($data, $key, $defaultKey){
    return isset($data[$key]) ? $data[$key] :
        (!is_array($defaultKey) && !is_object($defaultKey) && isset($data[$defaultKey]) ? $data[$defaultKey] : $defaultKey);
}


/**
 * 多维数组递归合并
 * 数组合并时同key合并，不管是否数字
 *
 * @return array|mixed
 */
function arrayMergeRecursive(){
    $arrays = func_get_args();
    $base = array_shift($arrays);
    if(!is_array($base)) $base = empty($base) ? array() : array($base);
    foreach($arrays as $append) {
        if(!is_array($append)) $append = array($append);
        foreach($append as $key => $value) {
            
            if(is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                $base[$key] = arrayMergeRecursive($base[$key], $append[$key]);
            } else {
                $base[$key] = $value;
            }
        }
    }
    
    return $base;
}




/**
 * 根据keys获取多维数据的值
 * <code>
 * multiDataByKeys([[2=>2]], '0.2');
 * multiDataByKeys([[2=>2]], '0.2', 2);
 * multiDataByKeys([[2=>2]], 0, 2, 2);
 * </code>
 *
 * @param array $data 多维数据
 * @param array ...$args
 * @return array|mixed|string
 */
function multiDataByKeys(array $data, ...$args)
{
    $count = count($args);
    if ($count < 3) {
        $default = $count === 1 ? '' : $args[$count - 1];
        unset($args[$count - 1]);
        $keyArr = is_string($args[0]) ? explode('.', $args[0]) : $args;
    } else {
        $default = $args[$count - 1];
        unset($args[$count - 1]);
        $keyArr = $args;
    }
    
    foreach ($keyArr as $item) {
        if (isset($data[$item])) {
            $data = $data[$item];
        } else {
            return $default;
        }
    }
    
    return $data;
}



function addDbKey(&$val, $key, $param)
{
    $val[$param['key']] = $param['val'];
}

/**
 * 验证是否手机
 * @param unknown $phone
 */
function is_phone($phone)
{
    if (!is_numeric($phone)) {
        return false;
    }
    return preg_match('#^1(3|4|5|7|8)[\d]{9}$#', $phone) ? true : false;
}

/**
 * 注册验证
 * @param $tel 手机号码
 * @return \think\response\Json
 */
if (!function_exists('checkTelIsExist')) {
    function checkTelIsExist($tel)
    {
        $userModel = new \app\common\model\User();
        $modelTel = $userModel->checkTelIsExist($tel);
        if (isset($modelTel)) {
            return \app\home\controller\Error::ERR_TEL_IS_REGISTER;
        } else {
            return 0;
        }
    }
}

/**
 * 验证密码是否合法
 * @param $password 密码
 * @return bool
 */
if (!function_exists('check_password')) {
    function check_password($password)
    {
        if (strlen($password) < 6 || strlen($password) > 16) {
            return false;
        }
        if (strstr($password, ' ') !== false) {
            return false;
        }
        return true;
    }
}

/**
 * 防止重复提交
 * name为['time' => 2, 'name' => 'name']，time为提交间隔
 *
 * @param null|array $value 存在值得时候会设置当前限制秒数
 * @return bool|int 错误时返回错误码
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function repeatLimit($value)
{
    static $data = [];
    $bool = '';
    $issetBool = isset($value['time']) && func_num_args() < 2; // 是否设置
    if (!$data && $issetBool) {
        $data = $value;
    }

    if (!isset($data['time']) || empty($data['name']) || $issetBool) {
        return $bool;
    }

    $time = time();
    // 限制重复提交的秒数
    $timeArr = \think\Session::get('repeatLimitTime');
    if (isset($timeArr[$data['name']]) && $timeArr[$data['name']] + $data['time'] > $time) { // 重复提交
        $bool = JsonResult::ERR_SUBMIT_FAST;
        JsonResult::setMsgArgs($bool, $data['time'] . '秒');
    } else { // 不重复
        $timeArr[$data['name']] = $time;
        \think\Session::set('repeatLimitTime', $timeArr);
    }

    return $bool;
}


/**
 * 连接url
 *
 * @return string
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function url_join()
{
    $args = func_get_args();
    $first = array_shift($args);
    $last = array_pop($args);
    $url = rtrim($first, '/');
    foreach ($args as $arg) {
        $url .= '/' . trim($arg, '/');
    }
    $url .= '/' . ltrim($last, '/');

    return $url;
}


/**
 * 根据内容找到图片
 *
 * @param        $content
 * @param string $replaceType todo 目前在test会出现获取id失败的问题，带优化
 * @return array
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function getImageList(&$content, $replaceType = '')
{
    if (empty($content)) {
        return [];
    }

    $id = uniqid('php_DOMDocument_');
    $document = new \DOMDocument();
    // 尽量将meta放到head中，处理乱码
    $start = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>
                <body><div id="' . $id . '">';
    $end = '</div></body>';
    $internalErrors = libxml_use_internal_errors(true);
    $document->loadHTML($start . $content . $end);
    libxml_use_internal_errors($internalErrors);

    $lists = $document->getElementsByTagName('img');

    $result = [];
    /** @var \DOMElement $item */
    foreach ($lists as $item) {
        $url = $item->getAttribute('src');
        $result[] = $url;

        // 替换原图片
        $replaceType && $item->setAttribute('src', getLocalImage($url, $replaceType, '', true));
    }

    $replaceType && $lists && $content = substr($document->saveHTML($document->getElementById($id)), 12 + strlen($id), -6);

    return $result;
}


/**
 * 获取缩略图的url
 *
 * @param string $imageUrl url地址
 * @param string $width 可分别为small, middle, big, 或宽度
 * @param string $height 高度，但width为指定类型时可为空
 * @return string
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function getLocalImage($imageUrl, $width, $height = '', $bool = false)
{
    $map = [
        'small' => ['200', '200'], // 头像大小
        'middle' => ['320', '320'],
        'big' => ['768', '768'],
    ];
    $imageData = [];
    $picUrl = Config::get('PIC_DOMAIN');

    if (
        empty($imageUrl) || empty($width) ||
        (empty($height) && !isset($map[$width])) || // 指定类型
        ($bool && !($imageData = parse_url($imageUrl))) // 过滤无效参数
    ) {
        return '';
    }
    empty($height) && list($width, $height) = $map[$width]; // 指定类型

    if ($imageData) { // 严格替换
        if (empty($imageData['host']) || $imageData['host'] !== parse_url($picUrl, PHP_URL_HOST)) {
            return $imageUrl;
        }

        $imageUrl = url_join($picUrl, $imageData['path']);
    }


    return preg_replace('/(.+group\d{1}\/M\d{2}\/.+)(?:-\d+-\d+)?(\.[^.]+)$/', "\\1-${width}-${height}\\2", $imageUrl);
}


/**
 * 拼接图片域名，如果已经存在则返回当前图片
 *
 * @param $picUrl
 * @return string
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function pic_url($picUrl, $default = '')
{
    return \helper\UrlSys::picUrl($picUrl, $default);
}


/**
 * 返回url的host，用于系统url方法的第四个参数
 *
 * @param $config
 * @return string
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function get_config_host($config)
{
    $url = config($config);

    return parse_url($url, PHP_URL_HOST);
}


/**
 * 剪切字符串
 *
 * @param $text
 * @param $num
 * @return string
 * @author aozhuochao<aozhuochao@99cj.com.cn>
 */
function cut_text($text, $num)
{
    $length = mb_strlen($text);
    if ($length > $num) {
        $text = mb_substr($text, 0, $num) . '...';
    }

    return $text;
}


/*
 * socket收发数据
 * @host(string) socket服务器IP
 * @post(int) 端口
 * @str(string) 要发送的数据
 * @back 1|0 socket端是否有数据返回
 * 返回true|false|服务端数据
 */

function send_socket_msg($host, $port, $str, $back = 0)
{
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    if ($socket < 0)
        return false;
    $result = @socket_connect($socket, $host, $port);
    if ($result == false)
        return false;
    socket_write($socket, $str, strlen($str));

    if ($back != 0) {
        $input = socket_read($socket, 1024);
        socket_close($socket);
        return $input;
    } else {
        socket_close($socket);
        return true;
    }
}

//发布时间
//x≤4分钟  刚刚
//4<x≤10分钟 (x+1)分钟
//10分钟<x<当天 hh:mm
//非当天 mm-dd hh:mm
/**
 *
 * @param number $ctime 待转换的时间戳
 * @param number $now 当前时间戳
 * @param number $todaytime 今天开始时间戳
 * @return string
 */
function formatter_inform_time($ctime, $now = 0, $todaytime = 0)
{
    return date('Y-m-d H:i:s', $ctime);
    $now || ($now = time());
    $todaytime || ($todaytime = strtotime(date('Y-m-d', time())));

    $invltime = $now - $ctime;
    if ($invltime <= 240) {
        return "刚刚";
    } else if ($invltime <= 600) {
        return "(" . (ceil($invltime / 60) + 1) . ")分钟前";
    } else if ($ctime > $todaytime) {
        return date('H:i', $ctime);
    } else {
        return date('m-d H:i', $ctime);
    }
}

/**
 * 生成GUID
 * @author Larry <sad812@163.com>
 * @return string
 */
if (!function_exists('guid')) {
    function guid()
    {
        if (function_exists('com_create_guid')) {
            $uuid = com_create_guid();
            $uuid = substr($uuid, 1, strlen($uuid) - 2);
            return $uuid;
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $char_id = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($char_id, 0, 8) . $hyphen
                . substr($char_id, 8, 4) . $hyphen
                . substr($char_id, 12, 4) . $hyphen
                . substr($char_id, 16, 4) . $hyphen
                . substr($char_id, 20, 12);
            return $uuid;
        }
    }
}


if (!function_exists('get_contents')) {
    function get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);

        // -------请求为空
        if (empty ($response)) {
            // $this->error->showError("50001");
            echo '出错';
        }
        return $response;
    }
}

/**
 * 获取充值流水码
 * @return string
 * @author Larry <sad812@163.com>
 */
if (!function_exists('getTradeNo')) {

    function getTradeNo()
    {
        $str = "T";
        $random = '';
        for ($i = 0; $i < 9; $i++) {
            $random .= rand(0, 9);
        }

        $str = $str . date('YmdHis') . $random;

        return $str;
    }
}


if (!function_exists('get_client_ip')) {
    function get_client_ip($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

/**
 * 验证邮箱是否正确
 * @param unknown $email
 * @return boolean
 */
if (!function_exists('is_email')) {
    function is_email($email)
    {
        $emailResult = false;
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (preg_match($pattern, $email)) {
            $emailResult = true;
        }
        return $emailResult;
    }
}

if (!function_exists('is_utf8')) {
    function is_utf8($string) //函数一
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
        [\x09\x0A\x0D\x20-\x7E] # ASCII
        | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
        | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
        )*$%xs', $string);
    }
}

/**
 * 上传传文件到 FDFS 上
 * @param   $target_file  文件路径
 * @param   $target_filename 文件名
 * @param   $is_delete boolean 上传后，是否要删除文件
 * @return  array
 */
if (!function_exists('upload_file')) {
    function upload_file($target_file, $target_filename, $is_delete = true)
    {
        $file_name = dirname($target_file) . DS . $target_filename;
        if (!is_utf8($file_name)) {
            $file_name = iconv('GB2312', 'UTF-8', $file_name);
            $target_filename = iconv('GB2312', 'UTF-8', $target_filename);
        }
        rename($target_file, $file_name);
        $data['file'] = new \CURLFile($file_name);
        $result = post(config('UPLOAD_URL'), $data);
        $arr = json_decode($result, true);
        //删除文件
        if ($is_delete) {
            if (file_exists($file_name) && is_file($file_name)) {
                unlink($file_name);
            }
        }

        return $arr;
    }
}

/**
 * 模拟post提交
 * @param   $url
 * @param   $data
 * @return result
 */
if (!function_exists('post')) {
    function post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $return_data = curl_exec($ch);
        curl_close($ch);
        return $return_data;
    }
}

/**
 * 通过链接post获取数据
 */
if (!function_exists('https_post')) {
	function https_post($url, $data = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}

if (!function_exists('isLogin')) {
    function isLogin()
    {
        /** @var \app\common\model\User $uDAL */
        $uDAL = model('common/User');
        $user = $uDAL->checkLogin();
        if (empty($user)) {
            return false;
        } else {
            return true;
        }
    }
}
if (!function_exists('time_tran')) {
    /**
     * 时间处理
     * @param $the_time
     * @return string
     * @author: zhengkejian
     * @Date: 20161114
     * @Time: ${TIME}
     */
    function time_tran($time, $str = 'Y-m-d H;i:s')
    {
        isset($str) ? $str : $str = 'm-d';
        $way = time() - $time;
        $r = '';
        if ($way < 60) {
            $r = '刚刚';
        } elseif ($way >= 60 && $way < 3600) {
            $r = floor($way / 60) . '分钟前';
        } elseif ($way >= 3600 && $way < 86400) {
            $r = floor($way / 3600) . '小时前';
        } elseif ($way >= 86400 && $way < 2592000) {
            $r = floor($way / 86400) . '天前';
        } elseif ($way >= 2592000 && $way < 15552000) {
            $r = floor($way / 2592000) . '个月前';
        } else {
            $r = date("$str", $time);
        }
        return $r;
    }
}
/**
 *获取当前用户ID
 */
function getUserId()
{
    return session('user.user_id');
}

/**
 * 获取星座
 * @param unknown $birthday
 * @return NULL|unknown|string
 */
function getConstellation($birthday)
{
    list(, $month, $day) = explode('-', $birthday);
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
    $signs = array(
        array('20' => '水瓶座'),
        array('19' => '双鱼座'),
        array('21' => '白羊座'),
        array('20' => '金牛座'),
        array('21' => '双子座'),
        array('22' => '巨蟹座'),
        array('23' => '狮子座'),
        array('23' => '处女座'),
        array('23' => '天秤座'),
        array('24' => '天蝎座'),
        array('22' => '射手座'),
        array('22' => '摩羯座')
    );
    list($start, $name) = each($signs[$month - 1]);
    if ($day < $start) list($start, $name) = each($signs[($month - 2 < 0) ? 11 : $month - 2]);

    return $name;
}

/**
 * 获取年龄
 * @param unknown $birthday
 */
function getAge($birthday)
{
    list($year, ,) = explode('-', $birthday);
    return date('Y') - $year;
}

if (!function_exists("filteringLuckNumber")) {
    function filteringLuckNumber($num)
    {
        $order4up = "/(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)){3}\\d$/"; // 后四位顺增
        $order4down = "/(?:9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){3}\\d$/"; // 后四位顺降

//        $aabbcc = "/(\d)\\1(\d)\\2(\d)\\3$/";

//        $aaabb = "/(\d)\\1\\1(\d)\\2$/";
//        $aaaab = "/(\d)\\1\\1\\1(\d)$/";
//
//        $a66a = "/(\d)66\\1$/";
//        $a88a = "/(\d)88\\1$/";
//        $a99a = "/(\d)99\\1$/";
//        $aaa6 = "/(\d)\\1\\1[6]$/";
//        $aaa8 = "/(\d)\\1\\1[8]$/";
//        $aaa9 = "/(\d)\\1\\1[9]$/";

        $aaaa = "/(\d)\\1\\1\\1$/";//4

//        $match520 = "/520$/";
//        $match1314 = "/1314$/";
        $match5203344 = "/5203344$/";
        $match5201314 = "/5201314$/";
//        $match168 = "/168$/";
//        $match166 = "/166$/";

        if (preg_match($order4up, $num) ||
            preg_match($order4down, $num) ||
            preg_match($aaaa, $num) ||
            preg_match($match5203344, $num) ||
            preg_match($match5201314, $num)
        ) {
            return true;
        } else {
            return false;
        }
    }
}

function getHeadImgUrl($headUrl){
    if(empty($headUrl)){
        return '';
    }

    $headUrl = trim($headUrl);

    if(strpos($headUrl,'http') === 0){
        return $headUrl;
    }

    if(strpos($headUrl,'group') !== false){
        return config('PIC_DOMAIN') . '/' . $headUrl;
    }else{
        return config('QINIU.FILE_DOMAIN') . '/' . $headUrl;
    }
}

/**
 * 获取企业付款流水码
 * @return string
 * @author xiaok
 */
if (!function_exists('getCashoutNo')) {

    function getCashoutNo()
    {
        $str = "C";
        $random = '';
        for ($i = 0; $i < 9; $i++) {
            $random .= rand(0, 9);
        }

        $str = $str . date('YmdHis') . $random;

        return $str;
    }
}


if (!function_exists('WIsPhone')) {
	/**
	 * 判断手机号格式是否正确
	 */
	function WIsPhone($phoneNo){
        if (empty($phoneNo)) {
            return false;
        }
	    
		$reg = "/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/";
		$rs = \think\Validate::regex($phoneNo,$reg);
		
		return !!$rs;
	}
}


/**
 * 获取二维码卡号
 * @return int
 * @author xiaok
 */
if(!function_exists('getQrCodeNo')){
    function getQrCodeNo(){
        $str = rand(1, 3);
        $random = '';
        for ($i = 0; $i < 9; $i++) {
            $random .= rand(0, 9);
        }
        $result = $str*1000000000 + (int)$random;

        return $result;
    }
}

if (!function_exists('WReturn')) {
	/**
	 * 生成数据返回值
	 */
	function WReturn($msg,$status = -1,$data = []){
		$rs = ['status'=>$status,'msg'=>$msg];
		if(!empty($data))$rs['data'] = $data;
		return json_encode($rs);
	}
}

if (!function_exists('WSendSMS')) {
	/**
	 * 创蓝短信服务商
	 * @param string $phoneNumer  手机号码
	 * @param string $content     短信内容
	 */
	//function WSendSMS($phoneNumer,$content){
	function WSendSMS( $mobile, $msg, $needstatus = 'true') {
	    \think\log::sms(['mobile' => $mobile, 'msg' => $msg]);
		
		//创蓝接口参数
		$postFields = array (
			'account'  =>  "N0577401",
			'password' => "alVcqx14mD570b",
			'msg' => urlencode($msg),
			'phone' => $mobile,
			'report' => $needstatus
        );
		$url = 'http://vsms.253.com/msg/send/json';
		
		$postFields = json_encode($postFields);
		$ch = curl_init ();
		curl_setopt( $ch, CURLOPT_URL, $url ); 
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8'
			)
		);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,1); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
		curl_close ( $ch );
		return $result;
	} 
}

if (!function_exists('WAllow')) {
	/**
	 * 只允许一维数组里的某些key通过
	 */
	function WAllow(&$data,$keys){
		if($keys!='' && is_array($data)){
			$key = explode(',',$keys);
			foreach ($data as $vkeys =>$v)if(!in_array($vkeys,$key))unset($data[$vkeys]);
		}
	}
}

if (!function_exists('interceptHtmlImage')) {
	/**
	 * 从html标签中截取图片信息
	 * @param unknown $content
	 * @return array
	 */
	function interceptHtmlImage($content) {
		$imageList = [];
		$pattern = '/<img.*?src=[\"|\']{1}(.*?)[\"|\']{1}.*?>/i';
		preg_match_all($pattern, $content, $imageList);
		return $imageList;
	}
}

if (!function_exists('checkKeyWord')) {
	/**
	 * 检查字符串是否含义敏感词，含义返回false，否则返回true
	 * @param unknown $string
	 * @return boolean
	 * @author liujuneng
	 */
	function checkKeyWord($string)
	{
		$keyWordList = \app\admin\service\Redis::setValueGet('talk_keyword',0);
		foreach ($keyWordList as $keyWord) {
			if (stripos($string, $keyWord) !== false) {
				return false;//含义敏感词
			}
		}
		
		return true;
	}
}

if (!function_exists('filterKeyWord')) {
	/**
	 * 过滤敏感词
	 * @param unknown $string	原字符串
	 * @param string $sign	替换的符号
	 * @return mixed
	 * @author liujuneng
	 */
	function filterKeyWord($string, $sign = '*')
	{
		$keyWordList = \app\admin\service\Redis::setValueGet('talk_keyword',0);
		foreach ($keyWordList as $keyWord) {
			if (stripos($string, $keyWord) !== false) {
				$length = mb_strlen($keyWord);
				$replace = '';
				while ($length) {
					$replace .= $sign;
					$length--;
				}
				$string = str_ireplace($keyWord, $replace, $string);
			}
		}
		return $string;
	}
}

if (!function_exists('saveQiniuGallerys')) {
	
	function saveQiniuGallerys($qiniuUrl)
	{
		if (empty($qiniuUrl)) {
			return 0;
		}
		
		$qiniuKey = strrchr($qiniuUrl, '/');
		$userId = getUserId();
		$dataQiniu = ['qiniuKey' => $qiniuKey, 'qiniuImg' => $qiniuUrl, 'userId' => 0];
		$qiniu_id = Db::connect('bs_db_config')->name('qiniu_gallerys')->insertGetId($dataQiniu);
		return $qiniu_id;
	}
}

if (!function_exists('isAllowNoLogin')) {
	/**
	 * app不登录访问接口
	 * @return mixed
	 * @author liujuneng
	 */
	function isAllowNoLogin()
	{
// 		return input('isAllowNoLogin/b', false);
		return request()->header('isAllowNoLogin', false);//本身是字符串，不为0、空格均为true
	}
}

if (!function_exists('setRedis')) {
    /**
     * redis写入
     * @author xiaokai
     */
    function setRedis($key, $hashKey, $value, $dbindex = 1, $time = 60)
    {
        \app\admin\service\Redis::hashSet($key,$hashKey, $value, $dbindex,$time);
    }
}

if (!function_exists('getRedis')) {
    /**
     * redis读取
     * @author xiaokai
     */
    function getRedis($key, $hashKey, $dbindex = 1)
    {
        return \app\admin\service\Redis::hashGet($key,$hashKey,$dbindex);
    }
}

