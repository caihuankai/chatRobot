<?php

namespace helper;


class ReflectionUtils
{
    use \traits\Singleton;
    
    
    /**
     * 获取$class的所有非魔术方法的公有方法
     *
     * @param string|object $class
     * @param callable $func
     * @return bool
     * @author aozhuochao
     */
    public function getPublicMethodByClass($class, callable $func)
    {
        if ($this->checkReflectionClassExists($class) === false) {
            return false;
        }
    
        $reflect = new \ReflectionClass($class);
        $arr = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);
        if (!empty($arr)) {
            $classInstance = $reflect->newInstanceArgs([]);
            /** @var \ReflectionMethod $reflectionMethod */
            foreach ($arr as $reflectionMethod) {
                if (
                    // 过滤其他方法
                    strpos('__', $reflectionMethod->getName()) === 0 // 过滤魔术方法
                ) {
                    continue;
                }
            
                // 回调函数
                $func($reflectionMethod, $classInstance);
            }
        }
        
        return true;
    }
    
    
    /**
     * 可以调用类的非公有方法
     *
     * @param callable|array $method
     * @param array    $array
     * @return mixed|null
     * @author aozhuochao
     */
    public function callClassMethodArray(array $method, array $array = [])
    {
        if (is_array($method) && count($method) === 2) { // [$class, 'method']传参
            $class = new \ReflectionClass($method[0]);
            /** @var \ReflectionMethod $methodClass */
            $methodClass = $class->getMethod($method[1]);
    
            return call_user_func_array($methodClass->getClosure($method[0]), $array);
        }
        
        return null;
    }
    
    
    /**
     * 判断是否可以用于\ReflectionClass的实例化参数
     *
     * @param $class
     * @return bool
     */
    protected function checkReflectionClassExists($class)
    {
        $type = gettype($class);
        if ($type === 'string') {
            if (!class_exists($class)){
                return false;
            }
        }else if ($type !== 'object'){
            return false;
        }
        
        return true;
    }
    
}