<?php
namespace app\common\controller;

use think\Config;
use think\Controller;

/**
 * 控制器基类
 * @author scan<232832288@qq.com>
 *
 * @property \app\common\model\User $User
 * @property \app\common\model\Member $Member
 * @property \app\common\model\UserPhoto $UserPhoto
 * @property \app\common\model\Bill $Bill
 * @property \app\common\model\Useraccount $Useraccount
 * @property \app\common\model\GroupMember $GroupMember
 * @property \app\common\model\Authentication $Authentication 
 * @property \app\common\model\PayOrder $PayOrder
 * @property \app\common\model\WithdrawDeposit $WithdrawDeposit
 * @property \app\common\model\Errmsg $Errmsg
 * @property \app\common\model\AppVersion $AppVersion
 * @property \app\common\model\UseraccountLog $UseraccountLog 
 * @property \app\common\model\UserGift $UserGift 
 * 
 */

class ControllerBase extends Controller
{
    
    use \traits\validate,
        \traits\sysJson;
    
    protected function _initialize()
    {
        // init
    }
    
    public function __get($name) 
    {
        return model($name);
    }
    
    public function __set($name, $value) 
    {
        $this->$name = $value;
    }
    /**
     * 接口成功返回
     * @param string $data 返回信息
     */
    public function successJson($data)
    {
        $json = array();
        $json['data'] = $data;
        $json['code'] = 0;
        $json['msg'] = null;
        return json($json, 200);
    }

    /**
     * 接口成功返回
     * @param string $data 返回信息
     */
    public function suJson($data)
    {
        $json = array();
        $json['data'] = null;
        $json['code'] = 0;
        $json['msg'] = $data;
        return json($json, 200);
    }

    /**
     * 接口成功返回
     * @param string $data 返回信息
     */
    public function successJsonp($data)
    {
        $json = array();
        $json['data'] = $data;
        $json['code'] = 0;
        $json['msg'] = null;
        return jsonp($json, 200);
    }

    /**
     * 接口失败返回
     * @param int $code 返回信息码
     */
    protected function errorJson($code)
    {
        $json = array();
        $json['data'] = null;
        $json['code'] = $code;
        $json['msg'] = JsonResult::$jsonMsg[$code];
        return json($json, 200);
    }
}
