<?php
namespace app\passport\controller;

use think\Request;
use think\Validate;
use app\passport\model\Sms;
use app\passport\model\Login as loginModel;

class Login extends App
{
    /**
     * 登录
     */
    /*
    public function smsLogin() 
    {
        $tel = $this->request->param("tel", 0);
        $code = $this->request->param("code", '');
        if (!$this->_isPhone($tel)){
            return $this->erret(\C::TEL_ERROR);
        }
        $login = new \app\passport\model\Login();
        if (!(new Sms())->verify($tel, $code, Sms::$enumAction['SMS_LOGIN'])){
            return $this->erret(\C::VERIFY_ERROR);
        }
        $info = (new loginModel())->get(['tel'=>$tel]);
        if (empty($info)) {
            return $this->erret(\C::USER_UNREGISTER);
        }
        $member = $this->Member->get($info['member_id']);
        return $this->ret(['member'=>\Protocol::memberInfo($member)]);
    }
    */
    
    /**
     * 注册表格
     */
    public function registerForm() 
    {
        ;
    }
    
    /**
     * 密码登录
     */
    public function doLogin() 
    {
        $tel = $this->request->param("tel", 0);
        $password = $this->request->param("password", '');
        if (!$this->_isPhone($tel)){
            return $this->erret(\C::TEL_ERROR);
        }
        $len = mb_strlen($password);
        if ($len < 6 || $len > 20) {
            return $this->erret(\C::PASSWORD_LENGTH_FAILD);
        }
        $login = new \app\passport\model\Login();
        $loginRs = $login->get(['tel'=>$tel]);
        if (empty($loginRs)) {
            return $this->erret(\C::USER_UNREGISTER);
        }
        if (empty($loginRs['user_pwd'])) {
            return $this->erret(\C::PASSWORD_NO_SETED);
        }
        $rs = $login->checkPassword($loginRs, $password);
        if (!$rs) {
            return $this->erret(\C::PASSWORD_FAILD);
        }
        $ret = $this->_logined($loginRs);
        if ($ret[0] != \C::SUCCESS){
            return $this->erret($ret[0]);
        }
        return $this->ret($ret[1]);
    }
    
    /**
     * 账号密码注册
     */
    public function doRegister() 
    {
        $tel = $this->request->param("tel", 0);
        $code = $this->request->param("code", '');
        $password = $this->request->param("password", '');
        if (!$this->_isPhone($tel)){
            return $this->erret(\C::TEL_ERROR);
        }
        $sms = new Sms();
        if (config('SMS_CHECK_OPEN') == 1 && strlen($code) != $sms::$NUM) {
            return $this->erret(\C::VERIFY_ERROR);
        }
        
        if (config('SMS_CHECK_OPEN') == 1 && !($sms->verify($tel, $code, Sms::$enumAction['REGISTER']))){
            return $this->erret(\C::VERIFY_ERROR);
        }
        
        $login = new \app\passport\model\Login();
        $loginRs = $login->get(['tel'=>$tel]);
        if (!empty($loginRs)) {
            return \C::TEL_REGISTERED;
        }
        $ret = $this->_logined($loginRs);
        if ($ret[0] != \C::SUCCESS){
            return $this->erret($ret[0]);
        }
        return $this->ret($ret[1]);
    }
    
    /**
     * 验证码自动登录注册
     */
    public function register() 
    {
        $tel = $this->request->param("tel", 0);
        $code = $this->request->param("code", '');
        if (!$this->_isPhone($tel)){
            return $this->erret(\C::TEL_ERROR);
        }
        $sms = new Sms();
        if (config('SMS_CHECK_OPEN') == 1 && strlen($code) != $sms::$NUM) {
            return $this->erret(\C::VERIFY_ERROR);
        }
        
        if (config('SMS_CHECK_OPEN') == 1){
            if(in_array($tel, $sms::$WHITE_LIST) && $code == '3414'){

            }else{
                if(!($sms->verify($tel, $code, Sms::$enumAction['REGISTER']))){
                    return $this->erret(\C::VERIFY_ERROR);
                }
            }
        }
        
        $login = new \app\passport\model\Login();
        $loginRs = $login->get(['tel'=>$tel]);
        $ret = $this->_logined($loginRs, $login, $tel);
        if ($ret[0] != \C::SUCCESS){
            return $this->erret($ret[0]);
        }
        return $this->ret($ret[1]);
    }
    
    /**
     * 登录
     * @param unknown $loginRs
     * @return number[]|number[]|number[][]|stdClass[][]|unknown[][]|mixed[][]|void[][]|boolean[][]|NULL[][]
     */
    private function _logined($loginRs, $login=false, $tel=false) 
    {
        if (empty($loginRs)){ 
            // return $this->erret(\C::TEL_REGISTERED);
            $info = $this->Member->register();
            if (!$info) {
                return [\C::ERR_FAIL];
            }
            $member_id = $info['id'];
            $data = [
                'tel' => $tel,
                'member_id' => $member_id,
            ];
            $login->create($data);
        } else {
            $info = $this->Member->get($loginRs['member_id']);
            if (empty($info)) {
                $info = $this->Member->register();
            }
            if (!$info) {
                return [\C::ERR_FAIL];
            }
            $info = $this->Member->logined($info); 
        }
        if ($info['is_multi']==0 && $info['role_id']==0) {
            $type = 0;
        } elseif ($info['is_multi']==0 && $info['role_id']>0){
            $type = 1;
        } else {
            $type = 2;
        }
        return [\C::SUCCESS, [
            'member'=>\Protocol::memberInfo($info), 
            'type'=>$type,
            'TCP_DB_HOSTNAME' => config('TCP_DB_HOST'),
        ]];
    }
    
    
    /**
     * 登出
     */
    public function logout() 
    {
        ;
    }
    
    /**
     * 验证码
     */
    public function checkCode()
    {
        $tel = $this->request->param('tel', '', 'trim');
        $actionType = $this->request->param('actionType', 0, 'intval');
        if (!$this->_isPhone($tel)) {
            return $this->erret(\C::TEL_ERROR);
        }
        // 验证码类别：1-注册账号；2-手机登录；3-找回密码；4-修改密码；5-推广；6-更换手机
        /* 去除号码验证
        if (!in_array($actionType, array_values(Sms::$enumAction))) {
            return $this->erret(\C::ERR_PARAMETER);
        }
        if ($actionType == 1) { // 注册
            $login = new \app\passport\model\Login();
            if (!empty($login->get(['tel'=>$tel]))){
                return $this->erret(\C::TEL_REGISTERED);
            }
        } elseif ($actionType == 2){ // 登录
            $login = new \app\passport\model\Login();
            if (empty($login->get(['tel'=>$tel]))){
                return $this->erret(\C::USER_UNREGISTER);
            }            
        }
        */
        // $enumAction
        $rs = (new Sms())->send($tel, $actionType);
        if ($rs == 11) {
            return $this->erret(\C::SMS_TEL_IS_BEYOND);
        } elseif ($rs == 12){
            return $this->erret(\C::SMS_IP_IS_BEYOND);
        } elseif ($rs != 1){
            return $this->erret(\C::SMS_SEND_FAILD);
        }
        return $this->ret();
    }
    
    /**
     * 忘记密码
     */
    public function forgetPassword() 
    {
        ;
    }
    
    private function _isPhone($tel) 
    {
        return Validate::regex($tel, '/^((13[0-9])|(14[0-9])|(15[0-9])|(18[0-9])|(17[0-9]))[0-9]{8}$/');
    }
    
}
