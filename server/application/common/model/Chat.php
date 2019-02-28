<?php
namespace app\common\model;

/**
 * 微信公众平台类 目前主要用于消息推送
 * @author xiaok
 * @time 2017/06/12
 */
class Chat{

    /**
     * token
     * @var string
     */
    public static $access_token = "";

    /**
     * AppId
     * @var string
     */
    public static $appId = "";

    /**
     * AppSecret
     * @var string
     */
    public static $appSecret = "";

    /**
     * 微信获取code地址
     */
    CONST ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';

    /**
     * 微信获取code地址
     */
    CONST CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=niu#wechat_redirect';

    /**
     * 微信获取网页授权token地址
     */
    CONST TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';

    /**
     * 获取用户信息url
     */
    CONST USER_URL = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';

    /**
     * 群发url 可文字，图文，视频
     */
    CONST SEND_ALL_URL = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=%s';

    /**
     * 群发url 可文字，图文，视频
     */
    CONST PREVIEW_URL = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=%s';

    /**
     * 用户列表
     */
    CONST USERLIST_URL = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=%s&next_openid=%s';

    /**
     * 用户列表
     */
    CONST MENU_URL = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';

    /**
     * 上传图片
     */
    CONST UPLOAD_URL = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s';//'https://api.weixin.qq.com/cgi-bin/media/upload?type=thumb&access_token=%s';//

    /**
     * 上传图文素材
     */
    CONST UPLOAD_NEW_URL = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s';

    /**
     * 上传图文素材
     */
    CONST DELETE_URL = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s';

    /**
     * 发送模版消息
     */
    CONST SEND_TEMPLATE_URL = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';

    /**
     * 添加模版
     */
    CONST ADD_TEMPLATE_URL = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s';

    public static $errCode;

    public static $errMsg;

    /**
     * Chat constructor.
     * @author xiaok
     * @time 2017/06/12
     */
    public function __construct($access_token = '', $appId = '', $appSecret = '')
    {
            self::$access_token = $access_token;
            self::$appId = $appId;
            self::$appSecret = $appSecret;
    }


    /**
     * 编码获取code的请求地址
     * @param $url
     * @return string
     * @author: zhengkejian
     * @Date: 20161107
     * @Time: ${TIME}
     */
    public static function getCodeUrl($url)
    {
        return sprintf(self::CODE_URL, self::$appId, urlencode($url));
    }

    /**
     * 获取用户授权地址
     * @param $url
     * @return string
     * @author: zhengkejian
     * @Date: 20161107
     * @Time: ${TIME}
     */
    public static function getTokenUrl($code)
    {
        $url = sprintf(self::TOKEN_URL, self::$appId, self::$appSecret, $code);
        $result = self::get_contents($url);
        \app\admin\service\Redis::set("log:TOKEN_URL:" . date('Y-m-d'), $result, 3600);
        \app\admin\service\Redis::set("log:tUrl", $url, 3600);
        $data = json_decode($result, true);
        return $data;
    }

    /**
     * 获取微信用户信息
     * @param $accessToken
     * @param $openId
     * @return mixed
     * @author: zhengkejian
     * @Date: 20161107
     * @Time: ${TIME}
     */
    public static function getUserInfo($openId)
    {
        $url = sprintf(self::USER_URL, self::getAccessToken(self::$appId, self::$appSecret), $openId);
        \app\admin\service\Redis::set("log:openid_url:" . date('Y-m-d'), $url, 3600);
        $result = self::get_contents($url);
        \app\admin\service\Redis::set("log:USER_URL:" . date('Y-m-d'), $result, 3600);
        $data = json_decode($result, true);
        return $data;
    }

    /**
     * 群发消息
     * @return mixed|string
     * @author: zhengkejian
     * @Date: 20161109
     * @Time: ${TIME}
     */
    public static function sendAllMessage($mediaId)
    {
        $url = sprintf(self::SEND_ALL_URL, self::$access_token);
        $data = [
            'filter' => ['is_to_all' => true],
            'mpnews' => ['media_id' => $mediaId],
            'msgtype' => 'mpnews',
        ];
        $result = self::http_post($url, self::json_encode($data));
        $data = json_decode($result, true);
        \app\admin\service\Redis::set("weixin:send" . date('YmdHis'), $result, 3600);
        return $data;
    }

