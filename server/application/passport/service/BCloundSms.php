<?php

namespace app\passport\service;

/**
 * BClound短信接口
 */
class BCloundSms
{
    const SENDURL = 'http://120.26.65.203:8800/msg/HttpSendSM';
    
    /*
    const QUERYURL = 'http://sms.253.com/bi';
    const ISENDURL = 'http://222.73.117.140:8044/mt';
    const IQUERYURL = 'http://222.73.117.140:8044/bi';
    */
    
    private $_sendUrl = ''; // 发送短信接口url
    private $_queryBalanceUrl = ''; // 查询余额接口url
    
    private $_un = "guangzhoumj"; // 账号
    private $_pw = 'mC7731smS';
    private $_product = '458760074';
    
    public $channel_id = 1;
 // 密码
    
    public function send($phone, $content, $isreport = 1)
    {
        // $content = urlencode(bin2hex(iconv('UTF-8', 'GBK', $content)));
        //return [false, 0];
        
        $requestData = array(
            'account' => $this->_un,
            'pswd' => $this->_pw,
            'mobile' => $phone,
            'msg' => $content,
            'needstatus' => true,
            'product' => $this->_product,
        );
        //http://120.26.65.203:8800/msg/HttpSendSM?account=guangzhoumj&pswd=mC7731smS&mobile=13430398355&msg=test&needstatus=true&product=458760074
        $url = self::SENDURL . '?' . http_build_query($requestData);
        return $this->_parseData($this->_request($url));
    }

    /**
     * 国际短信发送
     * 
     * @param string $phone
     *            手机号码
     * @param string $content
     *            短信内容
     * @param integer $isreport
     *            是否需要状态报告
     * @return void
     */
//     public function sendInternational($phone, $content, $isreport = 0)
//     {
//         $requestData = array(
//             'un' => $this->_un,
//             'pw' => $this->_pw,
//             'sm' => $content,
//             'da' => $phone,
//             'rd' => $isreport,
//             'rf' => 2,
//             'tf' => 3 
//         );
        
//         $url = self::ISENDURL . '?' . http_build_query($requestData);
//         return $this->_request($url);
//     }

    /**
     * 查询余额
     * 
     * @return String 余额返回
     */
//     public function queryBalance()
//     {
//         $requestData = array(
//             'un' => $this->_un,
//             'pw' => $this->_pw,
//             'rf' => 2 
//         );
        
//         $url = self::QUERYURL . '?' . http_build_query($requestData);
//         return $this->_request($url);
//     }

    /**
     * 查询余额
     * 
     * @return String 余额返回
     */
//     public function queryBalanceInternational()
//     {
//         $requestData = array(
//             'un' => $this->_un,
//             'pw' => $this->_pw,
//             'rf' => 2 
//         );
        
//         $url = self::IQUERYURL . '?' . http_build_query($requestData);
//         return $this->_request($url);
//     }

    /* ========== 业务模块 ========== */
    
    /* ========== 功能模块 ========== */
    /**
     * 请求发送
     * 
     * @return string 返回状态报告
     */
    private function _request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    /* ========== 功能模块 ========== */
    
    private function _parseData($result)
    {
        $taskId = '';
        $bool = false;
        if (strpos($result, ',') !== false) {
            if (strpos($result, "\n") !== false){
                list($rtn, $taskId) = explode("\n", $result);
            } else {
                list($rtn) = explode("\n", $result);
            }
            list(, $c) = explode(',', $rtn);
            if ($c == 0) {
                $bool = true;
            }
        }
        return [$bool, $taskId];
    }
}