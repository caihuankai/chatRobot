<?php

namespace app\passport\service;

/**
 * 吉信通短信接口
 */
class JiXinTongSms
{
    const SENDURL = 'http://service.winic.org:8009/sys_port/gateway/?';
    
    /*
    const QUERYURL = 'http://sms.253.com/bi';
    const ISENDURL = 'http://222.73.117.140:8044/mt';
    const IQUERYURL = 'http://222.73.117.140:8044/bi';
    */
    
    private $_sendUrl = ''; // 发送短信接口url
    private $_queryBalanceUrl = ''; // 查询余额接口url
    
    private $_un = "李宜达"; // 账号
    private $_pw = '3EDC4RFV';
    
    public $channel_id = 3;
 // 密码
    
    public function send($phone, $content, $isreport = 1)
    {
        // $content = urlencode(bin2hex(iconv('UTF-8', 'GBK', $content)));
        //return [false, 0];
        
        $data = "id=%s&pwd=%s&to=%s&content=%s&time=";
        $id = iconv('UTF-8', 'GB2312', $this->_un);
        $pwd = $this->_pw;
        $content = urlencode(iconv("UTF-8","GB2312",$content));
        $data = sprintf($data, $id, $pwd, $phone, $content);
        return $this->_parseData($this->_request(self::SENDURL, $data));
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
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //打印一下参数 可以看到 在GB2312编码模式的浏览器下 显示字符是正常的
        $result = curl_exec($ch);
        curl_close($ch);
        $result = iconv('GB2312', 'UTF-8', $result);
        return $result;
    }
    /* ========== 功能模块 ========== */
    
    private function _parseData($result)
    {
        $taskId = '';
        $bool = false;
        $status = substr($result,0,3);
        if ($status=='000') {
            $bool = true;
            $arr = explode('/', $result);
            foreach ($arr as $a){
                if (!strpos($a, ':')) continue;
                
                list($key, $val) = explode(':', $a);
                if ($key == 'sid'){
                    $taskId = $val;
                    break;
                }
            }
        }
        return [$bool, $taskId];
    }
}