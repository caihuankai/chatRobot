<?php

namespace helper;
use think\Config;


/**
 * 处理url的类
 *
 * @package helper
 */
class UrlSys extends UrlBase
{
    /**
     * 用户默认头像
     *
     * @var string
     */
    protected static $userDefaultPic = '';
    
    /**
     * 是否加载图片配置
     *
     * @var bool
     */
    protected static $configBool = false;
    
    
    
    /**
     * 系统图片域名
     *
     * @var string
     */
    protected static $sysPicHost;
    
    
    
    /**
     * 获取图片域名
     *
     * @return string
     */
    public static function getPicDomain()
    {
        if (!self::$picDomain) {
            self::$picDomain = \think\config::get('PIC_DOMAIN');
        }
        
        return self::$picDomain;
    }
    
    
    /**
     * 获取前台（微信）host
     *
     * @return null|string
     */
    static public function getWxHost()
    {
        static $host = null;
        if (is_null($host)){
            $host = get_config_host('WX_DOMAIN');
        }
        
        return $host;
    }
    
    
    /**
     * 处理为保存数据库的图片地址
     *
     * @param $img
     * @return string
     */
    static public function handleSaveImgSrc($img)
    {
        if (empty($img)) {
            return '';
        }
    
        return (string)parse_url($img, PHP_URL_PATH);
    }
    
    
    
    
    /**
     * 获取图片域名
     *
     * @return string
     */
    public static function getSysPicHost()
    {
        if (!self::$sysPicHost) {
            self::$sysPicHost = self::getHost(\think\config::get('WX_DOMAIN'));
        }
        
        return self::$sysPicHost;
    }
    
    
    /**
     * 名师推荐背景图
     *
     * @param string $img
     * @return mixed|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function handleIndexTeacherBack($img = '')
    {
        return self::handleImg($img, 'index_teacher_back');
    }
    
    
    
    /**
     * 处理首页（优课推荐）图
     *
     * @param $img
     * @return mixed|string
     */
    static public function handleIndexImg($img = '')
    {
        return self::handleImg(self::convertIndex($img), 'index');
    }
    
    /**
     * 处理直播间背景（海报）图
     *
     * @param $img
     * @return mixed|string
     */
    static public function handleLiveBackImg($img = '', $arrBool = false, $hostShow = true)
    {
        return self::handleImg($img, 'live_back', $arrBool, $hostShow);
    }
    
    /**
     * 处理直播间图标
     *
     * @param $img
     * @return mixed|string
     */
    static public function handleLiveImg($img = '', $arrBool = false, $hostShow = true)
    {
        return self::handleImg($img, 'live', $arrBool, $hostShow);
    }
    
    
    /**
     * 处理课程背景（海报）图
     *
     * @param $img
     * @return mixed|string
     */
    static public function handleCourseBackImg($img = '', $arrBool = false, $hostShow = true)
    {
        return self::handleImg($img, 'course_back', $arrBool, $hostShow);
    }
    
    
    /**
     * 处理课程背景（海报）图
     *
     * @param $img
     * @return mixed|string
     */
    static public function handleSeriesImg($img = '', $arrBool = false, $hostShow = true)
    {
        return self::handleImg($img, 'course_back', $arrBool, $hostShow);
    }
    
    
    /**
     * 处理图片
     *
     * @param      $img
     * @param      $key
     * @param bool $arrBool
     * @param bool $hostShow 图片显示域名
     * @return mixed|string
     */
    static protected function handleImg($img, $key, $arrBool = false, $hostShow = true)
    {
        if ($arrBool){
            $img = (array)self::getSysConfig($key);
    
            $host = self::getSysPicHost();
            foreach ($img as &$item) {
                $item = url($item, '', false, $host);
            }
            
        }else if (empty($img)){ // 默认图
            $img = self::getDefaultImg($key);
        }else{ // 有图片地址
            $img = strpos($img, 'http') === 0 ? $img : url($img, '', false, $hostShow ? self::getSysPicHost() : '');
        }
        
        return $img;
    }
    
    
    /**
     * 获取配置的图片地址
     *
     * @param $key
     * @return mixed|string
     */
    static protected function getDefaultImg($key)
    {
        $result = '';
        $data = self::getSysConfig($key);
    
        if (!empty($data)) {
            if (is_array($data)) {
                $rand = mt_rand(0, count($data) - 1);
                $result = $data[$rand];
            }else{
                $result = $data;
            }
        }
        
        return url($result, '', false, self::getSysPicHost());
    }
    
    
    
    /**
     * 返回配置数据
     *
     * @param $name
     * @return mixed
     */
    static protected function getSysConfig($name = null)
    {
        if (!self::$configBool) {
            Config::load(CONF_PATH . 'config.img.php', '', 'img');
            self::$configBool = true;
        }
        
        return Config::get($name, 'img');
    }
    
    
    /**
     * 转换课程默认图为对应小图
     *
     * @param $img
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    static protected function convertIndex($img)
    {
        if (!empty($img)) { // 如果是课程默认图也使用优课推荐默认图
            $key = array_search($img, self::handleImg('', 'course_back', true));
            if ($key !== false) { // 找到数字
                $indexData = self::handleImg('', 'index', true);
                $img = !empty($indexData[$key]) ? $indexData[$key] : $img;
            }
        }
    
        return $img;
    }
    
}