<?php

namespace traits;


use app\common\controller\JsonResult;
use think\Config;

trait sysJson
{
    
    /**
     * 成功时code的值
     *
     * @var int
     */
    protected $success = 200;
    
    /**
     * 返回json时，空data的值
     *
     * @var null
     */
    protected $emptyData = null;
    
    
    /**
     * 成功返回json
     *
     * @param array  $data
     * @param string $msg
     * @param bool   $is_log
     * @param int    $code
     * @param array  $header
     * @param array  $options
     * @return \think\response\Json
     */
    protected function sucJson(array $data = [], $msg = '', $code = 200, $header = [], $options = [])
    {
        $func = input('request.' . Config::get('var_JSONP_handler'), '') ? 'jsonp' : 'json';
        $args = [$data, $code, $header, $options];
    
        $this->hookJson(\app\common\HookList::API_SUC_JSON, $args);
        
        return $func(...$args);
    }
    
    
    /**
     * 失败返回json
     *
     * @param array  $data
     * @param string $msg
     * @param bool   $is_log
     * @param int    $code
     * @param array  $header
     * @param array  $options
     * @return \think\response\Json
     */
    protected function errJson(array $data = [], $msg = '', $code = null, $header = [], $options = [])
    {
        $func = input('request.' . Config::get('var_JSONP_handler'), '') ? 'jsonp' : 'json';
        $args = [$data, $this->disSuccessCode($code), $header, $options];
        
        $this->hookJson(\app\common\HookList::API_ERR_JSON, $args);
    
        return $func(...$args);
    }
    
    
    private function hookJson($tag, &$args)
    {
        if (property_exists($this, 'request') && $this->request instanceof \think\Request){
            /** @var \think\Request $request */
            $request = $this->request;
    
            \think\Hook::listen($tag . '_' . $request->action(), $this, $args);
        }
        
    }
    
    
    
    /**
     * 返回固定格式的json
     *
     * @param mixed  $data
     * @param string $msg
     * @param int    $code
     * @return \think\response\Json
     */
    protected function sucSysJson($data, $msg = '', $code = null)
    {
        return $this->sucJson(
            $this->defaultAjaxData($this->disSuccessCode($code), $data, $msg)
        );
    }
    
    
    /**
     * 返回固定格式的json
     *
     * @param mixed  $data
     * @param string $msg
     * @param int    $code
     * @return \think\response\Json
     */
    protected function errSysJson($data, $msg = '', $code = null)
    {
        $emptyData = '';
        if (is_int($data) && isset(JsonResult::$jsonMsg[$data])) { // 在JsonResult中
            $json = $this->defaultAjaxData($data, $emptyData, $msg = JsonResult::getMsg($data));
        }elseif(is_string($data) && empty($msg)){ // 直接data为错误信息
            $json = $this->defaultAjaxData(JsonResult::ERR_LOCAL_MESSAGE, $emptyData, $data);
        }else{
            $json = $this->defaultAjaxData(is_null($code) ? JsonResult::ERR_LOCAL_MESSAGE : $code, $data, $msg);
        }
        return $this->errJson($json);
    }
    
    
    /**
     * 默认ajax数据
     *
     * @return array
     */
    protected function defaultAjaxData($code = null, $data = null, $msg = '')
    {
        return [
            'code' => $this->disSuccessCode($code),
            'data' => $this->disEmptyData($data),
            'msg'  => $msg,
        ];
    }
    
    
    /**
     * 用于处理$this->emptyData
     *
     * @param mixed $data
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    private function disEmptyData($data)
    {
        if ($this->emptyData !== null && empty($data)) {
            if (is_string($this->emptyData) && strtolower($this->emptyData) === 'object'){
                $this->emptyData = new \stdClass();
            }elseif(is_string($this->emptyData) && strtolower($this->emptyData) === 'array'){
                $this->emptyData = [];
            }
            
            $data = $this->emptyData;
        }
        
        return $data;
    }
    
    
    /**
     * 处理成功时的code
     *
     * @param $code
     * @return int
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function disSuccessCode($code)
    {
        return is_null($code) ? $this->success : $code;
    }
    
}