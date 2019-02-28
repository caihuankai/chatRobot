<?php

namespace helper;


class RangeData
{
    
    const POSITIVE_INFINITY = '+';
    const NEGATIVE_INFINITY = '-';
    
    
    /**
     * [0, 100, 1000, 3000, 5000, 8000, 20000, 60000]
     * 
     * @var array 
     */
    protected $rangeArr = [];
    
    /**
     * RangeData constructor.
     *
     * @param array $rangeArr
     */
    public function __construct(array $rangeArr)
    {
        $this->rangeArr = $rangeArr;
    }
    
    
    /**
     * 获取key
     *
     * @param int $rangeNum
     * @return int|string
     */
    public function getKey($rangeNum)
    {
    
        foreach ($this->rangeArr as $key => $item) {
            if ($rangeNum < $item){
                return $key;
            }
        }
        
        // 比最后一个大
        return $key;
    }
    
    
    /**
     * 获取key的整数
     *
     * @param int $rangeNum
     * @return int|string
     */
    public function getIntKey($rangeNum)
    {
        return intval($this->getKey($rangeNum));
    }
    
    
    /**
     * 获取下一个key
     * （下一级）
     *
     * @param int $rangeNum
     * @return bool|int|string
     */
    public function getNextKey($rangeNum)
    {
        $bool = false;
        foreach ($this->rangeArr as $key => $item) {
            if ($bool){
                return $key;
            }else if ($rangeNum < $item){
                $bool = true;
            }
        }
    
        // 比最后一个大
        return false;
    }
    
    
    /**
     * 根据key获取下一级key
     *
     * @param $paramKey
     * @return bool|int|string
     * @author aozhuochao
     */
    public function getNextKeyByKey($paramKey)
    {
        $bool = false;
        foreach ($this->rangeArr as $key => $item) {
            if ($bool){
                return $key;
            }else if ($paramKey == $key){
                $bool = true;
            }
        }
    
        // 比最后一个大
        return false;
    }
    
    
    /**
     * 距离下一个值还差多少
     *
     * @param int $rangeNum
     * @return int
     */
    public function diffNext($rangeNum)
    {
        foreach ($this->rangeArr as $item) {
            if ($rangeNum < $item){
                return $item - $rangeNum;
            }
        }
        
        // 比最后一个大
        return 0;
    }
}