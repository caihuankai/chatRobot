<?php

/**
 * 重写\think\Collection
 */
class CollectionBase extends \think\Collection
{
    /**
     * @inheritDoc
     */
    public function column($column_key, $index_key = null)
    {
        // $this->items是对象所以无法使用系统array_column函数
        $result = [];
        foreach ($this->items as $row) {
            $key    = $value = null;
            $keySet = $valueSet = false;
            if (null !== $index_key && isset($row[$index_key])) {
                $keySet = true;
                $key    = (string)$row[$index_key];
            }
            if (null === $column_key) {
                $valueSet = true;
                $value    = $row;
            } elseif (
                is_array($row) && array_key_exists($column_key, $row) ||
                $row instanceof \think\Model && isset($row[$column_key])
            ) {
                $valueSet = true;
                $value    = $row[$column_key];
            }
            if ($valueSet) {
                if ($value instanceof \think\Model){
                    $value = $value->toArray();
                }
                
                if ($keySet) {
                    $result[$key] = $value;
                } else {
                    $result[] = $value;
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * 和$diffData求差值，保留$diffData
     *
     * @param       $columnKey
     * @param array $diffData
     * @return array
     * @author aozhuochao
     */
    public function diffData(array $diffData, $columnKey)
    {
        if (empty($columnKey)) {
            return [];
        }
    
        $data = $this->column($columnKey);
        if (empty($diffData)) {
            return $data;
        }
        
        
        return array_diff($diffData, $data);
    }
    
}