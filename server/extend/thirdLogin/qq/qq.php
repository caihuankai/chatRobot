<?php
namespace thirdLogin\qq;

use think\Config;
use think\Log;

/**
 * PHP Library for qq.com
 */
class qq
{
    function __construct($appId = '',$appKey = '')
    {
        if(empty($appId)){
            $this->appid = Config::get('OAUTH.QQ_APP_ID');
        }else{
            $this->appid = $appId;
        }
        if(empty($appKey)){
            $this->appkey = Config::get('OAUTH.QQ_APP_KEY');
        }else{
            $this->appkey = $appKey;
        }

        $this->access_token = '';
    }

    function login_url($callback_url, $scope = '', $state = '')
    {
        $params = array(
            'client_id' => $this->appid,
            'redirect_uri' => $callback_url,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        );
        return 'https://graph.qq.com/oauth2.0/authorize?' . http_build_query($params);
    }

    function access_token($callback_url, $code)
    {
        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'client_secret' => $this->appkey,
            'code' => $code,
            'state' => '',
            'redirect_uri' => $callback_url
        );
        $url = 'https://graph.qq.com/oauth2.0/token?' . http_build_query($params);
        var_dump($url);
        $result_str = $this->http($url);
        $json_r = array();
        if ($result_str != '') {
            parse_str($result_str, $json_r);
            if (!empty($json_r['access_token'])) {
                $this->access_token = $json_r['access_token'];
            }
            Log::write('qq = > ' . json_encode($json_r), 'oauth');
        }
        return $json_r;
    }

    /**
     * function access_token_refresh($refresh_token){
     * }
     **/

    function get_openid()
    {
        $params = array(
            'access_token' => $this->access_token
        );
        $url = 'https://graph.qq.com/oauth2.0/me?' . http_build_query($params);
        $result_str = $this->http($url);
        $json_r = array();
        if ($result_str != '') {
            preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_a);
            $json_r = json_decode($result_a[1], true);
            if (!empty($json_r['error'])) {
                Log::write('qq = >' . json_encode($json_r), 'oauth');
                return null;
            }
        }
        return $json_r;
    }

    function get_user_info($openid)
    {
        $params = array(
            'openid' => $openid
        );
        $url = 'https://graph.qq.com/user/get_user_info';
        return $this->api($url, $params);
    }

    function add_share($openid, $title, $url, $site, $fromurl, $images = '', $summary = '')
    {
        $params = array(
            'openid' => $openid,
            'title' => $title,
            'url' => $url,
            'site' => $site,
            'fromurl' => $fromurl,
            'images' => $images,
            'summary' => $summary
        );
        $url = 'https://graph.qq.com/share/add_share';
        return $this->api($url, $params, 'POST');
    }

    function api($url, $params, $method = 'GET')
    {
        $params['access_token'] = $this->access_token;
        $params['oauth_consumer_key'] = $this->appid;
        $params['format'] = 'json';
        if ($method == 'GET') {
            $result_str = $this->http($url . '?' . http_build_query($params));
        } else {
            $result_str = $this->http($url, http_build_query($params), 'POST');
        }
        $result = array();
        if ($result_str != '') $result = json_decode($result_str, true);
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