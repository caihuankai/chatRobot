<?php

namespace app\passport\service;

/**
 * 网易接口
 */
class NetEaseSms
{
    const SENDURL = 'https://api.netease.im/sms/sendcode.action';
    
    /*
    const QUERYURL = 'http://sms.253.com/bi';
    const ISENDURL = 'http://222.73.117.140:8044/mt';
    const IQUERYURL = 'http://222.73.117.140:8044/bi';
    */
    
    private $_appkey = 'a71419ab37939683346e750d9754f902'; 
    private $_aapsecret = '0a47a35310d2';
    private $_templateid = '3052147';
    
    public $channel_id = 4;
 // 密码
    
    public function send($phone, $content, $code)
    {
        // $content = urlencode(bin2hex(iconv('UTF-8', 'GBK', $content)));
        //return [false, 0];
        /*
        $data = [
            'templateid' => $this->_templateid,
            'mobiles' => json_encode([$phone]),
            'params' => json_encode([$code]),
        ];
        */
        // $str = '';
        // http_build_query($data)
        /*
        $str = "templateid={$this->_templateid}";
        $str.= '&mobiles='.json_encode([$phone]); 
        $str.= '&params='.json_encode([$code]);
        */
        $str = sprintf('mobile=%s&templateid=%s&codeLen=%s', $phone, $this->_templateid, 6);
        return $this->_parseData($this->_request(self::SENDURL, $str));
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
    private function _request($url, $data)
    {
        $ch = curl_init();
        $header = [
            // 'Content-Type' => 'application/x-www-form-urlencoded',
            'charset' => 'utf-8',
            'AppKey' => $this->_appkey,
            'Nonce' => strtolower(str_replace('-', '', guid())),
            'CurTime' => time().'',
        ];
        $sha1 = sha1($this->_aapsecret.$header['Nonce'].$header['CurTime'], FALSE);
        $header['CheckSum'] = $sha1;
        
        $h = [];
        foreach ($header as $key => $row){
            $h[] = $key.': '. $row;
        }
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
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
        if (!empty($result)){
            $data = json_decode($result, true);
            if (!empty($data) && $data['code']=='200') {
                $bool = true;
                $taskId = $data['msg'];
                $code = $data['obj'];
            }
        }
        $rtn = [$bool, $taskId];
        isset($code) && $rtn[2] = $code;
        return $rtn;
    }
}