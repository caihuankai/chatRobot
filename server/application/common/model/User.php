<?php

namespace app\common\model;

/**
 * 前台用户表
 *
 * @package app\common\model
 */
class User extends ModelBs
{
    
    use \app\common\traits\MysqlTinyintModel;
    
    const SESSION_USER_DATA_FREEZE = 'freeze';
    const SESSION_USER_DATA_ASSISTANT = 'assistant';
    
    protected static $mysqlTinyintMap = [];
    
    protected $vipLevelRange = [0, 0.01, 100, 1000, 3000, 5000, 8000, 20000, 60000];
    
    
    /**
     * 前台用户检测是否账号关闭
     *
     * @var string
     */
    protected $weChatCheckUserStatusKey = 'weChatCheckUserStatusKey';
    
    
    /**
     * 用户数据的session的key
     *
     * @var string
     */
    protected $sessionUserDataKey = 'userData';
    
    
    /**
     * 连表third_login
     *
     * @param \app\common\model\ModelBase $model
     * @return \app\common\model\ModelBase
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function joinThirdLogin($model = null)
    {
        $model = $this->disFormalModel($model);
        $model->join('third_login tl', 'tl.user_id = u.user_id');
        
        return $model;
    }
    
    
    public function joinUserAssistantBeUserId($userId, $join = 'INNER', $on = '', $alias = 'ua')
    {
        $this->join("user_assistant {$alias}", "{$alias}.teacher_id = u.user_id and {$alias}.user_id = {$userId} {$on}", $join);
        
        return $this;
    }
    
    
    public function joinUserAssistant($alias = 'ua', $join = 'INNER')
    {
        $this->join("user_assistant {$alias}", "{$alias}.teacher_id = u.user_id", $join);
        
        return $this;
    }
    
    
    /**
     * 显示的用户条件
     *
     * @return $this
     * @author aozhuochao
     */
    public function whereShow()
    {
        $this->where(['u.freeze' => 0])->alias('u');
        
        return $this;
    }
    
    
    
    
    /**
     * 处理用户头像，微信全域名，而二次上传的会不带域名
     *
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function disUserHead($head)
    {
        if (empty($head)) {
            return url(config('USER_HEAD_ADD'), '', false, \helper\UrlSys::getWxHost());
        }
        
        return $head;
    }
    
    
    /**
     * 加密密码
     *
     * @param $password
     * @return string
     * @author aozhuochao
     */
    public function encryptPassword($password)
    {
        return md5(var_export(['encryptPassword', $password], true));
    }
    
    
    
    /**
     * 获取weChatCheckUserStatusKey对应的redis的key
     *
     * @param $userId
     * @return string
     */
    public function getWeChatCheckUserStatusKey($userId)
    {
        return self::class . ':' . $this->weChatCheckUserStatusKey . ':' . $userId;
    }
    
    /**
     * 设置或判断用户的开启和关闭状态
     * true为开启
     *
     * @param $userId
     * @return $this
     */
    public function handleWeChatCheckUserStatus($userId, $status)
    {
        $key = $this->getWeChatCheckUserStatusKey($userId);
        $time = 7200;
    
        // 1为开启，0为关闭
        \CacheBase::set($key, $status, $time);
        
        return $this;
    }
    
    
    /**
     * @param int   $userId
     * @param array $extend
     * @return string
     */
    public function getSessionUserDataKey($userId = 0, array $extend = [])
    {
        return join(':', array_filter(array_merge([
            self::class,
            $this->sessionUserDataKey,
            $userId,
        ], $extend)));
    }
    
    
    /**
     * 用户的sessionUserData版本时间
     *
     * @return string|bool
     */
    public function handleSessionUserDataKeyMap($userId, $set = null)
    {
        $rootKey = __METHOD__;
        $redis = \CacheBase::redis();
        if (!is_null($set)) { // 设置
            $redis->hSet($rootKey, $userId, $set === true ? time() : $set);
            
            return true;
        }
        
        // 返回key
        return $redis->hGet($rootKey, $userId);
    }
    
    
    public function checkSessionUserStatus($userId, $extendKey = null)
    {
        $updateTime = $this->handleSessionUserDataKeyMap($userId);
        $func = function ($sessionKey)use($updateTime){
            $sessionTime = \think\Session::get($sessionKey);
            if ($sessionTime < $updateTime) { // 更新用户状态
                \think\Session::set($sessionKey, $updateTime);
                return true;
            }
            
            return false;
        };
        
        if ($updateTime){
            $defaultSessionKey = __METHOD__;
            $bool = $func($defaultSessionKey);
            if (!is_null($extendKey)) { // 额外检测时，返回额外检测的
                return $func($defaultSessionKey . ':'. $extendKey);
            }
            
            return $bool;
        }
        
        return false;
    }
    

    
    
    
    
    /**
     * 获取流量主数据
     *
     * @param array $userId
     * @param       $field
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getFlowUserData(array $userId, $field)
    {
        if (empty($userId)) {
            return [];
        }
        
        return $this->where(['u.user_id' => ['in', $userId]])->field($field)->alias('u')->join('apply a', 'a.type_id = u.user_id and a.type = 1 and a.status = 2')
            ->fetchClass('\CollectionBase')->select()->column(null, 'user_id');
    }
    
    
    /**
     * 获取user表和third_login的数据
     *
     * @param array $userIds
     * @param       $field
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getUserDataJoinOpenId(array $userIds, $field)
    {
        if (empty($userIds)) {
            return [];
        }
        
        return $this->joinThirdLogin()->where(['u.user_id' => ['in', $userIds]])->alias('u')->field($field)
            ->fetchClass('\CollectionBase')->select()->column(null, 'user_id');
    }
    
    
    
    /**
     * column获取用户的数据
     *
     * @param array  $userIds
     * @param string $field 如果field只有两个字段，则value不是数组
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getUserColumn(array $userIds, $field = 'alias')
    {
        if (empty($userIds)) {
            return [];
        }
        
        return $this->where(['user_id' => ['in', $userIds]])->column($field, 'user_id');
    }
    
    /**
     * 检查用户禁用情况。已禁用返回true，否则返回false。
     * @param unknown $userId
     * @return boolean
     */
    public function checkUserFreeze($userId)
    {
    	if(empty($userId)) {
    		return true;
    	}
    	
    	return !$this->where('user_id', $userId)->where('freeze', 0)->field('user_id')->find();
    }
    
    /**
     * @return array
     */
    public function getVipLevelRange()
    {
        return $this->vipLevelRange;
    }
    
}