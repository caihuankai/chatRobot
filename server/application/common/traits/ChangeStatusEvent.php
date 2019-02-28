<?php

namespace app\common\traits;


trait ChangeStatusEvent
{
    
    protected $statusKey = 'status';
    
    /**
     * 修改为那种值是调用事件
     *
     * @var int|array
     */
    protected $statusValue = 2;
    
    
    protected $tempData = null;
    
    
    /**
     * @param \app\common\model\ModelBase|\think\db\Query|\app\common\traits\MysqlTinyintModel $data
     */
    public function before_update($data)
    {
        $this->tempData = $data;
        $key = $this->statusKey;
        if (isset($data[$key]) &&
            ((is_array($this->statusValue) && in_array($data[$key], $this->statusValue)) || $data->$key == $this->statusValue)) {
        
            if (method_exists($this, 'statusDataFunc')) {
                $temp = $this->statusDataFunc($data);
                if (is_array($temp)) {
                    \think\Hook::add('response_end', function () use ($temp) {
                        foreach ($temp as $openId => $message) {
                            if (!empty($message)) {
                                \WeChat\app::staffMsg($message, $openId);
                            }
                        }
                    });
                }
            
            }
        }
    }
    
    
    /**
     * 获取修改的状态的值
     *
     * @param bool $default
     * @return bool
     * @author aozhuochao
     */
    protected function getStatusValue($default = false)
    {
        return isset($this->tempData[$this->statusKey]) ? $this->tempData[$this->statusKey] : false;
    }
    
}