<?php
namespace app\common\model;

use app\common\model\ModelBs;

class PushMessage extends ModelBs{
	public function getList($param){
		$page = !isset($param['pageNumber']) ? 0 : $param['pageNumber'];
        $limit = !isset($param['pageSize']) ? 0 : $param['pageSize'];
        $offset = ($page - 1) * $limit;
        $where = array();
        $where['status'] = ['in',[0,1]];
        if(!empty($param['user_id'])){
        	$where['user_id'] = $param['user_id'];
        }
        if($param['type'] != ""){
        	$where['type'] = $param['type'];
        }
        if(!empty($param['logmax'])){
			$param['logmax'] = date("Y-m-d",strtotime($param['logmax']."+ 1 day"));
		}
		if(!empty($param['logmax']) && !empty($param['logmin']))$where['push_time'] = ['between time',array($param['logmin'],$param['logmax'])];

		if(!empty($param['logmax']) && empty($param['logmin']))$where['push_time'] = ['<',$param['logmax']];

		if(empty($param['logmax']) && !empty($param['logmin']))$where['push_time'] = ['>',$param['logmin'],$param['logmax']];
		$dataList = $this->where($where)
		->order('push_time','desc')
		->limit($offset, $limit)
		->select();
		return $dataList;
	}
	public function getCount($param){
		$page = !isset($param['pageNumber']) ? 0 : $param['pageNumber'];
        $size = !isset($param['pageSize']) ? 0 : $param['pageSize'];
        $where = array();
        $where['status'] = ['in',[0,1]];
        if(!empty($param['user_id'])){
        	$where['user_id'] = $param['user_id'];
        }
        if($param['type'] != ""){
        	$where['type'] = $param['type'];
        }
        if(!empty($param['logmax'])){
			$param['logmax'] = date("Y-m-d",strtotime($param['logmax']."+ 1 day"));
		}
		if(!empty($param['logmax']) && !empty($param['logmin']))$where['push_time'] = ['between time',array($param['logmin'],$param['logmax'])];

		if(!empty($param['logmax']) && empty($param['logmin']))$where['push_time'] = ['<',$param['logmax']];

		if(empty($param['logmax']) && !empty($param['logmin']))$where['push_time'] = ['>',$param['logmin'],$param['logmax']];
		return $count = $this->where($where)->order('push_time','desc')->count();
	}
}