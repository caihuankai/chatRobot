<?php

namespace app\common\traits;

/**
 * Class Common
 *
 * @property \think\Request request
 * @package app\common\traits
 */
trait Common
{
    
    
    
    /**
     * 获取当前控制器的model
     * 必须在module下有model
     *
     * @return \app\common\model\ModelBase|\think\db\Query|\app\common\traits\MysqlTinyintModel
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getCurrentModel()
    {
        /** @var \think\Request $request */
        if (property_exists($this, 'request')) {
            $request = $this->request;
            $controller = $request->controller();
        } else if (basename(static::class) && class_exists(\think\Loader::parseClass(request()->module(), 'model', basename(static::class)))){
            $controller = basename(static::class);
        } else {
            $request = request();
            $controller = $request->controller();
        }
        
        return model($controller);
    }
    
    
    /**
     * 获取当前模型的pk名
     *
     * @param string $default
     * @return array|mixed|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getCurrentModelPk($default = 'id')
    {
        $pk = $this->getCurrentModel()->getPk();
        
        return !empty($pk) ? $pk : $default;
    }
    
    
}