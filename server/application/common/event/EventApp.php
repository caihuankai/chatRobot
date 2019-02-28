<?php

namespace app\common\event;


class EventApp
{
    use \app\common\traits\Common;
    
    /**
     * EventApp constructor.
     */
    public function __construct()
    {
    
    }
    
    
    /**
     * 返回where里的and数据
     *
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getModelWhereAnd()
    {
        $where = $this->getCurrentModel()->getOptions('where');
        
        return !empty($where['AND'])?$where['AND']:[];
    }
    
    
    /**
     * 获取model里save的条件
     *
     * @return $this|array
     * @author aozhuochao
     */
    protected function getSaveFuncData($model = null)
    {
        $temp = !is_null($model) ? $model : $this->getCurrentModel();
        
        return $temp->handleSaveFuncOfData();
    }
    
    
    
    /**
     * 返回修改的id
     *
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getIds()
    {
        $id = $this->getCurrentModelPk();
        
        return property_exists($this, $id) ? $this->$id : [];
    }
    
    
    /**
     * @param \app\common\model\ModelBase|\think\db\Query|\app\common\traits\MysqlTinyintModel $model
     */
    protected function before_update($model){}
    
    /**
     * @param \app\common\model\ModelBase|\think\db\Query|\app\common\traits\MysqlTinyintModel $model
     */
    protected function after_update($model){}
    protected function before_delete($model){}
    protected function after_delete($model){}
    
    
}