<?php
namespace app\common\model;

use think\Model;

/**
 * Class ModelBase
 *
 * @method static \ThinkPHP\Query alias($alias) 指定数据表别名
 * @method $this union($union, $all = false) 查询SQL组装 union
 * @method static \ThinkPHP\Query join($join, $condition = null, $type = 'INNER') 查询SQL组装 join
 * @method void startTrans() 开启事务
 * @method void commit() 提交事务
 * @method void rollback() 回滚事务
 * @method integer|string insert(array $data, $replace = false, $getLastInsID = false, $sequence = null) 插入数据
 * @method integer|string insertGetId(array $data, $replace = false, $sequence = null) 插入记录并获取自增ID
 * @method integer|string insertAll(array $dataSet) 批量插入记录
 * @method $this fetchClass($class) 指定数据集
 * @package app\common\model
 */
class ModelBase extends Model
{


    static protected $localEvent = [];
    
    
    protected function initialize()
    {
        $this->autoEvent();
    }

    static protected function init()
    {
    }
    
    
    /**
     * 调用event文件夹下的对应的类
     *
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function autoEvent()
    {
        // 绑定event
//        $name = basename(str_replace('\\', '/', get_called_class()));
        $class = '\\' . str_replace('\\model\\', '\\event\\', static::class);
        \helper\ReflectionUtils::instance()->getPublicMethodByClass($class, function (\ReflectionMethod $reflectionMethod, $class){
            if (!isset(static::$localEvent[static::class])){
                static::$localEvent[static::class] = $class;
            }
            
            static::event($reflectionMethod->getName(), $reflectionMethod->getClosure($class));
        });
    }
    
    
    
    /**
     * @return \app\admin\event\EventApp|[]
     */
    public function getLocalEvent()
    {
        return isset(static::$localEvent[static::class]) ? static::$localEvent[static::class] : null;
    }
    
    
    /**
     * 获取当前模型的数据库查询对象Query
     *
     * @return \ThinkPHP\Query
     * @author aozhuochao
     */
    public function db()
    {
        // 在model的构造函数里调用
        /** @var \ThinkPHP\Query $query */
        $query = parent::db();
    
        // 每次都要赋值，$this修改Query无法获取到
        $query->setModelClass($this);
        
        return $query;
    }
    
    
    /**
     * 处理model的data为数组
     *
     * @param $data
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function dataToArray($data)
    {
        if (empty($data)) {
            return [];
        }
    
        return is_object($data) && $data instanceof \think\Model ? $data->toArray() : (array)$data;
    }
    
    
    /**
     * 向上（下）拉取分页数据
     *
     * @param           $lastField // 排序和过滤的字段名
     * @param string    $orderType // order排序
     * @param           $limit // 一次多少数据
     * @param           $lastId // 最后id
     * @param int       $action 1为向下，2为向上
     * @return ModelBase
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function lastKeyPage($lastField, $orderType = 'desc', $limit = null, $lastId = null, $action = null)
    {
        $getFunc = function ($key, $default, $param) {
            return !is_null($param) ? request()->param($key, $default) : $param;
        };
        $lastId = intval($getFunc('listId', 0, $lastId));
        $limit = intval($getFunc('limit', 10, $limit));
        $action = $getFunc('action', 1, $action);
        
        if (!empty($lastId)) {
            if ($orderType == 'desc' ? '<' : '>') {
                $whereAction = ($action == 1 ? '<' : '>');
            } else { // asc
                $whereAction = ($action == 1 ? '>' : '<');
            }
            $lastWhere = [$whereAction, $lastId];
        } else {
            $lastWhere = ['>', 0];
        }
        
        $this->where([$lastField => $lastWhere])->order($lastField, $orderType)->limit($limit);
        
        return $this;
    }

    
    
    /**
     * 处理形参上的model
     *
     * @param $model
     * @return ModelBase
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function disFormalModel($model)
    {
        return is_null($model) || !($model instanceof \app\common\model\ModelBase)?$this:$model;
    }
    
    
    /**
     * 设置model的错误信息
     *
     * @param $msg
     * @return false
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function setError($msg)
    {
        $this->error = $msg;
        
        return false;
    }
    
    
    /**
     * @inheritdoc
     */
    public function save($data = [], $where = [], $sequence = null)
    {
        $this->handleSaveFuncOfData($data, $where, true);
        return parent::save($data, $where, $sequence);
    }
    
    
    /**
     * 处理save方法的data和where
     *
     * @param array $data
     * @param array $where
     * @return array|$this
     * @author aozhuochao
     */
    public function handleSaveFuncOfData($data = [], $where = [], $setBool = false)
    {
        static $tempData = [], $tempWhere = [];
    
        if ((!empty($data) && !empty($where)) || $setBool) {
            $tempData = is_array($data) ? $data : [];
            $tempWhere = is_array($where) ? $where : [];
        
            return $this;
        } else {
            return [
                'data' => $tempData + $this->getData(),
                'where' => $tempWhere,
            ];
        }
    }
    
    
    /**
     * 获取返回或者影响的记录数
     *
     * @return int
     * @author aozhuochao
     */
    public function getNumRows()
    {
        /** @var \think\db\Query $this */
        $connection = $this->getConnection();
        
        return $connection->getNumRows();
    }
    
}
