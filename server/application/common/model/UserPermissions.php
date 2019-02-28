<?php

namespace app\common\model;

class UserPermissions extends ModelBs
{
    use \app\common\traits\MysqlTinyintModel;
    
    protected static $mysqlTinyintMap = [
        'type' => [
            1 => [
                'sense' => 'course',
                'default' => true,
            ],
            2 => [
                'sense' => 'viewpoint',
            ],
        ],
    ];
    
    
    /**。
     * 获取一个教师的被助教管理的权限列表状态
     *
     * @param     $teacherId
     * @param int $type
     * @return array
     * @author aozhuochao
     */
    public function getTeacherPermissions($teacherId, $type = 0)
    {
        if (empty($teacherId)) {
            return [];
        }
    
        if (!empty($type)) {
            $this->where(['type' => $type]);
        }
        
        return $this->where(['user_id' => ['eq', $teacherId]])->column('status', 'type');
    }
    
    
    /**
     * 获取一个老师的所有权限（被助教控制的权限）
     * key为字符串
     *
     * @param $teacherId
     * @return array
     * @author aozhuochao
     */
    public function getTeacherAllPermission($teacherId, $cacheField)
    {
        if (empty($teacherId)) {
            return [];
        }
    
        $result = [];
        $tinyint = $this->getMysqlTinyint('type');
        $list = $tinyint->getList('sense');
        $default = $tinyint->getValueByActionTrue('default');
        $tempData = $this->where(['user_id' => ['eq', $teacherId]])->column('status', 'type');
    
        foreach ($list as $type => $item) {
            $result[$item] = isset($tempData[$type]) ? $tempData[$type] : $default;
        }
    
        // 额外的直播间
        /** @var \app\wechat\model\Live $liveModel */
        $liveModel = model('live');
        $liveData = $liveModel->getLiveDataByUserIdOfCache($teacherId, $cacheField);
        $result['live'] = isset($liveData['open_status']) ? $liveData['open_status'] : $default;
    
        return $result;
    }
    
    
    
    /**
     * 检测老师权限，false为无权限
     *
     * @param int $teacherId
     * @param int|string $type
     * @return bool
     * @author aozhuochao
     */
    public function checkTeacherPermissions($teacherId, $type)
    {
        if (empty($teacherId) || empty($type)) {
            return false;
        }
        
        if ($type === 'live'){ // 直播间用talk_live.open_status
            /** @var \app\wechat\model\Live $liveModel */
            $liveModel = model('live');
            return !!$liveModel->whereOpen()->where(['user_id' => $teacherId])->field('id')->find();
        }
        
        return !$this->where(['user_id' => ['eq', $teacherId], 'type' => ['eq', $type], 'status' => 2])->field('id')->find();
    }
    
    
    
    /**
     * 更新老师的指定$type权限
     *
     * @param $teacherId
     * @param $type
     * @param $status
     * @param $operatorId
     * @return bool
     * @author aozhuochao
     */
    public function updateStatus($teacherId, $type, $status, $operatorId)
    {
        if (empty($teacherId) || empty($type) || empty($status)) {
            return false;
        }
        $data = $this->where(['user_id' => $teacherId, 'type' => $type])->field('id')->find();
        $save = ['status' => $status, 'operator_id' => $operatorId];
    
        if (empty($data)) { // 插入
            $save['user_id'] = $teacherId;
            $save['type'] = $type;
            $this->insert($save);
        }else{
            $this->update($save, ['user_id' => $teacherId, 'type' => $type]);
        }
        
        
        // 更新sessionData
        /** @var \app\common\model\User $userModel */
        $userModel = model('User');
        $userModel->handleSessionUserDataKeyMap($teacherId, true);
        
        return true;
    }
    
}