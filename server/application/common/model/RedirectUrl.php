<?php

namespace app\common\model;

/**
 * 前端跳转链接
 *
 * @package app\common\model
 */
class RedirectUrl
{
    
    
    /**
     * 获取视频推送流
     *
     * @param $userId
     * @param $courseId
     * @return string
     * @author aozhuochao
     */
    public static function getVideoSteam($userId, $courseId)
    {
        return sprintf(config('video_stream.push'), $userId . '_' . $courseId);
    }
    
    
    
    
    /**
     * 获取我的介绍的url
     *
     * @param $userId
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getMyInfoUrl($userId)
    {
        return self::urlFunc("/#/personalSpace/lobbyPage/{$userId}/0"); // 0为分享人id
    }

    public static function getGlobalUrl(){
        return self::urlFunc("/#/publicOnlive");
    }
    
    
    /**
     * 课程页url
     *
     * @param $courseId
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getCourseUrl($courseId)
    {
        return self::urlFunc("/#/personalCenter/course/{$courseId}");
    }
    
    
    
    /**
     * 获取  我的   页面url
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getMyUrl()
    {
        return self::urlFunc('/#/personalCenter');
    }
    
    
    /**
     * 处理url链接
     *
     * @param $queryUrl
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected static function urlFunc($queryUrl, $vars = '')
    {
        return url($queryUrl, $vars, false, \helper\UrlSys::getWxHost());
    }
    
}