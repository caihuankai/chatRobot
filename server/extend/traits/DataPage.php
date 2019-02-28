<?php

namespace traits;

/**
 * 内部分页，可用于脚本分页处理数据
 *
 * @package traits
 */
trait DataPage
{
    
    protected function dataPageTrait(\Closure $func, $num = 2000)
    {
        $page = 0;
        
        for (;;){
            ++$page;
            if (!$func($page, $num)) {
                break;
            }
        }
    }
    
}