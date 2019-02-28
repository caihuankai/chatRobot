<?php
namespace app\passport\controller;

use app\common\controller\ControllerBase;
use think\Log;

/**
 *
 * @author scan<232832288@qq.com>
 *
 */
class App extends ControllerBase
{
    protected $now;
    
    protected function _initialize()
    {
        $this->now = time();
        parent::_initialize();
    }
    
    
    /**
     * 正确返回
     * @param array $data
     * @param string $msg
     */
    protected function ret($data=null, $msg = '')
    {
        empty($data) && $data = null;
        $content = [
            'code' => 0,
            'data' => $data,
            'msg'  => $msg,
            'time' => $this->now,
        ];
        Log::write(json_encode($content));
        return json_encode($content);
    }
    
    /**
     * 错误返回
     * @param unknown $code
     * @param string $msg
     */
    protected function erret($code, $msg = '')
    {
        $logArr = array('error_msg'=>($msg ?: (isset(\C::$msg[$code]) ? \C::$msg[$code] : '')),
            'error_code'=>$code,
            'request'=>json_encode($_REQUEST),
            'comment'=>'注意：并不是调用两次，只是打印了2次log');
        Log::write($logArr);
    
        $content = [
            'code' => $code,
            'data' => null,
            'msg'  => $this->getCodeMsg($code), // $msg ?: (isset(\C::$msg[$code]) ? \C::$msg[$code] : ''),
            'time' => $this->now,
        ];
        return json_encode($content);
    }
    
    /**
     * 获取错误码描述
     * @param unknown $code
     */
    protected function getCodeMsg($code)
    {
        $file = RUNTIME_PATH.DS.'codemsg.php'; // 后面改成存redis
        if (is_file($file)) {
            $data = json_decode(file_get_contents($file), true);
        } else {
            $data = $this->setCodeMsg();
            file_put_contents($file, json_encode($data));
        }
        return isset($data[$code]) ? $data[$code] : '未知错误';
    }
    
    /**
     * 生成错误码缓存文件
     */
    protected function setCodeMsg()
    {
        $content = file_get_contents(APP_PATH . 'common/controller/C.php');
        preg_match_all('/(\/\/(?P<document>[^\/\\n]+))([^c]+)const(?P<name>[^=]+)=(?P<digit>[^;]+);/', $content, $matches);
        $data = [];
        $count = count($matches['document']);
        for ($i=0; $i<$count; $i++){
            $data[trim($matches['digit'][$i])] = trim($matches['document'][$i]);
        }
        preg_match_all('/(\/\*(?P<document>[^\/+]+)\*\/)([^c]+)const(?P<name>[^=]+)=(?P<digit>[^;]+);/', $content, $matches);
        $count = count($matches['document']);
        for ($i=0; $i<$count; $i++){
            $data[trim($matches['digit'][$i])] = trim(str_replace(array('*', "\r", "\n"), '', $matches['document'][$i]));
        }
        ksort($data);
        return $data;
    }
    
}
