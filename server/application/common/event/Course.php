<?php

namespace app\common\event;


class Course
{
    use \app\common\traits\ChangeStatusEvent;
    
    public $courseIds = [];
    
    /**
     * 当前操作人
     *
     * @var int
     */
    public $operateId = 0;
    
    
    
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
        $ids = $this->courseIds;
        if (empty($ids)) {
            return [];
        }
        $ids = (array)$ids;
        
        $result = [];
    
        $statusValue = $this->getStatusValue();
        // 通知c++服务端
        \think\Hook::add('response_end', function ()use($ids, $statusValue){
            foreach ($ids as $id) {
                \CPlusPlusProtocol::instance()->callFunc('proc_CloseLiveAndCourse', $this->operateId, $id, $statusValue, 3);
            }
        });
    
        if ($statusValue != 2){
            return $result;
        }
        
        // 处理关闭消息推送
        $whereId = ['in', $ids];
        /** @var \app\common\model\Course $model */
        $model = model('Course');
        $data = $model->where(['c.id' => $whereId, $this->statusKey => 1])->alias('c')
            ->field('c.id, c.uid, c.class_name, c.pid, c.type, tl.open_id')
            ->join('user u', 'u.user_id = c.uid')
            ->join('third_login tl', 'tl.user_id = u.user_id')
            ->select();
        $msgClass = \PushMsgTemp::instance();
        
        foreach ($data as $val) {
            if ($val['type'] == 2){ // 系列课
                $message = "系列课【{$val['class_name']}】";
            }else if($val['type'] == 1 && $val['pid']){ // 系列课里的单节课
                $pName = $model->where(['id' => ['eq', $val['pid']]])->value('class_name', '');
                $message = "系列课【{$pName}】-【{$val['class_name']}】";
            }else{ // 普通单节课
                $message = "课程【{$val['class_name']}】";
            }
            
            $result[$val['open_id']] = new \EasyWeChat\Message\Text([
                'content' => $msgClass->getMsg(\PushMsgTemp::COURSE_FORBIDDEN, [$message]),
            ]);
        }
        
        return $result;
    }
    
    
}