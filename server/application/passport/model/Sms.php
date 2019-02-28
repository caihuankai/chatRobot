<?php
namespace app\passport\model;

use app\common\model\ModelBase;
use think\Cache;

/**
 * 发送短信
 * Class User 
 * @author scan<232832288@qq.com>
 * @package app\passport\model
 */
class Sms extends ModelBase
{
    /**
     * 验证码有效期 5分钟 -> 改成30分钟
     * @var int
     */
    private static $TIME = 1800;
    /**
     * 验证码长度 6位
     * @var int
     */
    static $NUM = 4;
    /**
     * 验证码文字
     * @var string
     */
    private static $REGISTER_TXT = "[摩聚科技]%s（注册验证码），请在30分钟内填写";
    
    private static $LOGIN_TXT = "[摩聚科技]%s（登录验证码），请在30分钟内填写";
    
    
    // "[魔方]%s（验证码），请在5分钟内填写"
    /**
     * 注册文字
     * @var string
     */
    private static $REGISTER = "[摩聚科技]感谢您的注册，账号：%s，默认密码：%u，该账号可以登录大咖社网页版。";
    // "[大决策]感谢您的注册，账号：%s"
    /**
     * 初始密码
     * @var int
     */
    public static $PASSWORD = 888888;
    
    /**
     * 客服
     * @var string
     */
    private static $CONTACT = "400－839-8855";
    
    protected $name = 'sms';
    
    protected $connection = 'bs_db_config';
    
    public static $WHITE_LIST = [
        '18125754398', // 苹果内购
        '13640211145', // 白痴超
    ];
    
    /**
     * @var 短信用途
     * REGISTER    -  注册
     * SMS_LOGIN       -  短信登录
     * FIND_PWD    -  找回密码
     * UPDATE_PWD  -  更新密码
     * PROMOTE     -  推广
     * UPDATE_TEL     -  更换手机号
     */
    public static $enumAction = [
        'REGISTER' => 1,
        'SMS_LOGIN' => 2,
        'FIND_PWD' => 3,
        'UPDATE_PWD' => 4,
        'PROMOTE' => 5,
        'UPDATE_TEL' => 6,
        
        'WITHDRAW_SUCCESS' => 11, // 提现成功
        'WITHDRAW_FAIL' => 12, // 提现失败
    ];
    
    /**
     * 发送验证码短信
     * @param $phone
     * @return bool
     * @author: zhengkejian
     * @Date: 20161107
     * @Time: 11:13
     */
    public function send($phone, $type)
    {
        if ($phone == '') return false;
        // $sms = new \app\passport\service\Sms();
        $code = "";
        for ($i = 0; $i < self::$NUM; $i++) {
            $ran = rand(0, 9);
            $code .= $ran;
        }
        $arr = [
            '1' => self::$LOGIN_TXT
            // '2' => self::$LOGIN_TXT,
        ];
        if (!isset($arr[$type])) {
            return false;
        }
        $now = time();
        $today = strtotime(date('Y-m-d', $now));
        $ip = get_client_ip();
        $result = $this->db()->where('phone', $phone)->where('create_time', ">=", $today)->count();
        if (!in_array($phone, self::$WHITE_LIST) && $result >= 10) {
            return 11;
        }
        $result = $this->db()->where('req_ip', $ip)->where('create_time', ">=", $today)->count();
        if (!in_array($phone, self::$WHITE_LIST) && $result > 100) {
            return 12;
        }
        
        $sms = $this->_selectChannel($phone);
        // new \app\passport\service\BCloundSms();
        
        $msg = sprintf($arr[$type], $code);
        $result = $sms->send($phone, $msg, $code);
        $channel_id = $sms->channel_id;
        $taskId = $result[1];
        isset($result[2]) && $code = $result[2];
        if ($result[0]) {
            Cache::set("sms: " .$phone.'-'.$type, $code, self::$TIME);
            $status = 1;
        } else { // 失败
            $status = 4;
        }
        $data = [
            'phone' => $phone,
            'code' => $code,
            'msg' => $msg,
            'create_time' => time(),
            'req_ip' => $ip,
            'action_type' => $type,
            'status' => $status,
            'channel_id' => $sms->channel_id,
            'task_id' => $taskId,
        ];
        $this->addData($data);
        // $this->db()->insert($data);
        return $status;
    }
    