    /**
     * 发送消息，预览接口
     * @param $content
     * @author: zhengkejian
     * @Date: 20161107
     * @Time: ${TIME}
     */
    public static function preview($content)
    {
        $url = sprintf(self::PREVIEW_URL, self::$access_token);
        $data = [
            'towxname' => '擒牛宝典',
            'text' => ['content' => $content],
            'msgtype' => 'text',
        ];
        $result = self::post_contents($url, $data);
        $data = json_decode($result, true);
        \app\admin\service\Redis::set("weixin:send" . date('YmdHis'), $result, 3600);
        return $data;
    }


    /**
     * 上传图片至微信
     * @author: zhengkejian
     * @Date: 20161110
     * @Time: ${TIME}
     */
    public static function uploadPic($file)
    {
        $url = sprintf(self::UPLOAD_URL, self::$access_token);
        \app\admin\service\Redis::set("weixin:upload-url:" . date('YmdHis'), $url, 3600);
        $data = [
            'media' => new \CURLFile($file),
        ];

        \app\admin\service\Redis::set("weixin:uploadData:" . date('YmdHis'), print_r($data, true), 3600);
        $result = self::post_contents($url, $data);
        $data = json_decode($result, true);
        \app\admin\service\Redis::set("weixin:uploadPic:" . date('YmdHis'), $result, 3600);
        return $data;
    }

    /**
     * 上传图文消息素材
     * @return array|mixed
     * @author: zhengkejian
     * @Date: 20161110
     * @Time: ${TIME}
     */
    public static function uploadNew($data)
    {
        $url = sprintf(self::UPLOAD_NEW_URL, self::$access_token);
        \app\admin\service\Redis::set("weixin:uploadNew-url:" . date('YmdHis'), $url, 3600);
        $post = self::json_encode($data);
        \app\admin\service\Redis::set("weixin:uploadNew-post:" . date('YmdHis'), $post, 3600);
        $result = self::http_post($url, $post);
        $data = json_decode($result, true);
        \app\admin\service\Redis::set("weixin:uploadNew:" . date('YmdHis'), $result, 3600);
        return $data;
    }

    /**
     * 获取服务号的用户列表
     * @return mixed
     * @author: zhengkejian
     * @Date: 20161109
     * @Time: ${TIME}
     */
    public static function getUserList($openid = '')
    {
        $url = sprintf(self::USERLIST_URL, self::$access_token, $openid);
        $result = self::get_contents($url);
        $data = json_decode($result, true);
        if (isset($data['data']['openid'])) {
            return $data;
        }
        return false;
    }

    /**
     * 从缓存服务器取token,如果缓存服务器token失败,从微信服务器取token并写入缓存，token有效期7200
     * @param $appId
     * @param $appSecret
     * @return bool|string
     * @author: zhengkejian
     * @Date: 20161103
     * @Time: 19:16
     */
    public static function getAccessToken($appId, $appSecret)
    {
        $accessToken = \app\admin\service\Redis::get("weixin:access_token:" . $appId);
        if ($accessToken) {
            return self::$access_token = $accessToken;
        }
        $url = sprintf(self::ACCESS_TOKEN, $appId, $appSecret);
        $result = self::get_contents($url);
        \app\admin\service\Redis::set("log:" . $appId, $result, 3600);
        $data = json_decode($result, true);
        if ($data && isset($data['access_token'])) {
            self::$access_token = $data['access_token'];
            \app\admin\service\Redis::set("weixin:access_token:" . $appId, $data['access_token'], 7200 - 300);//提前5分钟过期
            //更新其它库token
            $redisOther = new \Redis;
            $ip = config('cache.host') == '121.46.0.125' ? '121.12.118.32' : '121.46.0.125';
            $port = config('cache.port') == '6382' ? '6384' : '6382';
            $redisOther->connect($ip, $port);
            $redisOther->auth('my#123Redis');
            $redisOther->select(1);
            $redisOther->set("weixin:access_token:" . $appId, $data['access_token'], 7200 - 300);
            return self::$access_token;
        }
        return false;
    }


