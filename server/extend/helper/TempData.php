<?php

namespace helper;

/**
 * 用于跨方法的保存临时数据
 *
 * @package helper
 */
class TempData
{
    
    use \traits\Singleton;
    
    
    /**
     * 错误数据
     *
     * @var array
     */
    protected $tempData = [];
    
    /**
     * 最近一个key
     *
     * @var string
     */
    protected $lastKey = '';
    
    /**
     * TempData constructor.
     */
    protected function __construct()
    {
    }
    
    
    /**
     * 保存数据
     *
     * @param mixed $key
     * @param mixed $data
     */
    public function set($key, $data)
    {
        $key = $this->handleKey($key);
        $this->lastKey = $key;
        $this->tempData[$key] = $data;
    }
    
    
    /**
     * 获取数据
     *
     * @param mixed $key
     * @param mixed $default
     * @return bool|mixed
     */
    public function get($key = '', $default = false)
    {
        if (empty($key)){
            $key = $this->lastKey;
        }else{
            $key = $this->handleKey($key);
        }
    
        return isset($this->tempData[$key]) ? $this->tempData[$key] : (is_callable($default) ? $default() : $default);
    }
    
    
    /**
     * 不存在的key就执行匿名函数
     * 也可用于static数据
     *
     * @param          $key
     * @param \Closure $func
     * @return bool|mixed
     */
    public function one($key, \Closure $func)
    {
        $data = $this->get($key);
        if (!$data) {
            $data = $func($data);
            $this->set($key, $data);
        }
        
        return $data;
    }
    
    
    /**
     * 处理key
     *
     * @param $key
     * @return string
     */
    protected function handleKey($key)
    {
        return md5(var_export($key, true));
    }
    
    
    /**
     * 根据key获取自增的值
     *
     * @param $key
     * @return bool|mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getAutoInc($key)
    {
        $data = $this->get($key, 0);
        ++$data;
        $this->set($key, $data);
        
        return $data;
    }
    
    
}