<?php

namespace helper;

/**
 * 优化php-html-generator类
 *
 * @see https://packagist.org/packages/airmanbzh/php-html-generator
 * @package helper
 */
class Html extends \HtmlGenerator\HtmlTag implements \JsonSerializable
{
    
    /**
     * 设置元素的style
     *
     * @var array
     */
    protected $style = [];
    
    /**
     * 显示字符串时执行，返回false即当前不输出
     *
     * @var callable|null
     */
    protected $toStringCallback = null;
    
    
    /**
     * 生成select
     *
     * @param array $data
     * @return \HtmlGenerator\Markup|string
     */
    public static function select(array $data, $selected = null, $name = '')
    {
        if (empty($data)) {
            return '';
        }
        
        $select = self::createElement('select');
    
        if (!empty($name)) {
            $select->id($name)->attr('name', $name);
        }
        
        foreach ($data as $val => $text) {
            $option = $select->addElement('option')->attr('value', $val)->text($text);
            
            if (!is_null($selected) && $val == $selected){
                $option->attr('selected', true);
            }
        }
        
        return $select;
    }
    
    

    
    /**
     * 生成js对象，需自己处理字符串内部引号
     *
     * @param array $jsObject
     * @return array|string
     */
    public function jsObject(array $jsObject)
    {
        if (empty($jsObject)) {
            return [];
        }
    
        $arr = [];
        foreach ($jsObject as $key => $item) {
            $html = "{$key}: ";
            if (is_string($item)) {
                $html .= $item;
            } else if(is_callable($item)) {
                $html .= $item();
            }else{
                $html .= '""';
            }
            $arr[] = $html;
        }
        
        return '{' . join($arr, ',') . '}';
    }
    
    
    /**
     * 将class改为数组
     *
     * @param string $value
     * @return $this
     */
    public function addClass($value)
    {
        if (isset($this->attributeList['class']) && !is_array($this->attributeList['class'])){
            $this->attributeList['class'] = (array)$this->attributeList['class'];
        }
        parent::addClass($value);
        
        return $this;
    }
    
    
    /**
     * Add element at an existing Markup
     * 返回插入的元素
     *
     * @param Html|string $tag
     * @param bool $unShift
     * @return Html instance
     */
    public function addElement($tag = '', $unShift = false)
    {
        $htmlTag = (is_object($tag) && $tag instanceof self) ? $tag : new static($tag);
        $htmlTag->_top = $this->getTop();
        $htmlTag->_parent = &$this;
        
//        $this->content[] = $htmlTag;
        if ($unShift){
            array_unshift($this->content, $htmlTag);
        }else{
            array_push($this->content, $htmlTag);
        }
        
        return $htmlTag;
    }
    
    
    /**
     * 设置display，默认隐藏
     *
     * @param string $display
     * @return $this
     */
    public function setDisplay($display = 'none')
    {
        $this->set('style', 'display: none');
        
        return $this;
    }
    
    
    
    /**
     * 设置data
     *
     * @param string|array $attribute
     * @param $value
     * @return $this
     */
    public function setData($attribute, $value = null)
    {
        $prefix = 'data-';
        if(is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this[$prefix . $key] = $value;
            }
        } else {
            $this[$prefix . $attribute] = $value;
        }
        
        return $this;
    }
    
    /**
     * (Re)Define an attribute or many attributes
     *
     * @param string|array $attribute
     * @param string $value
     * @return $this
     */
    public function set($attribute, $value = null)
    {
        parent::set($attribute, $value);
        
        return $this;
    }
    
    /**
     * @param array|string $attribute
     * @param null         $value
     * @param bool         $add        给一个属性追加字符串
     * @return $this|\HtmlGenerator\Markup
     */
    public function attr($attribute, $value = null, $add = false)
    {
        if ($add){
            $this[$attribute] .= $value;
        }
        
        return parent::attr($attribute, $value);
    }
    
    
    /**
     * 设置为什么元素
     *
     * @param $tag
     * @return $this
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function tag($tag)
    {
        $this->tag = $tag;
        
        return $this;
    }
    
    
    public function setNameEqId()
    {
        $id = $this->offsetGet('id');
        if (!is_null($id)) {
            $this->offsetSet('name', $id);
        }
        
        return $this;
    }
    
    
    
    
    /**
     * 创建元素
     *
     * @param string $tag
     * @return mixed|\HtmlGenerator\Markup|self
     */
    public static function createElement($tag = '')
    {
        return parent::createElement($tag);
    }
    
    
    /**
     * 一个空格
     *
     * @return \HtmlGenerator\Markup
     */
    public static function space($num = 1)
    {
        return self::createElement('span')->text(str_repeat('&nbsp;', $num));
    }
    
    
    /**
     * @param string $tag
     * @param array  $content
     * @return mixed
     */
    public static function __callStatic($tag, $content)
    {
        $ele = self::createElement();
        if (method_exists($ele, $tag)) {
            return $ele->$tag(...(array)$content);
        }
        
        return parent::__callStatic($tag, $content);
    }
    
    
    public function jsonSerialize()
    {
        return $this->toString();
    }
    
    /**
     * Shortcut to set('id', $value)
     * @param string $value
     * @return $this instance
     */
    public function id($value)
    {
        parent::id($value);
        
        return $this;
    }
    
    /**
     * 设置text，append
     *
     * @param string $value
     * @return $this
     */
    public function text($value)
    {
        parent::text($value);
        
        return $this;
    }
    
    
    public function setToStringCallback(callable $func = null)
    {
        $this->toStringCallback = is_callable($func) ? $func : null;
        
        return $this;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function toString()
    {
        try{
            if (!is_null($this->toStringCallback) && call_user_func($this->toStringCallback) === false){
                return '';
            }
            
            $style = $this->getStyle();
            if (!empty($style)) {
                $this->attr('style', $style, true);
            }
            
            return parent::toString();
        }catch (\Exception $exception){
            return '';
        }
    }
    
    /**
     * @return string
     */
    public function getStyle()
    {
        $style = '';
    
        foreach ($this->style as $key => $item) {
            $style .= $key . ':' . $item . ';';
        }
    
        return $style;
    }
    
    /**
     * @param      $key
     * @param null $value
     * @return $this
     */
    public function setStyle($key, $value = null)
    {
        if (is_array($key)) {
            $this->style = array_merge($this->style, $key);
        }else{
            $this->style[$key] = $value;
        }
        
        return $this;
    }
    
    
}