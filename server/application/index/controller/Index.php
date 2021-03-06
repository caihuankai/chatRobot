<?php
namespace app\index\controller;

use think\Config;
use think\Request;
use app\common\model\Users;

class Index extends App
{   
    /**
    * array:
    * @array $key (requirement) 注册之后在机器人接入页面获得(32位)
    * @array $info (requirement)请求内容，编码方式为 UTFUTFUTF-8 (1-30位)
    * @array $userid (requirement) 开发者给自己的用户分配的唯一标志 (1-32位)
    * @array $loc (optional) 地址
    *
    * @param $posturl (requirement) 接口，默认为"http://www.tuling123.com/openapi/api";
    *
    * @return string
    */
    function call_tuling_api( $array,$posturl="http://www.tuling123.com/openapi/api" ){
        if (empty($array) || !is_array($array)){
            return 'ERROR: Sorry , your param are error. --- nosee';
        }
        $jsoninfo = json_encode($array);  //把传入的数据进行json编码
        //模拟post请求
        $ch = curl_init();//初始化curl
        if ( $ch === FALSE ){
            return 'ERROR: Sorry , you cannot open curl. --- nosee';
        }
        curl_setopt($ch, CURLOPT_URL,$posturl); //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);    //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-type:application/json;charset=utf-8",
            "Content-Length: " . strlen($jsoninfo)
        ));     //设置head头的请求数据格式为json
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);   //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsoninfo);
        $data = curl_exec($ch);   //运行curl 返回请求的json数据
        $json = json_decode($data);  //把json数据转为php的对象类型
        curl_close($ch);   //关闭curl
        $content = $json->text;  //获取返回的文本信息
        //处理返回的数据---超链接
        if (!empty($json->url)){
            $content .= "<a href=' ".$json->url." '>打开页面 </a>";
        }
        //处理返回的数据---列表
        if (!empty($json->list)){
            $i=0;
            //新闻类
            if ($json->code == 302000) {
                $newinfo='';
                foreach ($json->list as $list_item) {
                    $newinfo.="\n【".++$i."】<a href=' ".$list_item->detailurl." '>".$list_item->article."</a>";

                    if ($i==5)   break;
                }
                $content .=$newinfo;
            }
            //菜谱类
            if ($json->code == 308000) {
                $menuinfo='';
                foreach ($json->list as $list_item) {
                    $menuinfo.="\n【".++$i."】 <a href=' ".$list_item->detailurl." '>".$list_item->name."</a>\n".$list_item->info;

                    if ($i==5)   break;
                }
                $content .=' -- 共'.count($json->list)."项\n".$menuinfo;
            }
        }
        return $content;
    }
    public function _empty($name) 
    {
        echo 'cant found method: '.$name;
    }
    
    public function index()
    {
        $arr = array(

            'key' => '44f49fc37dd8487382f5875d460f138d',//这里填写自己的机器人密钥

            'info' => '明天',

            'userid' => '9934455',

            'loc' => ''

        );

        echo  call_tuling_api($arr);
    }
    
    public function test()
    {
        return $this->fetch();
    }
    
    public function a()
    {
        //$u = Users::get(1);
        //var_dump($u->getData(), $this->Users->get(1)->getData());
        //var_dump($this->Users->abc(), $this->Users->get(1)->getData());
        
        $a = $this->Users->haha();
        $a = Config::get();
        
        var_dump($a);
        
        //$this->kk()->abc();
        //var_dump(Config::get());
        $this->assign('domain', $this->request->url(true)); 
        //return $this->fetch('a');
        //return $this->fetch('a');
    }
    
    public function hello($id, $name)
    {
        //var_dump($_REQUEST);
        //var_dump($id);
        //var_dump($_REQUEST['id']);
        //$this->redirect('News/category', ['cid'=>2]); 重定向
        // var_dump($id, $name);
        /*
        $obj = \think\Loader::controller('Index');
        return $obj->a();
        */
        
        var_dump(Request::instance()->session());
        exit();
        
        var_dump(Request::instance()->param());
        exit();
        
        return \think\Loader::action('index/a', []);
        return $id;
    }
    
    public function miss() 
    {
        //return 'i will miss u';
        
        $request	=	Request::instance();
        //	获取当前域名
        echo	'domain:	'	.	$request->domain()	.	'<br/>';
        //	获取当前入口文件
        echo	'file:	'	.	$request->baseFile()	.	'<br/>';
        //	获取当前URL地址	不含域名
        echo	'url:	'	.	$request->url()	.	'<br/>';
        //	获取包含域名的完整URL地址
        echo	'url	with	domain:	'	.	$request->url(true)	.	'<br/>';
        //	获取当前URL地址	不含QUERY_STRING
        echo	'url	without	query:	'	.	$request->baseUrl()	.	'<br/>';
        //	获取URL访问的ROOT地址
        echo	'root:'	.	$request->root()	.	'<br/>';
        //	获取URL访问的ROOT地址
        echo	'root	with	domain:	'	.	$request->root(true)	.	'<br/>';
        //	获取URL地址中的PATH_INFO信息
        echo	'pathinfo:	'	.	$request->pathinfo()	.	'<br/>';
        //	获取URL地址中的PATH_INFO信息	不含后缀
        echo	'pathinfo:	'	.	$request->path()	.	'<br/>';
        //	获取URL地址中的后缀信息
        echo	'ext:	'	.	$request->ext()	.	'<br/>';
    }
}
