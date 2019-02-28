<?php


/**
 * 执行c++方法
 *
 * Class CPlusPlusProtocol
 */
class CPlusPlusProtocol
{
    use \traits\Singleton;
    
    public function getClient()
    {
        \think\Loader::import('Thrift.ThriftStub.RPC_Admire_Push.TRoomSvr', EXTEND_PATH);
        
        $transport = new \Thrift\Transport\THttpClient(config('RPC_IP'), config('ADMIRE_RPC_PORT'));
        $transport->setTimeoutSecs(30);
        $protocol = new \Thrift\Protocol\TBinaryProtocol($transport);
        $client = new \TRoomSvrClient($protocol);
    
        $transport->open();//建立连接
    
        return $client;
    }
    
    
    /**
     * 请求c++方法
     *
     * @param       $method
     * @param array ...$args
     * @return bool
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function callFunc($method, ...$args)
    {
        try {
            $bool = !!call_user_func_array([$this->getClient(), $method], $args);
            \think\Log::write('调用RPC成功:' . $method . '参数：' . var_export($args, true), 'rpc');
            
            return $bool;
        } catch (\Exception $exception) {
            \think\Log::write('调用RPC失败:' . $exception->getMessage() . '参数：' . var_export($args, true), 'rpc');
            return false;
        }
    }
    
}