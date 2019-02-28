<?php

namespace helper;


/**
 * 处理url的类
 */
abstract class UrlBase implements UrlInterface
{
    /**
     * 图片域名
     *
     * @var string
     */
    protected static $picDomain = '';
    
    
    /**
     * 自动拼接图片url
     *
     * @param $picUrl
     * @return string
     */
    public static function picUrl($picUrl, $default = '')
    {
        $picUrl = !empty($picUrl) ? $picUrl : $default;
        if (empty($picUrl)) {
            return '';
        }
    
        if (self::getHost($picUrl)) { // 已有host
            return $picUrl;
        }
    
    
        return static::getPicDomain() . $picUrl;
    }
    
    
    /**
     * 获取url的host
     *
     * @param $url
     * @return string
     */
    static public function getHost($url)
    {
        return (string)parse_url($url, PHP_URL_HOST);
    }
    
}


/**
 * 需要实现的方法
 */
interface UrlInterface{
    public static function getPicDomain();
}