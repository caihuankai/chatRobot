<?php

namespace app\common\model;


class UserAddr extends ModelBs
{
    
    
    /**
     * 获取用户地址
     *
     * @param array $userIds
     * @return false|\PDOStatement|string|\think\Collection
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getUserAddr(array $userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        
        return $this->where(['user_id' => ['in', $userIds]])->column('country, province, city, merger_name', 'user_id');
    }
    
}