<?php
namespace helper;


/**
 * 判断空可用$this->count或isset()
 *
 * @package helper
 */
class JsonEmptyObject extends \ArrayIterator implements \JsonSerializable
{
    protected $returnArray = false;
    
    public function returnArray($bool = true)
    {
        $this->returnArray = $bool;
        
        return $this;
    }
    
    
    /**
     * 返回数组或对象
     *
     * @return array|\stdClass
     */
    public function jsonSerialize()
    {
        return $this->count() || $this->returnArray ? $this->getArrayCopy() : new \stdClass();
    }
    
    
}