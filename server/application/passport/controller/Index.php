<?php
namespace app\passport\controller;

use think\Config;
use think\Request;
use app\common\model\Users;

class Index extends App
{
    public function a() 
    {
        /*
        $u = new \app\passport\model\User();
        $a = 'dfsfbvc3';
        $b = $u->getUserId($a);
        var_dump($a, $b);
        */
        
        /*
        $a = $u->getByname('agds23sfdsf');
        // $a = $u->exists(1101);
        $b = $u->getByname('agds23sf322');
        
        $c = $u->add('13800138000');
        var_dump($a, $b, $c);
        */
        /**/
        // $u->deleteTable();
        // $u->createTable();
        // echo 'a';
        
        $client = new \Yar_client('http://api.mc.net/Yarserver/yar');
        //$result = $client->index('index/a', json_encode(['a'=>'lalala']));
        //$result = $client->index('index/a', [1,2,3]);
        //$result = $client->run('a');
        $action = 'index/a';
        $param = ['a'=>3, 'b'=>3];
        echo 'action: '.$action."\n";
        echo 'param: ';
        var_dump($param);
        echo "\n";
        $result = $client->handle($action, $param);
        var_dump($result);
        
        exit();
    }
    
    public function test()
    {
        return $this->fetch();
    }
    
    public function run() 
    {
        // 验证码 session存储
        // 注册
        exit();
        set_time_limit(0);
        $n = 20000;
        $index = 128000109;
        $login = new \app\passport\model\Login();
        $member = new \app\passport\model\Member();
        
        for ($i=1; $i<$n; $i++){
            $tel = $index+$i;
            $info = $member->register();
            if (!$info) {
                echo 'error!';
                break;
            }
            $member_id = $info['id'];
            $data = [
                'tel' => $tel,
                'member_id' => $member_id,
            ];
            $login->create($data);
        }
    }
    
    
}
