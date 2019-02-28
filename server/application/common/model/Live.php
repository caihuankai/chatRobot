<?php

namespace app\common\model;


class Live extends \app\common\model\ModelBs
{
    
    /**
     * 开启的条件
     *
     * @param string $alias
     * @param        $model
     * @return self|mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function whereOpen($alias = '', $model = null)
    {
        return $this->disFormalModel($model)->where([$alias ? $alias . '.open_status' : 'open_status' => 1]);
    }
    
    
    
    public function joinCourse($openStatus = 0)
    {
        $on = empty($openStatus)?'':" and c.open_status = {$openStatus}";
        
        $this->join('course c', "l.id = c.live_id and c.status <> 5 {$on}");
        
        return $this;
    }
    
    
    
    public function joinUser()
    {
        $this->join('user u', 'u.user_id = l.user_id');
        
        return $this;
    }
    
    

    /**
     * 获取直播间
     * @author xiaok
     * @time 2017/06/13
     */
    public function isExist($live_id){
        $where['id'] = $live_id;
        $data = $this->where($where)->find();
        return $data;
    }
    
    
    /**
     * 根据直播间id获取直播间名称
     *
     * @param array  $liveIds
     * @param string $field
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getLiveName(array $liveIds, $field = 'name')
    {
        if (empty($liveIds)) {
            return [];
        }
        
        return $this->where(['id' => ['in', $liveIds]])->column($field, 'id');
    }
    
    
    /**
     * 根据用户id获取用户的直播间
     *
     * @param        $userId
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getLiveDataByUserId($userId, $field = 'id')
    {
        if (empty($userId)) {
            return [];
        }
        
        return $this->where(['user_id' => $userId])->field($field)->find();
    }
    
    
    /**
     * 上面方法加了cache功能
     *
     * @param        $userId
     * @param string $field
     * @return mixed
     * @author aozhuochao
     */
    public function getLiveDataByUserIdOfCache($userId, $field = 'id')
    {
        static $temp = [];
    
        $key = md5(var_export([$userId, $field], true));
        if (!isset($temp[$key])) {
            $temp[$key] = $this->getLiveDataByUserId($userId, $field);
        }
        
        return $temp[$key];
    }
    
    
    
    /**
     * 根据用户id获取用户的直播间
     *
     * @param        $userIds
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getLiveDataByUserIds(array $userIds, $field = 'user_id', $status = 1)
    {
        if (empty($userIds)) {
            return [];
        }
        $where = ['user_id' => ['in', $userIds]];
    
        if (!empty($status)) {
            $where['open_status'] = $status;
        }
        
        return $this->where($where)->column($field, 'user_id');
    }

    /**
     * 更新房间被赞赏次数和赞赏总额
     * @param  [type] $amount 赞善金额
     * @return [type]         [description]
     */
    public function upLiveAdmire($user_id,$amount){
        $this->startTrans();
        try{
            $this->where('user_id',$user_id)->setInc('admire_num');
            $this->where('user_id',$user_id)->setInc('admire_amount',$amount);
            $this->commit();
        }catch(Exception $e){
            $this->rollback();
        }
    }
    
    
    
    /**
     * 改变直播间状态，默认屏蔽
     *
     * @param     $ids
     * @param int $status
     * @return $this
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function closeStatus($ids, $status = 2, $operateId = 0)
    {
        $ids = array_filter((array)$ids);
        if (empty($ids)) {
            return $this;
        }
        
        /** @var \app\admin\event\Live $event */
        $event = $this->getLocalEvent();
        if (!is_null($event)) {
            $event->liveIds = $ids;
            $event->operateId = $operateId;
        }
        
        return $this->update(['open_status' => $status], ['id' => ['in', $ids]]);
    }
    
}