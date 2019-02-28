<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * 系统行为扩展：请求参数记录
 */
class RequestLogBehavior {
    
    /**
     * 是否可执行
     *
     * @var bool
     */
    protected $appBool = true;

    // 行为扩展的执行入口必须是run
    public function run(){
        $num = 20;
        $br = PHP_EOL;
        $msg = str_repeat('-', $num) . $br;
        $msg .= 'agent：' . $this->getClientType() . '|token：' . input('param.token', '') .
            '|uid：' . input('param.userid', '') . '|ip：' . input('server.REMOTE_ADDR') . $br;
        $msg .= 'url：' . input('server.REQUEST_URI', '') . $br;
        $msg .= 'get：' . var_export(input('get.', []), true) . $br;
        $msg .= 'post：' . var_export(input('post.', []), true) . $br;
        $msg .= 'file：' . var_export($_FILES, true) . $br;
        $msg .= str_repeat('-', $num) . $br;
        
        
        $filePath = $this->getLogRoot() . 'requestLog/' .date('y_m_d') . '.log';
        $this->putFile($filePath, $msg);
    }
    
    
    /**
     * 获取客户端类型
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getClientType()
    {
        $agent = input('server.HTTP_USER_AGENT');
        if (stripos($agent, 'iPhone') !== false || stripos($agent, 'iPad')) {
            return 'ios';
        } elseif (stripos($agent, 'Android') !== false) {
            return 'Android';
        } else {
            return '';
        }
    }
    
    
    /**
     * 写入文件
     *
     * @param $filePath
     * @param $msg
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function putFile($filePath, $msg)
    {
        if (!$this->appBool){
            return;
        }
        
        $dir = dirname($filePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
    
        if (is_writable($filePath)) {
            file_put_contents($filePath, $msg, FILE_APPEND);
        }else{
            error_log('无法记录请求日志，' . $filePath);
        }
    }
    
    
    /**
     * 获取日志目录
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getLogRoot()
    {
        if (defined('LOG_PATH') && !empty(LOG_PATH)) {
            return LOG_PATH;
        } else {
            $logPath = config('LOG_PATH');
            if (!empty($logPath)) {
                return $logPath;
            } else {
                $this->appBool = false;
                return '';
            }
        }
    }

}