    /**
     * 提现短信通知, 
     * @param unknown $phone
     * @param string $success true 成功, false 失败
     * @return boolean|number
     */
    public function withdraw($phone, $msg, $success=true) 
    {
        if (empty($phone)) return false;
        // $sms = new \app\passport\service\Sms();
        $type = $success ? self::$enumAction['WITHDRAW_SUCCESS'] : self::$enumAction['WITHDRAW_FAIL'];
        $now = time();
        $today = strtotime(date('Y-m-d', $now));
        $ip = get_client_ip();
        $sms = $this->_selectChannel($phone);
        // new \app\passport\service\BCloundSms();
        
        $result = $sms->send($phone, $msg, 1);
        $channel_id = $sms->channel_id;
        $taskId = $result[1];
        isset($result[2]) && $code = $result[2];
        if ($result[0]) {
            $status = 1;
        } else { // 失败
            $status = 4;
        }
        $data = [
            'phone' => $phone,
            'code' => '',
            'msg' => $msg,
            'create_time' => time(),
            'req_ip' => $ip,
            'action_type' => $type,
            'status' => $status,
            'channel_id' => $sms->channel_id,
            'task_id' => $taskId,
        ];
        $this->addData($data);
        // $this->db()->insert($data);
        return $status;
    }
    
    /**
     * 短信是否合法
     * @param $phone 手机号码
     * @param $code 验证码
     * @param $actionType  短信用途
     * @return object
     */
    public function verify($phone, $code, $actionType)
    {
        $rtn = false;
        $key = "sms: " .$phone.'-'.$actionType;
        if (Cache::has($key) && Cache::get($key) == $code){
            $rtn = true;
            Cache::rm($key);
            $this->db()->where(['phone' => $phone, 'code' => $code, 'action_type'=>$actionType])->update(['vail_time' => time()]);
        }
        return $rtn;
    }
    
    /**
     * 流程通过，修改短信状态
     * @param $id 短信id
     * @return int|string 是否成功
     */
    public function updateState($id)
    {
        return $this->db()->where('id', $id)->update(array('vail_time' => time(), 'status' => 1));
    }
    
    /**
     * 修改短信状态
     * @author zhengkejian
     */
    public function checkSms($phone, $code)
    {
        return $this->db()->comment('标识短信已使用')->where(['phone' => $phone, 'code' => $code])->update(array('vail_time' => time()));
    }
    
    /**
     * @param $data
     * @return int|string
     * @author Larry <sad812@163.com>
     */
    public function addData($data)
    {
        /*
        $phone = $data['phone'];
        $result = $this->db()->where('phone', $phone)->where('create_time', ">=", strtotime(date("Y-m-d")))->count();
        if ($result >= 100) {
            return \C::SMS_TEL_IS_BEYOND;;
        }
        $result = $this->db()->where('req_ip', $data['req_ip'])->where('create_time', ">=", strtotime(date("Y-m-d")))->count();
    
        if ($result > 100) {
            return \C::SMS_IP_IS_BEYOND;
        }
        */
        return $this->db()->insertGetId($data);
    }
    
    private function _selectChannel($phone) 
    {
        $str1 = substr($phone, 0, 3);
        $str2 = substr($phone, 0, 4);
        // return new \app\passport\service\BCloundSms();
        // return new \app\passport\service\JiXinTongSms();
        
        $CMCC = [ // 移动
            '134', '135', '136', '137', '138', '139','150', '151', '158', '159',
            '182', '183', '184', // 2g
            '157', '187', '188', // 3g
            '147', // 3g上网卡
            '178', // 4g
            '1705', // 虚拟运营商
        ];
        
        $TELECOM = [// 电信
            '133', '153', '180', '181', '189',
            '177',
            '1700',
        ];
        
        if (in_array($str1, $CMCC) || in_array($str2, $CMCC)) { // 移动
            return new \app\passport\service\BCloundSms();
//         } elseif (in_array($str1, $TELECOM) || in_array($str2, $TELECOM)){
//             return new \app\passport\service\NetEaseSms();
        } else {
            return new \app\passport\service\BCloundSms();
        }
    }
    
}
