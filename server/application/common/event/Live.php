<?php

namespace app\common\event;


class Live
{
    use \app\common\traits\ChangeStatusEvent;
    
    
    public $liveIds = [];
    
    /**
     * 当前操作人
     *
     * @var int
     */
    public $operateId = 0;
    
    
    protected $messageTemplate = "您的Live直播间被禁用\n您可在大策略公众号回复昵称+解禁，来申请解禁以及了解原因。";
    
    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->statusKey = 'open_status';
        $this->statusValue = [1, 2];
    }
    
    
    protected function statusDataFunc()
    {
        $ids = $this->liveIds;
        if (empty($ids)) {
            return [];
        }
        $ids = (array)$ids;
        
        $result = [];
        
        $statusValue = $this->getStatusValue();
        // 通知c++服务端
        \think\Hook::add('response_end', function ()use($ids, $statusValue){
            foreach ($ids as $id) {
                // type  1,直播间，2用户所有课程，3课程
                \CPlusPlusProtocol::instance()->callFunc('proc_CloseLiveAndCourse', $this->operateId, $id, $statusValue, 1);
            }
        });
        
        
        $whereId = ['in', $ids];
        $userIds = [];
        /** @var \app\common\model\User $userModel */
        $userModel = model('User');
        /** @var \app\admin\model\Live $liveModel */
        $liveModel = model('Live');
        $liveData = $liveModel->where(['id' => $whereId])->field('id, user_id')->select();
    
        foreach ($liveData as $item) {
            $userIds[] = $item['user_id'];
            // 更新sessionData
            $userModel->handleSessionUserDataKeyMap($item['user_id'], true);
        }
        
        if ($statusValue != 2){
            return $result;
        }
        
        // 处理关闭消息推送
        $data = $userModel->joinThirdLogin()->where(['u.user_id' => ['in', $userIds]])->field('u.alias, tl.open_id')->alias('u')->select();
        
        foreach ($data as $val) {
            $result[$val['open_id']] = new \EasyWeChat\Message\Text(['content' => sprintf($this->messageTemplate, $val['alias'])]);
        }
        
        return $result;
    }
    
}