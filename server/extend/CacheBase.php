<?php

class CacheBase extends \think\Cache
{
    
    protected static $md5Bool = false;
    
    
    /**
     * 缓存数据
     *
     * @param array   $keyArgs
     * @param Closure $func
     * @param int     $time
     * @return array|mixed
     */
    public static function cacheData(array $keyArgs, \Closure $func, $time = 1800)
    {
        $key = self::getCacheName(...$keyArgs);
        $data = self::get($key);
        if (empty($data)) {
            $data = $func();
            self::set($key, $data, $time);
        }
        
        return $data;
    }
    
    
    
    /**
     * 缓存数组的每个数据，数组的key作为cache的key的一部分
     *
     * @param array   $keyArgs
     * @param Closure $func
     * @param int     $time
     * @return array
     */
    public static function cacheEveryData($where, array $keyArgs, \Closure $func, $time = 1800)
    {
        $key = self::getCacheName(...$keyArgs);
        $getKeyFunc = function ($whereKey)use($key){
            return $key . ':' . $whereKey;
        };
        $diffWhere = $keyArr = $map = $result = [];
        $where = (array)$where;
        foreach ($where as $item) { // 转换key
            $funcKey = $getKeyFunc($item);
            $keyArr[] = $funcKey;
            $map[$funcKey] = $item;
        }
    
    
        $redisData = self::redisMGetForJson($keyArr);
        foreach ($redisData as $key => $item) {
            if(is_null($item)){ // 缓存为null
            
            }else if ($item === false && isset($map[$key])){ // 无值
                $diffWhere[] = $map[$key];
            }else if(isset($map[$key])){ // 有值
                $result[$map[$key]] = $item;
            }
        };
    
    
        if (!empty($diffWhere)) {
            $data = $func($diffWhere);
            $save = [];
            foreach ($diffWhere as $item) {
                $funcKey = $getKeyFunc($key);
                if (isset($data[$item])) {
                    $save[$funcKey] = $data[$item];
                    $result[$item] = $data[$item];
                } else {
                    $save[$funcKey] = null;
                }
            }
            self::redisMSetForJson($save, $time);
        };

        // 返回存在值的数组
        return $result;
    }
    
    
    
    /**
     * 根据传参获取缓存名称
     *
     * @return string
     */
    public static function getCacheName()
    {
        $nameArgs = func_get_args();
        $funKey = function ($data){
            return md5(var_export($data, true));
        };
    
        $key = '';
        if (self::$md5Bool){
            $key = $funKey($nameArgs);
        }else if(!empty($nameArgs)){
            foreach ($nameArgs as &$item) {
                if (!is_scalar($item)) {
                    $item = $funKey($item);
                }
            }
            $key = join($nameArgs, ':'); // redis的key规则
        }
        
        return $key;
    }
    
    /**
     * @inheritDoc
     */
    public static function get($name, $default = false, $num = 1)
    {
        if (self::debug()){
            return [];
        }
    
        \think\Log::record(__METHOD__ . ': ' . $name, 'cache');
        $data = parent::get($name, $num > 1 ? null : $default);
        
        if ($num > 1 && !is_null($data)){
            --$num;
            return static::get($data, $default, $num);
        }else if(is_null($data)){
            $data = $default;
        }
        
        return $data;
    }
    
    /**
     * @inheritDoc
     */
    public static function set($name, $value, $expire = 0)
    {
        if ($value === null){ // 为null会报错
            return false;
        }
    
        \think\Log::record(__METHOD__ . ': ' . $name, 'cache');
        return parent::set($name, $value, (int)$expire);
    }
    
    
    /**
     * 是否debug不缓存
     *
     */
    protected static function debug()
    {
        return \think\Config::get('cache.clearBool');
    }
    
    
    /**
     * 获取redis实例
     *
     * @return \Redis
     */
    public static function redis()
    {
        \think\Log::record(__METHOD__, 'cache');
    
        self::init(\think\Config::get('cache')); // 会切换\think\Cache::$handler
        return self::store('')->handler();
    }
    
    
    /**
     * 一次获取多个数据
     *
     * @param array $keys
     * @return array
     */
    public static function redisMGetForJson(array $keys)
    {
        if (empty($keys)) {
            return [];
        }
    
        $result = [];
        $keys = array_values($keys);;
        $data = self::redis()->mget($keys);
        foreach ($keys as $key => $value) {
            try{
                $result[$value] = $data[$key] === false ? false : json_decode($data[$key], true); // null就是不存在
            }catch (\Exception $exception){
                $result[$value] = null;
            }
        };;
        
        return $result;
    }
    
    
    /**
     * 一次设置多个数据
     *
     * @param array $data
     */
    public static function redisMSetForJson(array $data, $timeout = 0)
    {
        if (empty($data)) {
            return;
        }
    
        if ($timeout > 0) {
            foreach ($data as $key => $value) {
                self::set($key, json_encode($value), $timeout);
            }
        }else{
            $redis = self::redis();
            foreach ($data as &$item) {
                $item = json_encode($item);
            }
    
            $redis->mset($data);
        }
    }
    
    /**
     * @param bool $md5Bool
     */
    public static function setMd5Bool($md5Bool = true)
    {
        self::$md5Bool = $md5Bool;
    }
    
    
}