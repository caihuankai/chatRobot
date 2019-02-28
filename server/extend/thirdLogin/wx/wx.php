<?php
namespace thirdLogin\wx;

use think\Cache;
use think\Config;
use think\Log;

class wx
{
    function __construct($appId = '',$appSecret = '')
    {
        if(empty($appId)){
            $this->appid = Config::get('OAUTH.WX_APP_ID');
        }else{
            $this->appid = $appId;
        }
        if(empty($appSecret)){
            $this->appsecret = Config::get('OAUTH.WX_APP_SECRET');
        }else{
            $this->appsecret = $appSecret;
        }

        $this->access_token = '';
        $this->openid = '';
    }

    /**
     * 生成获取code的url地址
     * @param $redirectUrl
     * @param string $state
     * @return string
     */
    public function getCodeUrl($redirectUrl , $state = ''){
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
        $state = empty($state) ? md5(time()) : $state;
        $params = [
            'appid' => $this->appid,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'scope'=> 'snsapi_userinfo',
            'state' => $state
        ];
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取access_token
     * @return mixed|null
     */
    private function getAccessTokenByJS(){
        //https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET
        $accessToken = Cache::get('wx_access_token');
        if(empty($accessToken)){
            $params = [
                'grant_type' => 'client_credential',
                'appid' => $this->appid,
                'secret' => $this->appsecret
            ];

            $url = 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($params);
            $result = $this->http($url);
            $result = json_decode($result , true);
            if (empty($result['access_token'])) {
                Log::write('wx = > ' . json_encode($result), 'oauth');
                return null;
            }
            $accessToken = $result['access_token'];
            Cache::set('wx_access_token',$accessToken,7200);
        }

        return $accessToken;
    }

    /**
     * 获取jsapi_ticket，jsapi_ticket是公众号用于调用微信JS接口的临时票据
     * @param $accessToken
     * @return mixed|null
     */
    private function get_jsapi_ticket($accessToken){
        //https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
        $ticket = Cache::get('wx_jsapi_ticket');
        if(empty($ticket)){
            $params = [
                'access_token' => $accessToken,
                'type' => 'jsapi'
            ];

            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?' . http_build_query($params);
            $result = $this->http($url);
            $result = json_decode($result , true);
            if (empty($result['ticket'])) {
                Log::write('wx = > ' . json_encode($result), 'oauth');
                return null;
            }
            $ticket = $result['ticket'];
            Cache::set('wx_jsapi_ticket',$result['ticket'],7200);
        }

        return $ticket;
    }

    /**
     * 获取签名信息
     * @return array
     */
    public function getSignPackage() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        $accessToken = $this->getAccessTokenByJS();
        $jsapi_ticket = $this->get_jsapi_ticket($accessToken);

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    function getAccessToken($code)
    {
        $params = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
        );
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($params);
        $result = $this->http($url);
        $result = json_decode($result , true);
        if (empty($result['access_token'])) {
            Log::write('wx = > ' . json_encode($result), 'oauth');
            return null;
        }
        $this->access_token = $result['access_token'];
        $this->openid = $result['openid'];
        return $this->access_token;
    }

    function getUserInfo()
    {
        $params = array(
            'openid' => $this->openid,
            'access_token' => $this->access_token,
//            'appid' => $this->appid,
//            'secret' => $this->appsecret
        );
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($params);
//        var_dump($url);
        $result = $this->http($url);
        $result = json_decode($result, true);
        if (!empty($result['errcode'])) {
            Log::write('wx = > ' . json_encode($result), 'oauth');
            return null;
        }
        return $result;
    }

    function api($url, $params, $method = 'GET')
    {
        $params['access_token'] = $this->access_token;
        $params['secret'] = $this->appid;
        $params['format'] = 'json';
        if ($method == 'GET') {
            $result_str = $this->http($url . '?' . http_build_query($params));
        } else {
            $result_str = $this->http($url, http_build_query($params), 'POST');
        }
        $result = array();
        if ($result_str != '') {
            $result = json_decode($result_str, true);
        }
        return $result;
    }

    function http($url, $postfields = '', $method = 'GET', $headers = array())
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        if ($method == 'POST') {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if ($postfields != '') curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        $headers[] = "User-Agent: curl/7.15.5";
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}