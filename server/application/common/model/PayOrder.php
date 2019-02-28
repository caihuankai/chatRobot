<?php

namespace app\common\model;


class PayOrder extends ModelBs
{
    
    
    /**
     * 根据课程id统计课程购买人数
     *
     * @param array $courseIds
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function countCourseBuy(array $courseIds)
    {
        if (empty($courseIds)) {
            return [];
        }
        
        return $this->where(['class_id' => ['in', $courseIds], 'type' => 1, 'state' => 1])->group('class_id')->field('count(id) num, class_id')
            ->fetchClass('\CollectionBase')->select()->column('num', 'class_id');
    }
    
    
    /**
     * 根据用户id和类型统计用户购买数
     *
     * @param array $userIds
     * @param int|string   $type
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function countUserBuy(array $userIds, $type = 1)
    {
        if (empty($userIds)) {
            return [];
        }
        
        $field = 'count(class_id) num, user_id';
        $column_key = 'num';
        if ($type === 'all'){
            $field = ['sum(if(`type`=1,"1","0")) courseNum', 'sum(if(`type`=2,"1","0")) viewpointNum',
                'sum(if(`type`=3,"1","0")) admireNum', 'user_id'];
            $column_key = null;
        }else{
            $this->where(['type' => $type]);
        }

        
        return $this->where(['user_id' => ['in', $userIds], 'state' => ['in', [1,3]]])
            ->group('user_id')->field($field)
            ->fetchClass('\CollectionBase')->select()->column($column_key, 'user_id');
    }
    
    
}