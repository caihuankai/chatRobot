<?php

namespace helper;


/**
 * 通过hash实现数据全缓存
 *
 * @package helper
 */
class RedisHashData
{
    
    use \traits\Singleton;
    
    /**
     * 主名称
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * 搜索的条件
     *
     * @var string
     */
    protected $search = '';
    
    
    /**
     * 第二个hash的key
     *
     * @var string
     */
    protected $mapSuffix = '_param';
    
    
    /**
     * redis实例
     *
     * @var null|\Redis
     */
    protected $redis = null;
    
    
    /**
     * RedisHashData constructor.
     */
    public function __construct($name = '', $search = '')
    {
        $this->setName($name);
        $this->setSearch($search);
    }
    
    
    public function cacheData(callable $dataFunc, $default = [], callable $filterListId = null)
    {
        $redis = $this->getRedis();
        $redis->hGet($this->getMapKey(), $this->getSearch());
        
        
    }
    
    
    protected function getMapData($type)
    {
        static $map = [];
    
        if (is_null($map)) {
            $map = $this->getRedis()->hGet($this->getMapKey(), $this->getSearch());
        }
        
        switch ($type){
            case 'list':
                return isset($map[$type]) ? $map[$type] : [];
                break;
            case 'checkTime':
                $time = time();
                return isset($map['time']) && $map['time'] < $time; // true为未过期
                break;
            case 'int': // 其他存储类型
                break;
        }
        
        return false;
    }
    
    
    
    protected function getMapKey()
    {
        return $this->getName() . $this->mapSuffix;
    }
    
    
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->getName() . ':' . $this->search;
    }
    
    /**
     * @param string $search
     * @return $this
     */
    public function setSearch($search)
    {
        $this->search = join((array)$search, '_');
        
        return $this;
    }
    
    /**
     * @return null|\Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }
    
    /**
     * @param null|\Redis $redis
     * @return $this
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
        
        return $this;
    }
    
    
}