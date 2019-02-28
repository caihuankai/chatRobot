<?php

namespace app\common\model;


class RecommendLog extends ModelBs
{
    
    /**
     * 获取推荐的课程id或观点id
     *
     * @param $userId
     * @param $type
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getRecommendByUserId($userId, $type)
    {
        static $temp = [];
        if (empty($userId)) {
            return [];
        }
        
        $key = $userId . '_' . $type;
        if (!isset($temp[$key])){
            $temp[$key] = $this->where(['user_id' =>$userId, 'type' => $type])->field('DISTINCT target_id')
                ->fetchClass('\CollectionBase')->select()->column('target_id');
        }
        
        return $temp[$key];
    }
}