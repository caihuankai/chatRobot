<?php


/**
 * 修复composer的think的mongo库报错
 *
 * Class MongoConnection
 */
class MongoConnection extends \think\mongo\Connection
{
    public function getQuery($model = 'db', $queryClass = '')
    {
        return $this->model($model, $queryClass);
    }
    
    
    public function model($model = 'db', $queryClass = '')
    {
        if (!isset($this->query[$model])) {
            $class               = $queryClass ?: $this->config['query'];
            $this->query[$model] = new $class($this, '');
        }
        
        return $this->query[$model];
    }
    
}