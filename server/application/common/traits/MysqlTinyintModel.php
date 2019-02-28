<?php

namespace app\common\traits;

/**
 * Class MysqlTinyintModel
 *
 * @package app\common\traits
 */
trait MysqlTinyintModel
{
//    protected static $mysqlTinyintMap = [];
    
    private $mysqlTinyint = null;
    
    
    /**
     * @return \helper\Tinyint
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getMysqlTinyint($field = '')
    {
        /** @var \helper\Tinyint $tinyint */
        $tinyint = null;
        if (is_null($this->mysqlTinyint) || !isset($this->mysqlTinyint[$field])) {
            $this->mysqlTinyint[$field] = \helper\Tinyint::instance([static::class, $field]);
            $tinyint = $this->mysqlTinyint[$field];
            $tinyint->setMap($this->getMysqlTinyintMap());
        }else{
            $tinyint = $this->mysqlTinyint[$field];
        }
        $field && $tinyint->setField($field);
        
        return $tinyint;
    }
    
    
    protected function getMysqlTinyintMap()
    {
        static $bool = true;
    
        if ($bool){
            if (static::$mysqlTinyintMap !== self::$mysqlTinyintMap){
                static::$mysqlTinyintMap = arrayMergeRecursive(static::$mysqlTinyintMap, self::$mysqlTinyintMap);
            }else if (($parent = get_parent_class(self::class)) && method_exists($parent, 'getMysqlTinyintMap') && static::$mysqlTinyintMap !== parent::$mysqlTinyintMap){
                static::$mysqlTinyintMap = arrayMergeRecursive(static::$mysqlTinyintMap, parent::$mysqlTinyintMap);
            }
            $bool = false;
        }
    
        return static::$mysqlTinyintMap;
    }
    
}