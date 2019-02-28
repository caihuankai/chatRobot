<?php
namespace app\common\model;


class ThirdLogin extends ModelBs
{
    protected $name = 'third_login';
    
    
    /**
     * 获取用户的open_id
     *
     * @param array $userId
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getUserOpenId(array $userId)
    {
        if (empty($userId)) {
            return [];
        }
        
        return $this->where(['user_id' => ['in', $userId]])->column('open_id', 'user_id');
    }
    
    
}