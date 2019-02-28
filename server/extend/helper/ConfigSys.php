<?php

namespace helper;


class ConfigSys
{
    use \traits\Singleton;
    
    protected $name = '';
    
    /**
     * 是否加载默认config的文件名配置
     * 
     * @var bool
     */
    protected $mergeBool = false;
    
    
    /**
     * 设置配置
     *
     * @param        $key
     * @param null   $value
     * @param string $name
     * @return static
     */
    public function set($key, $value = null, $name = '')
    {
        \think\config::set($key, $value, $this->load($name)->getName());
        
        return $this;
    }
    
    
    /**
     * 获取配置
     *
     * @param        $key
     * @param null   $default
     * @param string $name
     * @return mixed|null
     */
    public function get($key, $default = null, $name = '')
    {
        $data = \think\config::get($key, $this->load($name)->getName());
    
        return empty($data) && !is_null($default) ? $default : $data;
    }
    
    
    /**
     * 获取配置文件所有数据
     *
     * @param string $name
     * @param array  $default
     * @return mixed|null
     */
    public function getAll($name = '', $default = [])
    {
        return $this->get(null, $default, $this->load($name)->getName());
    }
    
    
    /**
     * 给系统的\think\config::load添加cache
     *
     * @param $name
     * @return static
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function load($name = '')
    {
        $this->setName($name);
        \helper\TempData::instance()->one($this->getDataKey(), function ()use ($name){
            \think\config::load($this->getPath($name), '', $name);
            
            if ($this->mergeBool){ // 加载默认config配置下的config.aaa.php名称的配置
                $key = str_replace('.', '-', $this->getFullName($name)); // 将.转为-
                $data = \think\config::get($key);
                !empty($data) && \think\config::set($data, null, $name);
            }
            
            return true;
        });
        
        return $this;
    }
    
    
    /**
     * 用于\helper\TempData的key
     *
     * @param string $name
     * @return string
     */
    protected function getDataKey($name = '')
    {
        return __CLASS__ . ':' . $this->getName($name);
    }
    
    public function getPath($name = '')
    {
        return CONF_PATH . $this->getFullName($name);
    }
    
    
    public function setMergeBool($bool = true)
    {
        $this->mergeBool = $bool;
        
        return $this;
    }
    
    
    /**
     * @param string $name
     * @return static
     */
    public function setName($name)
    {
        if (!empty($name)) {
            $this->name = $name;
        }
    
    
        return $this;
    }
    
    /**
     * @return string
     */
    protected function getName($name = '')
    {
        return $name ? $name : $this->name;
    }
    
    
    /**
     *
     *
     * @param string $name
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getFullName($name = '')
    {
        return "config.{$this->getName($name)}.php";
    }
    
}