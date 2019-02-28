<?php

namespace app\passport\service;

/**
 * 未知短信接口
 */
class HunanSms
{
    /**
     * 发送接口地址
     */
    const SENDURL = 'http://42.121.98.132:8888/sms.aspx';
    /**
     * 余额及已发送量查询接口
     */
    const QUERYURL = 'http://42.121.98.132:8888/sms.aspx';
    /**
     * 敏感词查询
     */
    const SENSITIVEURL = 'http://42.121.98.132:8888/sms.aspx';
    /**
     * 状态报告接口
     */
    const STATUSREPORTURL = 'http://42.121.98.132:8888/statusApi.aspx';
    
    const ISENDURL = 'http://222.73.117.140:8044/mt';
    const IQUERYURL = 'http://222.73.117.140:8044/bi';
    
    private $_sendUrl = ''; // 发送短信接口url
    private $_queryBalanceUrl = ''; // 查询余额接口url
    
    private $_un = "mjkj"; // 账号
    private $_pw = 'sk123456';
    private $_uid = 641;
    
    public $channel_id = 2;
 // 密码
    
    /**
     * 构造方法
     * 
     * @param string $account
     *            接口账号
     * @param string $password
     *            接口密码
     */
    // public function __construct($account,$password){
    // $this->_un=$account;
    // $this->_pw=$password;
    // }
    
    /* ========== 业务模块 ========== */
    /**
     * 短信发送
     * 
     * @param string $phone
     *            手机号码
     * @param string $content
     *            短信内容
     * @param integer $isreport
     *            是否需要状态报告
     * @return void
     */
    public function send($phone, $content, $isreport = 1)
    {
        // $content = urlencode(bin2hex(iconv('UTF-8', 'GBK', $content)));
        $para = [
            'action' => 'send',
            'userid' => $this->_uid,
            'account' => $this->_un,
            'password' => $this->_pw,
            'mobile' => $phone,
            'content' => $content,
            'sendTime' => '',
            'extno' => ''
        ];
        return $this->_request(self::SENDURL, $para);
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
    /*
    public function sendInternational($phone, $content, $isreport = 0)
    {
        $requestData = array(
            'un' => $this->_un,
            'pw' => $this->_pw,
            'sm' => $content,
            'da' => $phone,
            'rd' => $isreport,
            'rf' => 2,
            'tf' => 3 
        );
        
        $url = self::ISENDURL . '?' . http_build_query($requestData);
        return $this->_request($url);
    }
    */
    
    /**
     * 查询余额
     * 
     * @return String 余额返回
     */
    /*
    public function queryBalance()
    {
        $requestData = array(
            'un' => $this->_un,
            'pw' => $this->_pw,
            'rf' => 2 
        );
        
        $url = self::QUERYURL . '?' . http_build_query($requestData);
        return $this->_request($url);
    }
    */
    /**
     * 查询余额
     * 
     * @return String 余额返回
     */
    /*
    public function queryBalanceInternational()
    {
        $requestData = array(
            'un' => $this->_un,
            'pw' => $this->_pw,
            'rf' => 2 
        );
        
        $url = self::IQUERYURL . '?' . http_build_query($requestData);
        return $this->_request($url);
    }
    */
    /* ========== 业务模块 ========== */
    
    /* ========== 功能模块 ========== */
    /**
     * 请求发送
     * 
     * @return string 返回状态报告
     */
    private function _request($url, $para)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true); // post传输数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $para);// post传输数据
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        if (empty($result)) {
            return false;
        }
        return $this->_parseData($result);
    }
    /* ========== 功能模块 ========== */
    
    private function _parseData($result) 
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $result, $vals, $index);
        xml_parser_free($p);
        print_r($vals);
        $bool = false;
        $taskId = '';
        if (!empty($vals)) {
            foreach ($vals as $v){
                if (strtoupper($v['tag']) == 'RETURNSTATUS'){
                    if (strtolower($v['value']) == 'success'){
                        $bool = true;
                    }
                } elseif (strtoupper($v['tag']) == 'TASKID'){
                    $taskId = $v['value'];
                }
            }
        }
        return [$bool, $taskId];
    }
}