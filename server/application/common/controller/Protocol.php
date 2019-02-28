<?php
/**
 * 协议
 * Class Protocol 
 * @author scan<232832288@qq.com>
 */

use app\common\model\Useraccount;
use app\common\model\UserPhoto;

class Protocol
{
    public static function _objDefault($var)
    {
        return empty($var) ? new stdClass() : $var;
    }
    
    /**
     * 用户信息
     * @param unknown $user
     * @param boolean $photos 是否查询相册 
     */
    public static function userInfo(&$user, $photoBool=true)
    {
        $accountModel = new Useraccount();
        $account = $accountModel->get(['user_id'=>$user['id']]);
        if (empty($account)) {
            $account = (object)$accountModel->register($user['id']);
        }
        if ($photoBool) {
            $photoModel = new UserPhoto();
            $photos = $photoModel->lists($user['id']);
        } else {
            $photos = [];
        }
        return self::_objDefault([
            'user_id' => $user['id'],
            'sex' => (int)$user['gender'],
            'money' => $account->gold_coin/100,
            'sweet' => $account->total_candy/100,
            'is_id_auth' => $user['identity_auth']>0 ? 1 : 0,
            'is_work_auth' => 1,
            'sign' => !empty($user['sign']) ? $user['sign'] : '',
            'birthday' => $user['birthday'],
            'age' => getAge($user['birthday']),
            'star_sign' => $user['constellation'],
            'city' => $user['city'],
            'work' => $user['work'],
            'hobby' => [$user['hobby']],
            'photos' => $photos,
            'titles' => [],
            'alias' => $user['alias'],
            'head' => $user['headaddr'],
        ]);
    }
    
    /**
     * 登录号信息
     * @param unknown $user
     */
    public static function memberInfo(&$member)
    {
        if(!empty($member)){
            $member['member_id'] = $member['id'];
            unset($member['id']);
        } 
        return self::_objDefault($member);
    }

    
}
