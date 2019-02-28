<?php

namespace helper;

use think\Config;
use think\Debug;
use think\Log;

/**
 * 搜索引擎推送
 *
 * @package helper
 */
class UrlSubmit
{
    /**
     * 是否配置
     *
     * @var bool
     */
    protected $configBool = false;
    
    
    /**
     * 百度推送
     *
     * @see 查看爬虫 sudo cat /var/log/nginx/dks/dks.access.log | grep Baiduspider |wc
     * @see http://zhanzhang.baidu.com/linksubmit/index?site=http://dks.home.dacelue.com.cn/
     * @param array $urls
     * @return array
     */
    public function baidu($urls)
    {
        $urls = (array)$urls;
        $config = $this->getConfig('baidu');
        if (empty($urls) || empty($config)) {
            return [];
        }
    
        $pushApi = call_user_func_array('sprintf', $config);
    
        
        return $this->pushUrlSubmit($pushApi, [
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER     => ['Content-Type: text/plain'],
        ]);
    }
    
    
    /**
     * 百度的ping功能
     *
     * @param string $url 需要ping的url
     * @return array|mixed
     */
    public function pingBaidu($url)
    {
        $config = $this->getConfig('pingBaidu');
        if (empty($url) || empty($config)) {
            return [];
        }
    
        $name = '大咖社';
        $indexUrl = config('HOME_DOMAIN');
        $postData = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
    <methodName>weblogUpdates.extendedPing</methodName>
    <params>
        <param>
            <value><string>${name}</string></value>
        </param>
        <param>
            <value><string>${indexUrl}</string></value>
        </param>
        <param>
            <value><string>${url}</string></value>
        </param>
    </params>
</methodCall>
XML;
        $pushApi = call_user_func_array('sprintf', $config);
        
        return $this->pushUrlSubmit($pushApi, [
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER     => ['Content-Type: text/xml'],
            CURLOPT_TIMEOUT        => 2,
        ]);
    }
    
    
    
    /**
     * 执行推送
     *
     * @param       $pushApi
     * @param       $urls
     * @param array $options
     * @return mixed
     */
    protected function pushUrlSubmit($pushApi, array $options = [])
    {
        if (empty($pushApi)) {
            return [];
        }
        
        Debug::remark('urlSubmitStartTime', 'time');
        
        
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL            => $pushApi,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        $options += $defaultOptions;
    
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
    
    
        // 记日志
        Debug::remark('urlSubmitEndTime', 'time');
        $runtime = Debug::getRangeTime('urlSubmitStartTime', 'urlSubmitEndTime');
        Log::record('搜索推送： ' . var_export($options, true) . ' [ RunTime:' . $runtime . 's ]', 'urlSubmit');
    
        return !empty($data) ? json_encode($data, true) : [];
    }
    
    
    /**
     * 返回配置数据
     *
     * @param $name
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getConfig($name)
    {
        if (!$this->configBool) {
            Config::load(CONF_PATH . 'config.urlSubmit.php', '', 'urlSubmit');
            $this->configBool = true;
        }
    
        return Config::get($name, 'urlSubmit');
    }
}