    /**
     * 设置菜单
     * @return bool
     * @author: zhengkejian
     * @Date: 20161103
     * @Time: 19:56
     */
    public static function setMenu()
    {
        $url = sprintf(self::MENU_URL, self::$access_token);
        $data = [];
        $result = self::post_contents($url, $data);
        $data = json_decode($result, true);
        if ($data['errcode'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据OpenID列表群发图文消息(订阅号不可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "touser"=>array(
     *         "OPENID1",
     *         "OPENID2"
     *     ),
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public static function sendMassMessage($data)
    {
        $url = self::SEND_ALL_URL . self::$access_token;
        $result = self::http_post($url, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                self::$errCode = $json['errcode'];
                self::$errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 根据群组id群发图文消息(认证后的订阅号可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "filter"=>array(
     *         "is_to_all"=>False,     //是否群发给所有用户.True不用分组id，False需填写分组id
     *         "group_id"=>"2"     //群发的分组id
     *     ),
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public function sendGroupMassMessage($data)
    {
        $url = self::SEND_ALL_URL . self::$access_token;
        $result = $this->http_post($url, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                self::$errCode = $json['errcode'];
                self::$errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 删除群发图文消息(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     */
    public function deleteMassMessage($msg_id)
    {
        $url = self::DELETE_URL . self::$access_token;
        $result = $this->http_post($url, self::json_encode(array('msg_id' => $msg_id)));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                self::$errCode = $json['errcode'];
                self::$errMsg = $json['errmsg'];
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 高级群发消息, 预览群发消息(认证后的订阅号可用)
     *    注意：视频需要在调用uploadMedia()方法后，再使用 uploadMpVideo() 方法生成，
     *             然后获得的 mediaid 才能用于群发，且消息类型为 mpvideo 类型。
     * @param array $data 消息结构
     * {
     *     "touser"=>"OPENID",
     *      "msgtype"=>"mpvideo",
     *      // 在下面5种类型中选择对应的参数内容
     *      // mpnews | voice | image | mpvideo => array( "media_id"=>"MediaId")
     *      // text => array ( "content" => "hello")
     * }
     * @return boolean|array
     */
    public static function previewMassMessage($data)
    {
        $url = '' . self::$access_token;
        $result = self::http_post($url, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                self::$errCode = $json['errcode'];
                self::$errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 高级群发消息, 查询群发消息发送状态(认证后的订阅号可用)
     * @param int $msg_id 消息id
     * @return boolean|array
     * {
     *     "msg_id":201053012,     //群发消息后返回的消息id
     *     "msg_status":"SEND_SUCCESS" //消息发送后的状态，SENDING表示正在发送 SEND_SUCCESS表示发送成功
     * }
     */
    public static function queryMassMessage($msg_id)
    {
        $url = '' . self::$access_token;
        $result = self::http_post($url, self::json_encode(array('msg_id' => $msg_id)));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                self::$errCode = $json['errcode'];
                self::$errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }

    /**
     * 远程请求
     * @param $url
     * @return array|mixed
     * @author: zhengkejian
     * @Date: 20161103
     * @Time: 19:16
     */
    private static function get_contents($url)
    {
        //初始化
        $ch = curl_init();

        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);

        //返回获得的数据
        return $output;
    }

    /**
     * 远程请求
     * @param $url
     * @return array|mixed
     * @author: zhengkejian
     * @Date: 20161103
     * @Time: 19:16
     */
    private static function post_contents($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);
        curl_close($ch);

        //返回获得的数据
        return $output;
    }

    /**
     * 远程请求
     * @param $url
     * @return array|mixed
     * @author: zhengkejian
     * @Date: 20161103
     * @Time: 19:16
     */
    private static function post_contents_arr($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $output = curl_exec($ch);
        curl_close($ch);

        //返回获得的数据
        return $output;
    }

    /**
     * 微信api不支持中文转义的json结构
     * @param array $arr
     */
    private static function json_encode($arr)
    {
        if (count($arr) == 0) return "[]";
        $parts = array();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private static function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
            $is_curlFile = true;
        } else {
            $is_curlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        if (is_string($param)) {
            $strPOST = $param;
        } elseif ($post_file) {
            if ($is_curlFile) {
                foreach ($param as $key => $val) {
                    if (substr($val, 0, 1) == '@') {
                        $param[$key] = new \CURLFile(realpath(substr($val, 1)));
                    }
                }
            }
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        //dump($strPOST);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 发送模板消息
     * @param array $data 消息结构
     * @return boolean|array
     */
    public static function sendTemplateMessage($data)
    {
        $url    = sprintf(self::SEND_TEMPLATE_URL,self::$access_token);
        $result = self::http_post($url, is_array($data) ? self::json_encode($data) : $data);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return $result;
            }
            return $json;
        }
        return $result;
    }
    /**
     * 模板消息 添加消息模板
     * 成功返回消息模板的调用id
     * @param string $tpl_id 模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式
     * @return boolean|string
     */
    public static function addTemplateMessage($tpl_id){
        $data = array ('template_id_short' =>$tpl_id);
        $url  = sprintf(self::ADD_TEMPLATE_URL,self::$access_token);
        $result = self::http_post($url,is_array($data) ? self::json_encode($data) : $data);
        if($result){
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                return false;
            }
            return $json['template_id'];
        }
        return false;
    }
}