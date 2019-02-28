<?php

namespace helper;

/**
 * [
 *      'status' => [
 *          2 => [
 *              'title' => '关闭',
 *              '任意名1' => '<span class="red">{{title}}</span>',
 *              '任意名2' => function($val, $map){return $val;},
 *
 *          ],
 *      ]
 * ]
 *
 * @package helper
 */
class Tinyint
{
    
    use \traits\Singleton; // todo 待判定如何使用单例
    
    /**
     * 值对应的文案
     */
    const TITLE = 'title';
    /**
     * 值对应数组的key
     */
    const VALUE = 'value';
    
    /**
     * 唯一key，可以通过反向查找到value
     */
    const KEY = 'key';
    
    
    /**
     * 值的下一个状态
     */
    const NEXT = 'next';
    
    
    protected $map = [];
    
    /**
     * 当前操作的field
     *
     * @var string
     */
    protected $field = '';
    
    
    /**
     * 设置文案，可改变action改变场景
     *
     * @param string $field 字段名
     * @param int    $value 字段名对应的值
     * @param mixed  $data $action参数对应的值
     * @param string $action 场景
     * @return $this
     */
    public function set($value, $data, $action = self::TITLE, $field = '')
    {
        $field = $this->getField($field);
        if (isset($this->map[$field])) {
            if (isset($this->map[$field][$value])) {
                $this->map[$field][$value][$action] = $data;
            }else{
                $this->map[$field][$value] = [$action => $data];
            }
        }else{
            $this->map[$field] = [
                $value => [$action => $data],
            ];
        }
        
        return $this;
    }
    
    
    /**
     * 获取设置的文案
     *
     * @param string $field   字段名
     * @param int    $value     字段名对应的值
     * @param string $default 最终为空时的默认值
     * @param string $action  场景
     * @return string
     */
    public function get($value, $default = '', $action = self::TITLE, $field = '', $map = null)
    {
        $field = $this->getField($field);
        $map = is_null($map) ? $this->map : $map;
        $result = '';
        $bool = $this->existsValue($value, $field);
        if ($bool && isset($map[$field][$value][$action])){
            $result = $map[$field][$value][$action];
            $data = $map[$field];
            
            if (is_callable($result)) {
                $result = $result($value, $data);
            }else if (is_string($action)){ // 可通过{{}}来获取到其他值
                $result = preg_replace_callback('/{{([^_]+)}}/', function ($matches)use($value, $data){
                    return $this->replaceCallbackByGet($matches, $value, $data);
                }, $result);
                
            }
        }else if ($bool && $action === self::VALUE){ // 获取值
            $result = $value;
        }
    
        return !empty($result) ? $result : $default;
    }
    
    
    /**
     * 根据action 获取actionValue列表
     *
     * @param string $action
     * @param string $key  指定返回值的key的值，默认为value
     * @param string $field
     * @param null   $map
     * @return array
     */
    public function getList($action = self::TITLE, $key = self::VALUE, $field = '', $map = null)
    {
        $field = $this->getField($field);
        $map = is_null($map) ? $this->map : $map;
        
        $result = [];
        if (isset($map[$field]) && is_array($map[$field])){
            foreach ($map[$field] as $value => $item) {
                if (isset($item[$action])) {
                    $result[$this->get($value, '', $key, $field, $map)] = $this->get($value, '', $action, $field, $map);
                }
            }
        }
        
        return $result;
    }
    
    
    
    /**
     * 根据action 获取actionValue为true的列表，返回value
     *
     * @param string $action
     * @param        $field
     * @param null   $map
     * @return array
     */
    public function getListByActionValueTrue($action, $field = '', $map = null)
    {
        $field = $this->getField($field);
        $map = is_null($map) ? $this->map : $map;
        
        $result = [];
        if (isset($map[$field]) && is_array($map[$field])){
            foreach ($map[$field] as $value => $item) {
                if (isset($item[$action]) && $item[$action] === true) {
                    $result[] = $value;
                }
            }
        }
        
        return $result;
    }
    
    
    
