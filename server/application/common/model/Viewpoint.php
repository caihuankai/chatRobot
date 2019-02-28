<?php

namespace app\common\model;


class Viewpoint extends ModelBs
{
    
    /**
     * 统计用户发布观点数
     *
     * @param array $userIds
     * @param int   $status
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function countByUserIds(array $userIds, $status = 1, $field = '', array $otherWhere = [])
    {
        if (empty($userIds)) {
            return [];
        }
    
        if (!empty($otherWhere)) {
            $this->where($otherWhere);
        }
    
        return $this->where(['uid' => ['in', $userIds], 'status' => $status])->group('uid')
            ->field(empty($field) ? 'count(id) num, uid' : $field)
            ->fetchClass('\CollectionBase')->select()->column(empty($field) ? 'num' : null, 'uid');
    }
    
    /**
     * 统计助理发布观点数，默认只统计草稿的数量
     * @param array $assistantIds
     * @param number $status
     * @param string $field
     * @param array $otherWhere
     * @return array|array
     * @author liujuneng
     */
    public function countByAssistantIds(array $assistantIds, $status = 0, $field = '', array $otherWhere = [])
    {
    	if (empty($assistantIds)) {
    		return [];
    	}
    	
    	if (!empty($otherWhere)) {
    		$this->where($otherWhere);
    	}
    	
    	return $this->where(['assistant_id' => ['in', $assistantIds], 'status' => $status])->group('assistant_id')
    	->field(empty($field) ? 'count(id) num, assistant_id' : $field)
    	->fetchClass('\CollectionBase')->select()->column(empty($field) ? 'num' : null, 'assistant_id');
    }
    
}