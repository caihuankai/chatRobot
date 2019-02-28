<?php

namespace app\common\model;


class Config extends ModelBs
{
    protected $cacheConstants = [];
    
    // 菜单上的使用教程
    const HELPER_URL = 'weChat_helper_url';
    
    
    /**
     * 设置系统配置的值
     *
     * @param $key
     * @param $value
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function setConfig($key, $value)
    {
        $this->checkKey($key);
        
        if ($this->getConfig($key, false) === false) { // 记录不存在
            $this->insert([
                'value' => $value,
                'key' => $key,
            ]);
        }else{
            $this->update(['value' => $value], ['key' => $key]);
        }
        
        return true;
    }
    
    
    /**
     * 获取系统配置的值
     *
     * @param $key
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getConfig($key, $default = '')
    {
        $this->checkKey($key);
        
        $data = $this->where(['key' => $key])->value('value');
    
        return !empty($data) ? $data : $default;
    }
    
    
    /**
     * 检测key是否存在
     *
     * @param $key
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function checkKey($key)
    {
        if (empty($this->cacheConstants)) {
            $reflect = new \ReflectionClass($this);
            $this->cacheConstants = $reflect->getConstants();
            $this->cacheConstants = !empty($this->cacheConstants) ? array_flip($this->cacheConstants) : [];
        }
    
        if (!array_key_exists($key, $this->cacheConstants)) {
            abort('config的key不存在');
        }
    }
    
}