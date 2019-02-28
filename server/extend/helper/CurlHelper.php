<?php

namespace helper;

/**
 * 各种curl的姿势
 */
class CurlHelper
{
    use \traits\Singleton, \traits\CallFunc;
    
    /**
     * 单例
     */
    protected function __construct()
    {
    }
    
    
    /**
     * curl_exec封装
     *
     * @param string $url 请求url
     * @param array $postData post时的数据
     * @param array $options  更多option设置
     * @return mixed
     */
    public function exec($url, $postData = [], $options = [])
    {
        $ch = curl_init();
        $defaultOptions = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true, // 不直接输出
        ];
    
        if (!empty($postData)) { // post请求
            $defaultOptions[CURLOPT_POST] = true;
            $defaultOptions[CURLOPT_POSTFIELDS] = $postData;
        }
        
        $options += $defaultOptions;
    
        curl_setopt_array($ch, $options);
        
        $data = curl_exec($ch);
        
        $this->traitCall('close', ['curl', $ch]);
        
        return $data;
    }
    
    
    /**
     * 不获取结果的请求
     *
     * @param string $url 请求地址
     * @param array  $option 需要发送的参数，key-value结构，其中整数为不考虑key
     * @param bool   $action 是否在读取到第一个字符时结束
     * @param int    $timeout 等待时间，最小为1
     * @return bool|\Closure  返回匿名函数，用于最后程序结束时运行
     */
    public function pushUrlOpen($url, array $option = [], $action = false, $timeout = 0.8)
    {
        $url = parse_url($url);
        if (empty($url) || empty($url['host'])) {
            return false;
        }
        $port = isset($url['port']) ? $url['port'] : 80;
        
        $startTime = microtime(true);
        
        $openTime = 2;
        try{
            $fp = fsockopen($url['host'], $port, $errNo, $errStr, $openTime);
            if (!$fp) return false;
    
            $path = !empty($url['query']) ? $url['path'] . '?' . $url['query'] : $url['path'];
    
            $httpText = '';
            $option = [
                    -1           => "POST {$path} HTTP/1.1",
                    'Host'       => $url['host'],
                    'Connection' => 'close',
                ] + $option;
            foreach ($option as $key => $val) {
                $key = is_int($key) ? '' : $key . ': ';
                $httpText .= $key . $val . "\r\n";
            }
    
            stream_set_blocking($fp, 1);
            stream_set_timeout($fp, $timeout);
    
            fwrite($fp, $httpText . "\r\n\r\n");
        }catch (\Exception $exception){
            return false;
        }
        
        return $this->traitCall('pushUrlOpenReturnCallback', [$fp, $action, $timeout, $startTime, $httpText]);
    }
    
    
    
    protected function pushUrlOpenReturnCallback($fp, $action, $timeout, $startTime, $httpText)
    {
        return function () use ($fp, $action, $timeout, $startTime, $httpText) {
            if ($action) { // 获取到数据就断开
                fread($fp, 1);
            } else {
                $diffTime = $timeout - (microtime(true) - $startTime);
                $diffTime > 0 && sleep(ceil($diffTime)); // 不足0.8秒就等待0.8秒
            }
    
            $this->traitCall('close', ['close', $fp, $httpText]);
    
            return true;
        };
    }
    
    
    /**
     * 资源关闭时执行
     *
     * @param $action
     * @param $fp
     * @return bool
     */
    protected function close($action, $fp)
    {
        $bool = false;
        if ($action === 'curl'){
            $bool = curl_close($fp);
        }else if($action === 'close'){
            $bool = fclose($fp);
        }
        
        return $bool;
    }
    
}