<?php
namespace app\passport\model;

use app\common\model\ModelBase;

/**
 * 号码登录
 * Class User 
 * @author scan<232832288@qq.com>
 * @package app\passport\model
 */
class Login extends ModelBase
{
    protected $name = 'login';
    
    protected $connection = 'bs_db_config';
    
    public function checkPassword($rs, $psw) 
    {
        if ($rs['user_pwd'] != md5($psw)) {
            return false;
        }
        return true;
    }
    
}
