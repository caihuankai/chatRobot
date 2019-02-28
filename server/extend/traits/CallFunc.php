<?php

namespace traits;


/**
 * 方法覆盖，方法的AOP
 */
trait CallFunc
{
    /**
     * 存储func
     * <code>
     *      [
     *          'after' => [],
     *          'before' => [],
     *          'current' => [
     *              "func" => function(){}
     *              "action" => '',
     *          ]
     *      ]
     * </code>
     *
     * @var array
     */
    protected $traitCallFuncMap = [];
    
    
    /**
     * 覆盖方法，给方法添加before和after
     *
     * @param  string    $name
     * @param \Closure   $func
     * @param string     $action 行为，其中before,after为在原方法执行前或后执行，如果为空则不执行原方法
     * @param array|bool $funcKey before与after去重处理，如果为true则直接覆盖所有
     */
    public function traitAddFunc($name, \Closure $func, $action = '', $funcKey = [])
    {
        if (empty($name)) {
            $this->traitCallFuncThrow('$name不能为空');
        }
        
        if (!isset($this->traitCallFuncMap[$name])) {
            $this->traitCallFuncMap[$name] = ['before' => [], 'after' => [], 'current' => []];
        }
        
        if (empty($action) || $action === 'current') {
            $this->traitCallFuncMap[$name]['current'] = $func;
        } else if ($action === 'before' || $action === 'after') {
            if ($funcKey === true){ // 覆盖所有
                $this->traitCallFuncMap[$name][$action] = [];
            }else if(!empty($funcKey)){ // 唯一性
                $key = md5(serialize($funcKey));
                $this->traitCallFuncMap[$name][$action][$key] = $func;
                return;
            }
            
            array_push($this->traitCallFuncMap[$name][$action], $func);
        }
    }
    
    
    /**
     * 调用方法
     *
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    protected function traitCall($name, array $args = [])
    {
        if (empty($name)) {
            $this->traitCallFuncThrow('$name不能为空');
        }
        
        $default = null;
        $call = function ($func) use ($args, $default) {
            if ($func instanceof \Closure) {
                return call_user_func($func, $this, $args);
            }
            
            return $default;
        };
        
        $bool = true; // 是否执行当前类的方法
        if (isset($this->traitCallFuncMap[$name])) { // 存在设置
            foreach ($this->traitCallFuncMap[$name] as $action => $item) {
                $func = null;
                if ($action === 'current') {
                    $bool = false;
                    
                    return $call($item);
                } else if ($action === 'before' || $action === 'after') {
                    foreach ($item as $func) {
                        $call($func);
                    }
                }
            }
        }
        
        if ($bool && method_exists($this, $name)) { // 执行当前类方法
            return call_user_func_array([$this, $name], $args);
        }
        
        return $default;
    }
    
    /**
     * 抛出异常
     *
     * @param string $message
     * @throws \Exception
     */
    private function traitCallFuncThrow($message)
    {
        throw new \Exception($message);
    }
}