<?php

namespace app\common\model;

/**
 * 佣金设置表
 */
class Brokerage extends ModelBs{
	public function upInfo($param){
        try{
        	if($param['id'] == 0){
	        	$BrokerageModel = $this->insert($param);
	        }else{
	        	$BrokerageModel = $this->where('id',$param['id'])->update([
	        		'platform_brokerage'=>$param['platform_brokerage'], 
					'divide_brokerage'=>$param['divide_brokerage'],
					'update_time'=>date('Y-m-d H:i:s') 
	    		]);
	        }	
	        return ['code' => 0, 'data' => '', 'msg' => '修改成功'];
        }catch( \PDOException $e){
        	return ['code' => 1, 'data' => '', 'msg' => $e->getMessage()];
        }
	}
}