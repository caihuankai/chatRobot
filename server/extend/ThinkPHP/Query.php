<?php

namespace ThinkPHP;


class Query extends \think\db\Query
{
    /**
     * @var null|\app\common\model\ModelBase
     */
    protected $modelClass = null;
    
    
    /**
     * @inheritdoc
     */
    public function update(array $data)
    {
        if ($this->ifSetModelClass() && !$this->getModelClass()->isTriggerBool()) { // 没有走Model事件通知
            return $this->getModelClass()->isUpdate(true)->save($data);
        }
        
        return parent::update($data);
    }
    
    
    /**
     *
     * @todo 待解决event无法触发问题，多种insert
     * @param array $data
     * @param bool  $replace
     * @param bool  $getLastInsID
     * @param null  $sequence
     * @return int|string
     */
    public function insert(array $data, $replace = false, $getLastInsID = false, $sequence = null)
    {
        return parent::insert($data, $replace, $getLastInsID, $sequence);
    }
    

    
    
    public function ifSetModelClass()
    {
        return !is_null($this->modelClass);
    }
    
    
    /**
     * @param \app\common\model\ModelBase $modelClass
     * @return Query
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;
        
        return $this;
    }
    
    
    /**
     * @return \app\common\model\ModelBase|null
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }
    
    
    
    
    /*****************************解决throw*************************/
    
    
    /**
     * 查找记录
     *
     * @inheritdoc
     */
    public function select($data = null)
    {
        return parent::select($data);
    }
    
    /**
     * @inheritdoc
     */
    public function find($data = null)
    {
        return parent::find($data);
    }
    
    
    /**
     * @inheritdoc
     */
    public function delete($data = null)
    {
        return parent::delete($data);
    }
    
    
}