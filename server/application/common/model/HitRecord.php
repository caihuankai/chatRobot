<?php
namespace app\common\model;

use app\common\model\ModelBs;

/**
 * live直播间进入记录表
 */
class HitRecord extends ModelBs{
	//用户进入live直播间记录
	public function recordJoinLive($user_id,$live_id){
		//判断用户当日是否已经记录
		$where['hitRecordType'] = 2;
		$where['userId'] = $user_id;
		$where['targetId'] = $live_id;
		$status = 0;
		$today = strtotime(date('Y-m-d',time()));
		$whetherJoin = $this->where($where)->where('UNIX_TIMESTAMP(createTime)>'.$today)->find();
		if(empty($whetherJoin)){
			$this->startTrans();
			try{
				$where['createTime'] = date('Y-m-d H:i:s');
				$status = $this->insertGetId($where);
				$this->commit();
			}catch(Exception $e){
				$this->rollback();
			}
		}
		if($status > 0){
			return true;
		}else{
			return false;
		}
	}
	//用户进入课程直播间记录
	public function recordJoinCourse($user_id,$course_id){
		//判断用户当日是否已经记录
		$where['hitRecordType'] = 3;
		$where['userId'] = $user_id;
		$where['targetId'] = $course_id;
		$status = 0;
		$today = strtotime(date('Y-m-d',time()));
		$whetherJoin = $this->where($where)->where('UNIX_TIMESTAMP(createTime)>'.$today)->find();
		if(empty($whetherJoin)){
			$this->startTrans();
			try{
				$where['createTime'] = date('Y-m-d H:i:s');
				$status = $this->insertGetId($where);
				$this->commit();
			}catch(Exception $e){
				$this->rollback();
			}
		}
		if($status > 0){
			return true;
		}else{
			return false;
		}
	}
    
    
    /**
     * 根据用户id获取这个用户的直播间访问数
     *
     * @param array $userIds
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function countLiveByUserId(array $userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        
        return $this->where(['l.user_id' => ['in', $userIds]])->alias('hr')->field('l.user_id, count(hr.hitRecordId) num')
            ->join('live l', 'l.id = hr.targetId and hr.hitRecordType = 2')
            ->fetchClass('\CollectionBase')->select()->column('num', 'user_id');
	}
	
	
}