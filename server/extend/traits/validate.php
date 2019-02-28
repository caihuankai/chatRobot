<?php

namespace traits;
use app\common\controller\JsonResult;


/**
 * Class validate
 *
 * @property \think\Request request
 * @package traits
 */
trait validate
{
    
    /**
     * 默认abortError处理方法
     *
     * @var string
     */
    protected $defaultErrFuncTrait = 'error';
    
    
    /**
     * 根据指定验证规则失败时，直接报错，输出json
     *
     * @param string $validate 验证器名或者验证规则数组
     * @param mixed  $localMsg 固定的自定义报错信息，可传数组，在定义了$errFunc的时候
     * @param string $errFunc 定义处理函数
     * @param array  $data 指定验证的data
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     * @throws \RuntimeException
     */
    protected function validateByName($validate = '', $localMsg = '', $errFunc = null, $data = [])
    {
        if (empty($validate)) {
            /** @var \think\Request $request */
            $request = $this->request;
            $validate = (string)$request->controller() . '.' . $request->action();
        }
        
        $data = $data ?: input('param.');
        $validate = $this->validate($data, $validate);
        if ($validate !== true) { // 直接报错
            
            $localMsg = $localMsg ?: $validate;
            
            $this->abortError($localMsg, $errFunc);
        }
    }
    
    
    /**
     * 抛出异常
     *
     * @param mixed  $args
     * @param string $errFunc
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     * @throws \RuntimeException
     */
    protected function abortError($args, $errFunc = null)
    {
        $errFunc = $this->disErrFunc($errFunc);
        if (!($args instanceof \think\Response) && $errFunc && method_exists($this, $errFunc)) {
            abort(call_user_func_array([$this, $errFunc], (array)$args));
        } else {
            abort($args);
        }
    }
    
    
    /**
     * 处理errFunc
     *
     * @param string $errFunc
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function disErrFunc($errFunc)
    {
        return $errFunc !== null ? $errFunc : $this->defaultErrFuncTrait;
    }
    
    
    /**
     * 防止重复提交
     *
     * @param int|true $addTime
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function filterRepeatPost($key, $addTime = true)
    {
        if (!$this->request->isPost()){
            return;
        }
        
        $key = __METHOD__ . ':' . md5(var_export((array)$key, true));
        
        $md5 = md5(var_export((array)$this->request->post('.'), true));
        $time = microtime(true);
        $data = \think\Cache::get($key);
        if (!empty($data) && $data['md5'] === $md5 && $data['time'] > $time) {
            $code = JsonResult::ERR_SUBMIT_FAST;
            JsonResult::setMsgArgs($code, (int)$addTime);
            $this->abortError($code);
        }else{
            if ($addTime === true){
                \think\Cache::set($key, ['md5' => $md5, 'time' => $time + 5], $time + 1); // 最大5秒的锁
                \think\Hook::add('response_end',function() use($key, $md5, $time){ // 后面解除
                    \think\Cache::set($key, ['md5' => $md5, 'time' => $time], time() + 1);
                });
            }else{
                $time = $time + $addTime;
                \think\Cache::set($key, ['md5' => $md5, 'time' => $time], $time + 1);
            }
    
        }
    }
    
    
}