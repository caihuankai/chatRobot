<?php
namespace app\common\model;


/**
 * 课程邀请卡表
 * @author xiaok
 * @time 2071/06/13
 */
class Invitationcard extends ModelBs{
	//根据二维码id获取邀请卡信息
	public function getInfo($EventKey){
		$EventKey = (int)$EventKey;
		$data = $this->alias('a')
		->field('a.*,t.alias,u.user_level,u.is_assistant,u.alias as ualias')
		->where('a.sn',$EventKey)
		->join('third_login t','a.uid = t.user_id','left')
        ->join('user u','a.uid = u.user_id','left')
        ->order('a.create_time','desc')
		->find();
		
		return $data;
	}
    
    
    /**
     * static保存数据
     *
     * @param $eventKey
     * @return array|false|\PDOStatement|string|\think\Model
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getStaticInfo($eventKey)
    {
        static $cache = [];
        if (empty($cache[$eventKey])) {
            $cache[$eventKey] = $this->getInfo($eventKey);
        }
        
        return $cache[$eventKey];
	}
    
    
    /**
     * 根据直播id获取直播邀请数据
     * 直播间没有邀请嘉宾
     * todo 未使用过
     *
     * @param array $liveIds
     * @param       $field
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getInviteUserByLiveId(array $liveIds, $field)
    {
        if (empty($liveIds)) {
            return [];
        }
        
        $data = $this->where(['ic.live_id' => ['in', $liveIds], 'ic.type' => 2])->alias('ic')->field($field)
            ->join('invitationcard_user icu', 'icu.card_id = ic.id')
            ->select();
    
        $result = [];
        foreach ($data as $item) {
            $result[$item['live_id']][] = $item;
        }
        
        return $result;
	}
    
    

	
	
}