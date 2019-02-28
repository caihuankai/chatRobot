<?php

namespace traits;

/**
 * 单例类
 */
trait Singleton
{
    protected static $instance = null;
    
    
    /**
     * 单例
     *
     * @return static
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function instance(...$args)
    {
        $key = md5(var_export(['class' => static::class, 'args' => $args], true));
        if (!isset(self::$instance[$key])){
            self::$instance[$key] = new static(...$args);
        }
    
        return self::$instance[$key];
    }
    
}