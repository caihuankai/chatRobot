<?php

namespace app\common\model;


class Course extends ModelBs
{
    
    use \app\common\traits\MysqlTinyintModel;
    
    protected static $mysqlTinyintMap = [
        'open_status' => [
            1 => [
                'title' => '开启',
            ],
            2 => [
                'title' => '关闭',
            ],
        ],
        'form' => [
            1 => [
                'title' => '常规直播',
            ],
            2 => [
                'title' => '视频直播',
            ],
            3 => [
                'title' => 'PPT直播',
            ],
        ],
    ];
    
    
    /**
     * 单次课程的过滤条件
     *
     * @return $this
     * @author aozhuochao
     */
    public function whereSingleCourse()
    {
        $this->where(['c.type' => 1, 'c.pid' => ['eq', 0]])->alias('c');
        
        return $this;
    }
    
    /**
     * 系列课程的过滤条件
     * @return \app\common\model\Course
     * @author liujuneng
     */
    public function whereSeriesCourse()
    {
    	$this->where(['c.type' => 2, 'c.pid' => ['eq', 0]])->alias('c');
    	
    	return $this;
    }
    
    
    
    /**
     * 显示的条件
     *
     * @return $this
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function whereShow($openStatus = 1, $status = [0, 1, 4])
    {
        $where = ['status' => ['in', $status]]; // 1未知
    
        if (!empty($openStatus)) {
            $where['open_status'] = $openStatus;
        }
        
        $this->where($where);
        
        return $this;
    }
    
    
    /**
     * 连表user
     *
     * @return $this
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function joinUser()
    {
        $this->join('user u', 'u.user_id = c.uid');
        
        return $this;
    }
    
    
    public function getOriginPriceAttr($value)
    {
        return $value > 0 ? (float)$value : '';
    }
    
    
    public function getPriceAttr($value)
    {
        return $value > 0 ? (float)$value : '';
    }
    
    
    
    /**
     * column获取课程的数据
     *
     * @param array  $courseIds
     * @param string $field
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCourseColumn(array $courseIds, $field = 'class_name')
    {
        return $this->whereShow()->where(['id' => ['in', $courseIds]])->column($field, 'id');
    }
    
    
    
    
    /**
     * 获取课程前台详情页url
     *
     * @param $courseId
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCourseUrl($courseId)
    {
        return url('/#/personalCenter/course/' . $courseId, '', false, \helper\UrlSys::getWxHost());
    }
    
    /**
     * 获取特训班前台详情页url
     * @param unknown $courseId
     * @return string
     * @author liujuneng
     */
    public function getTrainCourseUrl($courseId)
    {
    	return url('/#/course/specialOnlive/' . $courseId, '', false, \helper\UrlSys::getWxHost());
    }
    
    
    /**
     * 统计系列的课程数
     *
     * @param array $pIds
     * @param int   $openStatus
     * @return array
     * @author aozhuochao
     */
    public function getCourseNumByPIds(array $pIds, $openStatus = 1)
    {
        if (empty($pIds)) {
            return [];
        }
        
        return $this->whereShow($openStatus)->where(['pid' => ['in', $pIds]])->field('count(id) num, pid')->group('pid')
            ->fetchClass('\CollectionBase')->select()->column('num', 'pid');
    }
    
    
    
    /**
     * 根据用户id统计用户课程数
     *
     * @param array $userIds
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCourseNumByUserIds(array $userIds, $openStatus = 1, $pid = false)
    {
        if (empty($userIds)) {
            return [];
        }
        
        if ($pid !== false){
            $this->where(['pid' => $pid]);
        }
        
        return $this->whereShow($openStatus)->where(['uid' => ['in', $userIds]])->field('count(id) num, uid')->group('uid')
            ->fetchClass('\CollectionBase')->select()->column('num', 'uid');
    }
    
    
    /**
     * 根据用户id统计用户课程数
     *
     * @param array $liveIds
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCourseNumByLiveIds(array $liveIds, $field = '', $otherWhere = [], $openStatus = 1)
    {
        if (empty($liveIds)) {
            return [];
        }
    
        if (!empty($otherWhere)) {
            $this->where($otherWhere);
        }
    
        return $this->whereShow($openStatus)->where(['live_id' => ['in', $liveIds]])->field(empty($field) ? 'count(id) num, live_id' : $field)->group('live_id')
            ->fetchClass('\CollectionBase')->select()->column(empty($field) ? 'num' : null, 'live_id');
    }
    
    
    
    /**
     * 改变课程状态，默认屏蔽
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
        
        /** @var \app\admin\event\Course $event */
        $event = $this->getLocalEvent();
        if (!is_null($event)) {
            $event->courseIds = $ids;
            $event->operateId = $operateId;
        }
        
        $result = $this;
        $this->startTrans();
        try {
        	$result = $this->update(['open_status' => $status], ['id' => ['in', $ids]]);
        	
        	//系列课屏蔽操作，子课程也要屏蔽
        	if ($status == 2) {
        		$seriesIdList = $this->where('id', 'in', $ids)->where('type', 2)->column('id');
        		if (!empty($seriesIdList)) {
	        		$this->update(['open_status' => $status], ['pid' => ['in', $seriesIdList], 'open_status' => 1, 'status' => ['<>', 5]]);
        		}
        	}
        	
        	$this->commit();
        } catch (\Exception $e) {
        	$this->rollback();
        }
        
        return $result;
    }
    
    
    /**
     * 计算系列课现有子课程数量
     * @param array $pidList
     * @param unknown $openStatus, 屏蔽状态,默认查询开启状态的记录。如果为0/false/null,则不限制该状态
     * @return array
     * @author liujuneng
     */
    public function getChildCourseNum($pidList, $openStatus = 1)
    {
    	if (empty($pidList)) {
    		return [];
    	}
    	if ($openStatus) {
    		$this->where('open_status', $openStatus);
    	}
    	return $this->where('pid', 'in', $pidList)->where('status', 'in', '0,1,4')->group('pid')->column('count(1) as num', 'pid');
    }

    
}