<?php
namespace app\index\controller;

use think\Request;

class Error
{
    public function index(Request $request) 
    {
        $controller = $request->controller();
        return $this->run($controller);
    }
    
    protected function run($controller) 
    {
        echo 'cant found controller: '.$controller;
        ;// 找不到控制器的处理
    }
    
}