    /**
     * 不使用set，每次指定map
     *
     * @param array  $data 对应$this->map
     * @param array ...$args
     * @return string
     */
    public function getTemp(array $data, ...$args)
    {
        array_push($args, $data);
        return call_user_func_array([$this, 'get'], $args);
    }
    
    
    /**
     * get中的preg_replace_callback
     *
     * @param $matches
     * @param $value
     * @param $data
     * @return string
     */
    protected function replaceCallbackByGet($matches, $value, $data)
    {
        if (isset($data[$value][$matches[1]])) {
            return $data[$value][$matches[1]];
        }
        
        return '';
    }
    
    
    public function setMap($map, $value = null)
    {
        if (is_null($value) && is_array($map)) {
            $this->map = $map;
        }else if(isset($value) && is_string($map)){
            $this->map[$map] = $value;
        }
    
        return $this;
    }
    
    
    /**
     * 获取值
     *
     * @param string $value
     * @return array|mixed
     */
    public function getMap($value = '')
    {
        return !empty($value) ? $this->map[$value] : $this->map;
    }
    
    
    /**
     * 检测field是否存在
     *
     * @param $field
     * @return bool
     */
    public function existsField($field)
    {
        if (empty($field)) {
            return false;
        }
        
        return isset($this->map[$field]);
        
    }
    
    
    /**
     * 检测值是否存在
     *
     * @param $field
     * @param $value
     * @return bool
     */
    public function existsValue($value, $field = '')
    {
        $field = $this->getField($field);
        return $this->existsField($field) && isset($this->map[$field][$value]);
    }
    
    
    /**
     * 检测action里是否存在actionValue
     *
     * @param mixed  $actionValue
     * @param string $action
     * @param string $field
     * @param null   $map
     * @return bool
     */
    public function existsActionValue($actionValue, $action, $field = '', $map = null)
    {
        $map = $map ?: $this->getMap($this->getField($field));
        if (is_array($map)){
            foreach ($map as $item) {
                if (isset($item[$action]) && $item[$action] == $actionValue) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * 检测指定action是否被设置
     *
     * @param $field
     * @param $action
     * @return bool
     */
    public function existsSetAction($action, $field = '')
    {
        $map = $this->getMap($this->getField($field));
        if (is_array($map)){
            foreach ($map as $item) {
                if (is_array($item) && isset($item[$action])) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    
    
    /**
     * 判定对应action是否为true
     *
     * @param $field
     * @param $value
     * @param $action
     * @return bool
     */
    public function ifActionTrue($value, $action, $field = '')
    {
        $field = $this->getField($field);
        return $this->existsValue($value, $field) && isset($this->map[$field][$value][$action]) && $this->map[$field][$value][$action] === true;
    }
    
    
    
    
    /**
     * 获取$value里的$action的值，作为下一个值的$nextAction
     *
     * @param string $field
     * @param string $value
     * @param string $action  当前action
     * @param string $nextAction 下一个值的action
     * @param array  $flipArr 临时指定 值的映射关系，如：[1 => 2, 2 => 1]
     * @return string
     */
    public function getNextValueByValue($value, $action = self::NEXT, $nextAction = self::VALUE, $field = '', $flipArr = [])
    {
        $field = $this->getField($field);
        $result = '';
        if (!empty($flipArr) && isset($flipArr[$value])){ // 指定映射关系
            $result = $this->get($flipArr[$value], $result, $nextAction, $field);
        }else if ($this->existsValue($value, $field)) {
            $result = $this->get($this->get($value, $result, $action, $field), $result, $nextAction, $field);
        }
        
        return $result;
    }
    
    
    /**
     * 根据action和actionValue获取 值（map下的key）
     *
     * @param $field
     * @param $action
     * @param $actionValue
     * @return bool|int|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getValueByActionValue($actionValue, $action = self::KEY, $field = '')
    {
        $field = $this->getField($field);
        if ($this->existsField($field)) {
            $map = $this->getMap($field);
            foreach ($map as $value => $item) {
                if (isset($item[$action]) && $item[$action] == $actionValue) {
                    return $value;
                }
            }
        }
        
        return false;
    }
    
    
    /**
     * 获取action和actionValue为true的value
     *
     * @param        $action
     * @param string $field
     * @return bool|int|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getValueByActionTrue($action, $field = '')
    {
        return $this->getValueByActionValue(true, $action, $field);
    }
    
    
    /**
     * @return string
     */
    public function getField($field = '')
    {
        return !empty($field) ? $field : $this->field;
    }
    
    /**
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;
        
        return $this;
    }
    
    
}