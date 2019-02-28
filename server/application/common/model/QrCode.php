<?php
namespace app\common\model;

use app\common\model\ModelBs;
use app\wechat\model\AccessToken;
use app\wechat\controller\UpdateAccessToken;
use app\common\model\Live;
use app\common\controller\JsonResult;
use app\wechat\model\Course as CourseQ;
use app\common\model\Invitationcard;
use app\wechat\model\LiveFocus;
use app\common\model\InvitationcardUser;
use app\common\model\Fans;
use app\wechat\model\InvitationcardRep;
use think\config;
use app\wechat\model\ThirdLogin as ThirdLoginM;
use app\common\model\User;
use app\wechat\model\Viewpoint as ViewpointQ;

/**
 * 二维码生成类
 * @author xiaok
 * @time 2017/06/13
 */
class QrCode extends ModelBs{

    //根据判断是否创建直播间推送不同消息
    public function sendSubMsg($FromUserName=''){
        $ThirdLoginModel = new ThirdLoginM();
        $data = $ThirdLoginModel->alias('t')->join('live l','l.user_id = t.user_id')->where('t.open_id',$FromUserName)->find();
        if(empty($data)){
            $msg = "HI，欢迎来到大策略！大策略是专注于金融投资理财领域专业的知识分享平台。1分钟创建专属的直播间，
即可享受知识变现。这里也是你学习投资理财的宝库，大策略倡导做你最关注的知识，这里的内容，你一定受益。
我想讲课：\n<a href='".config('WX_DOMAIN')."/#/personalCenter/createLive/0'>1分钟创建你的空间吧</a>
我想听课：\n<a href='".config('WX_DOMAIN')."/#/index/0'>首页[点击这里]去发现吧！</a>";
        }else{
            $msg = "HI，欢迎来到大策略！大策略是专注于金融投资理财领域专业的知识分享平台。1分钟创建专属的直播间，
即可享受知识变现。这里也是你学习投资理财的宝库，大策略倡导做你最关注的知识，这里的内容，你一定受益。
我想讲课：\n<a href='".config('WX_DOMAIN')."/#/personalCenter'>1分钟创建你的空间吧</a>
我想听课：\n<a href='".config('WX_DOMAIN')."/#/index/0'>首页[点击这里]去发现吧！</a>";
        }
        return $msg;
    }
    //推送客服欢迎文字消息
    public function welcome($FromUserName){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $msg = $this->sendSubMsg($FromUserName);
        $jsonstr = '{"touser": "'.$FromUserName.'", "msgtype": "text", "text": {"content": "'.$msg.'"}}';
        $strtest = $this->https_post($url, $jsonstr);
        return $strtest;
    }

    /**
     * 推送客服欢迎图文消息
     * @param  [type] $FromUserName [description]
     * @return [type]               [description]
     */
    public function welcomeNews($FromUserName){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $title = "无策略不投资，你想学的投资方法开讲啦！";
        $description = "被套不怕，解套不烦，牛股很多，抓住不难。\n\n众多国内股票期货界一线名师倾囊相授：买哪只、什么时候买、买多少、什么时候卖？让你彻底告别韭菜生涯。\n\n开播时间：逢工作日9:00-15:00\n\n主讲嘉宾：国内知名金融机构高级分析师\n\n点击即可倾听名师干货";
        //推送内容
        $jsonstr = '{"touser":"'.$FromUserName.'","msgtype":"news",
    "news":{"articles": [{"title":"'.$title.'","description":"'.$description.'","url":"'.config('WX_DOMAIN').'/Client/welcome","picurl":"'.config('WX_DOMAIN').'/images/default/new_welcome.jpg"}]}}';
        $strtest = $this->https_post($url, $jsonstr);
        return $strtest;

    }
    /**
     * 发送单张图片
     * @return [type] [description]
     */
    public function pusImage($open_id){
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token.'';
        $str = '{
          "touser":"'.$open_id.'",
          "msgtype":"image",
          "image":
          {
            "media_id":"'.config('material_media_id').'"
          }
        }';
        $this->https_post($url, $str);
    }
    /**
     * 推送单篇图文消息
     * @param  [type] $FromUserName [description]
     * @param  [type] $data         [description]
     * @return [type]               [description]
     */
    public function pushImageMessage($FromUserName,$data){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $title = $data['title'];
        $description = $data['content'];
        //推送内容
        $jsonstr = '{"touser":"'.$FromUserName.'","msgtype":"news",
    "news":{"articles": [{"title":"'.$title.'","description":"'.$description.'","url":"'.$data['jump_url'].'","picurl":"'.$data['image'].'"}]}}';
        $strtest = $this->https_post($url, $jsonstr);
        return $strtest;
    }
    //1.0.8新增批量推送文字消息
    public function pushMessage($data){
        $userList = $this->pushUserList($data);
        if(empty($userList)){
            return;
        }
        if($data['type'] == 0){
            foreach ($userList as $k => $v) {
                $this->pushMsg($v['open_id'],$data['content']);
            }
        }
        if($data['type'] == 2){
            foreach ($userList as $k => $v) {
                $this->pushImageMessage($v['open_id'],$data);
            }
        }
        if($data['type'] == 3){
            $strArray = json_decode($data['content'],true);
            $str = '';
            foreach ($strArray as $k => $value) {
                $str .= '{"title":"'.$value['title'].'","description":"","url":"'.$value['jump_url'].'","picurl":"'.$value['image'].'"},';
            }
            $str = substr($str, 0, -1);
            foreach ($userList as $k => $v) {
                $this->pushImageArrayMessage($v['open_id'],$str);
            }
        }

    }
    //获取接收用户
    public function pushUserList($data){
        $where = array();
        $where['UNIX_TIMESTAMP(u.last_login_time)'] = ['>=',strtotime(date('Y-m-d H:i:s'))-172800];
        if($data['push_target'] == 3){
            $where['u.user_id'] = $data['user_id'];
        }
        elseif ($data['push_target'] == 1) {
            $where['u.inpour_total'] = ['<>',0];
        }
        elseif ($data['push_target'] == 2) {
            $where['u.inpour_total'] = 0;
        }
        $where['t.open_id'] = ['<>',''];
        $UserModel = new User();
        $userList = $UserModel->alias('u')
        ->join('third_login t','t.user_id = u.user_id','left')
        ->where($where)
        ->field('t.open_id')
        ->select();
        return $userList;
    }
    /**
     * 推送多篇图文消息
     * @param  [type] $FromUserName [description]
     * @param  [type] $data         [description]
     * @return [type]               [description]
     */
    public function pushImageArrayMessage($FromUserName,$data){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        //推送内容
        $jsonstr = '{"touser":"'.$FromUserName.'","msgtype":"news",
    "news":{"articles": ['.$data.']}}';
        $strtest = $this->https_post($url, $jsonstr);
        return $strtest;
    }

    //v1.3 消息推送
    public function pushMsg($FromUserName,$msg){
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
        $jsonstr = '{"touser": "'.$FromUserName.'", "msgtype": "text", "text": {"content": "'.$msg.'"}}';
        $strtest = $this->https_post($url, $jsonstr);
        return $strtest;
    }

    //创建邀请卡 @param live_id:直播间id class_id：课程id type：邀请卡类型 1课程邀请粉丝 2直播间邀请粉丝 3邀请嘉宾 4平台邀请用户 user_id：二维码创建人
    public function index($live_id=1,$class_id=1,$type=2,$user_id=1706743){
        $live_id = (int)$live_id;
        $class_id = (int)$class_id;
        $type = (int)$type;
        $user_id = (int)$user_id;
        $QRcodeData['lname'] = '';
        $QRcodeData['uid'] = 0;
        $QRcodeData['begin_time'] = '';
        $QRcodeData['cname'] = '';
        $QRcodeData['master_userid'] = '';
        $class_type = 1;

        if($type == 2){
            //获取信息 验证直播间是否存在
            $LiveModel = new Live();
            $LiveInfo = $LiveModel->isExist($live_id);
            if(empty($LiveInfo)){
                return $this->errorJson(JsonResult::ERR_LIVE_NULL);
            }
            $QRcodeData['lname'] = $LiveInfo['name'];
            $QRcodeData['master_userid'] = $LiveInfo['user_id'];
        }

        if($type == 1 || $type == 3){
            //获取信息 验证课程是否存在
            $classModel = new CourseQ();
            $classInfo = $classModel->isExist($class_id);
            if(empty($classInfo)){
                return $this->errorJson(JsonResult::ERR_CLASS_NULL);
            }
            $QRcodeData['master_userid'] = $classInfo['uid'];
            $QRcodeData['begin_time'] = $classInfo['begin_time'];
            $QRcodeData['cname'] = $classInfo['class_name'];
            $class_type = $classInfo['type'];
        }

        if($type == 5){
            $QRcodeData['master_userid'] = $user_id;
        }

        if($type == 8){
            $QRcodeData['master_userid'] = $user_id;
            $ViewpointM = new ViewpointQ();
            $QRcodeData['cname'] = $ViewpointM->where('id',$class_id)->value('title');
        }

        //获取二维码邀请卡需要保存信息 当前时间戳md5
        $QRcodeData['sn'] = getQrCodeNo();//二维码卡号 需唯一
        $QRcodeData['live_id'] = $live_id;
        $QRcodeData['class_id'] = $class_id;
        $QRcodeData['create_time'] = date('Y-m-d H:i:s',time());
        $QRcodeData['type'] = $type;
        $QRcodeData['state'] = 0;
        $QRcodeData['uid'] = $user_id;
        //生成二维码图片链接
        $QRcodeData['qrcode'] = $this->makeUrl($QRcodeData['sn']);
        //保存数据
        $InvitationcardModel = new Invitationcard();
        $status = $InvitationcardModel->insert($QRcodeData);
        if($status){
            $QRcodeData['class_type'] = $class_type;
            return $QRcodeData;
        }else{
            return ['code' => 1, 'data' => '', 'msg' => '获取二维码失败'];
        }
        
    }

	//@param scene_id:二维码卡号（需唯一 由php生成）
	public function makeUrl($scene_id){
	    $day = 60*60*24*30;//生成固定时间的带参数的二维码,过期时间30天
	    $qrcode = '{"expire_seconds": '.$day.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene_id.'}}}';//生成固定时间的带参数的二维码
	    
	    $access_token = $this->getAccessToken();
	    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
	    $request = $this->https_post($url, $qrcode);//发送请求，并获取返回值json，http://blog.csdn.net/qq_21119773/article/details/51681382有这个方法
	    $request = json_decode($request);//解析json
	     
	    $r_ticket = $request->ticket;//获取带参数二维码的凭证
	    $r_expire_seconds = $request->expire_seconds;//带参数二维码的有效时长，秒
	    $r_url= $request->url;//微信返回参数
	     
	    $imgurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$r_ticket;//带参数二维码的图片路径
	    return $imgurl;
	}

	//通过链接post获取数据
    public function https_post($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         }
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         $output = curl_exec($curl);
         curl_close($curl);
         return $output;
    }

    //通过链接get获取数据
    public function https_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         $output = curl_exec($ch);
         curl_close($ch);
         return $output;
     }

    //获取access_token
    public function getAccessToken(){
        $AccessTokenModel = new AccessToken();
        $UpdateAccessTokenController = new UpdateAccessToken();
        $AccessTokenInfo = $AccessTokenModel->find();

        //判断是否更新access_token
        if(!empty($AccessTokenInfo)){
            //access_token是否过期
            if($AccessTokenInfo['access_expires_time'] < time()+60){
                $update = $UpdateAccessTokenController->update();
                $AccessTokenInfo = $AccessTokenModel->find();
            }
        }else{
            $update = $UpdateAccessTokenController->update();
            $AccessTokenInfo = $AccessTokenModel->find();
        }
        $accessToken = $AccessTokenInfo['access_token'];
        return $accessToken;
    }

    //处理微信扫二维码推送事件
    public function HandleEvent($EventKey,$FromUserName,$CreateTime){
        /** @var \app\common\model\User $userModel */
        $userModel = model('common/User');
        //获取用户信息
        $ThirdLoginModel = new ThirdLoginM();
        $ThirdLoginInfo = $ThirdLoginModel->alias('t')
        ->join('user u','u.user_id = t.user_id')
        ->field('t.user_id,u.user_type')
        ->where('t.open_id',$FromUserName)
        ->find();
    
        if (empty($ThirdLoginInfo)) {
            return null;
        }

        //马甲身份拒绝扫码
        // if($ThirdLoginInfo['user_type'] == 2){
        //     return '你是马甲，别扫';
        // }

        $InvitationcardRepModel = new InvitationcardRep();
        //判断该二维码是否重复发送 解决二维码闪烁扫描问题
        $InvitationcardRepWhere['open_id'] = $FromUserName;
        $InvitationcardRepWhere['create_time'] = $CreateTime;
        try{
            $InvitationcardRepInsert = $InvitationcardRepModel->insert($InvitationcardRepWhere);
        }catch(\Exception $e){
            $InvitationcardRepModel->rollback();
            return null;
        }
        $InvitationcardModel = new Invitationcard();
        $InvitationcardUserModel = new InvitationcardUser();
        //关注model
        $LiveFocusModel = new LiveFocus();
        $InvitationCardInfo = $InvitationcardModel->getStaticInfo($EventKey);
        if(empty($InvitationCardInfo)){
            $str = '二维码信息不存在。';
        }
        //判断扫码人与直播间隶属人是否相同
        if($InvitationCardInfo['type'] == 1 || $InvitationCardInfo['type'] == 2 || $InvitationCardInfo['type'] == 3 || $InvitationCardInfo['type'] == 5){
            if($ThirdLoginInfo['user_id'] != $InvitationCardInfo['master_userid']){
                //用户是否已关注
                $isFocus = $LiveFocusModel->isFocus($InvitationCardInfo['live_id'],$ThirdLoginInfo['user_id']);
                if($isFocus == 0){
                    $addFocus = $LiveFocusModel->focus($InvitationCardInfo['live_id'],$ThirdLoginInfo['user_id'],1);
                }
            }
        }
        //判断二维码创建人与直播间隶属人是否相同
        // if($InvitationcardInfo['uid'] == $InvitationcardInfo['master_userid']){
            //如果相同 则判断用户是否已经为别人粉丝
            //只有讲师和流量主可以绑定粉丝
            //助教无法绑定讲师
            if($InvitationCardInfo['is_assistant'] == 2){
                // if($InvitationCardInfo['user_level'] == 2 || $InvitationCardInfo['user_level'] == 3){
                //     $FansModel = new Fans();
                //     $isFans = $FansModel->where('open_id',$FromUserName)->find();

                //     //马甲不绑定上级
                //     if($ThirdLoginInfo['user_type'] != 2){
                //         //不能成为自己粉丝
                //         if($ThirdLoginInfo['user_id'] != $InvitationCardInfo['uid']){
                //             //如果不存在 则该用户成为邀请者粉丝
                //             if(empty($isFans)){
                //                 $FansData['open_id'] = $FromUserName;
                //                 $FansData['invita_userid'] = $InvitationCardInfo['uid'];
                //                 $FansData['user_id'] = $ThirdLoginInfo['user_id'];
                //                 $saveFans = $FansModel->insert($FansData);
                //             }
                            
                            
                //             // 更新user.invite_user_id字段，只要字段为空就更新
                //             if (!empty($ThirdLoginInfo['user_id']) && !empty($InvitationCardInfo['uid'])) {
                //                 $userModelInfo = $userModel->where('user_id',$ThirdLoginInfo['user_id'])->find();
                //                 if($userModelInfo['invite_user_id'] == 0){
                //                     $userModel->update(['invite_user_id' => $InvitationCardInfo['uid']], ['user_id' => $ThirdLoginInfo['user_id']]);
                //                 }
                //             }
                //         }
                //     }
                // }
            }else{
                $InvitationcardUserModelData['create_user_id_type'] = 2;
            }
        // }
        //邀请卡来自课程
        $InvitationcardUserModelData['create_card_class'] = $InvitationCardInfo['class_id'];
        //邀请卡和被邀请人关联信息 $InvitationcardUserModelData
        $InvitationcardUserModelData['card_id'] = $EventKey;
        $InvitationcardUserModelData['create_user_id'] =$InvitationCardInfo['uid'];
        $InvitationcardUserModelData['get_user_id'] = $FromUserName;
        $InvitationcardUserModelData['user_id'] = $ThirdLoginInfo['user_id'];
        //判断用户是否已经被邀请
        $InvitationcardUserIsExist = $InvitationcardUserModel->where('get_user_id',$FromUserName)->where('card_id',$EventKey)->find();
        //判断是否保存邀请卡和被邀请人关联信息
        $InvitationcardUserIsExistStatus = 1;
        if(empty($InvitationcardUserIsExist)){
            $InvitationcardUserIsExistStatus = 0;
        }

        if($InvitationCardInfo['type'] == 1){
            if($InvitationcardUserIsExistStatus == 0){
                $InvitationcardUserModelStatus = $InvitationcardUserModel->insert($InvitationcardUserModelData);
            }
            //3.0修改推送内容
            $url = config('WX_DOMAIN')."/#/personalCenter/course/".$InvitationCardInfo['class_id'];
            $CourseModel = new CourseQ();
            $CourseInfo = $CourseModel->alias('c')
            ->join('course ca','ca.id = c.pid','left')
            ->field('c.class_name,c.pid,c.type,ca.class_name as p_class_name,ca.plan_num,c.plan_num as c_plan_num,c.seriesType')
            ->where('c.id',$InvitationCardInfo['class_id'])
            ->find();
            //大策略H5推送链接不同
            if(in_array($CourseInfo['seriesType'], [1,2])){
                $url = config('WX_DOMAIN')."/#/dacelueMini/courseDetail/".$InvitationCardInfo['class_id'];
            }
            if($CourseInfo['pid'] == 0 && $CourseInfo['type'] == 1){
                $str = "课程：【".$InvitationCardInfo["cname"]."】\n开播时间：".substr($InvitationCardInfo["begin_time"],0,16)."\n<a href='".$url."'>点此去课堂</a>";
            }
            if($CourseInfo['pid'] != 0 && $CourseInfo['type'] == 1){
                $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n课程安排：预计更新".$CourseInfo["plan_num"]."节\n<a href='".$url."'>点此去课堂</a>";
            } 
            if($CourseInfo['type'] == 2){
                $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n课程安排：预计更新".$CourseInfo["c_plan_num"]."节\n<a href='".$url."'>点此去课堂</a>";
            }             
            return $str;
        }

        if($InvitationCardInfo['type'] == 2){
            $InvitationcardUserModelData['create_card_class'] = $InvitationCardInfo['live_id'];
            if($InvitationcardUserIsExistStatus == 0){
                $InvitationcardUserModelStatus = $InvitationcardUserModel->insert($InvitationcardUserModelData);
            }
            $url = config('WX_DOMAIN')."/#/personalCenter/liveroom/".$InvitationCardInfo['live_id'];
            if($InvitationCardInfo['live_id'] == 9999){
                $url = config('WX_DOMAIN').'/#/publicOnlive';
            }
            $str = "您已成功接受[ ".$InvitationCardInfo["ualias"]." ]邀请\n关注：【".$InvitationCardInfo['lname']."】，即可收听最新课程\n"."<a href='".$url."'>点击此处关注直播间</a>";
            return $str;
        }

        if($InvitationCardInfo['type'] == 3){
            $urlCourse = config('WX_DOMAIN')."/#/personalCenter/course/".$InvitationCardInfo['class_id'];
            $url = config('WX_DOMAIN')."/#/onlive/".$InvitationCardInfo['class_id'];

            $CourseModel = new CourseQ();
            $CourseInfo = $CourseModel->alias('c')
            ->join('course ca','ca.id = c.pid','left')
            ->field('c.class_name,c.pid,c.type,ca.class_name as p_class_name,ca.plan_num,c.status,c.plan_num as c_plan_num,c.form')
            ->where('c.id',$InvitationCardInfo['class_id'])
            ->find();
            //ppt课程推送链接不同
            if($CourseInfo['form'] == 3){
                $url = config('WX_DOMAIN')."/#/pptOnlive/".$InvitationCardInfo['class_id'];
            }
            if($CourseInfo['status'] == 4){
                return $str = "感谢您的参与，本次课程《".$InvitationCardInfo["cname"]."》已经结束";
            }
            if($InvitationCardInfo['master_userid'] == $ThirdLoginInfo['user_id']){
                return $str = "您为该课程【".$InvitationCardInfo["cname"]."】的主讲人。\n"."<a href='".$url."'>点击此处开始讲课</a>";
            }
            //判断该二维码是否是否失效
            if($InvitationCardInfo['state'] == 1){
                if($CourseInfo['pid'] == 0 && $CourseInfo['type'] == 1){
                    $str = "课程：【".$InvitationCardInfo["cname"]."】\n开播时间：".substr($InvitationCardInfo["begin_time"],0,16)."\n<a href='".$url."'>点此去课堂</a>";
                }
                if($CourseInfo['pid'] != 0 && $CourseInfo['type'] == 1){
                    $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n课程安排：预计更新".$CourseInfo["plan_num"]."节\n<a href='".$url."'>点此去课堂</a>";
                } 
                if($CourseInfo['type'] == 2){
                    $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n课程安排：预计更新".$CourseInfo["c_plan_num"]."节\n<a href='".$url."'>点此去课堂</a>";
                } 
            }else{
                if($ThirdLoginInfo['user_type'] == 2){
                    if($CourseInfo['pid'] == 0 && $CourseInfo['type'] == 1){
                    $str = "课程：【".$InvitationCardInfo["cname"]."】\n开播时间：".substr($InvitationCardInfo["begin_time"],0,16)."<a href='".$url."'>点此去课堂</a>\n注：马甲账号不能成为嘉宾";
                    }
                    if($CourseInfo['pid'] != 0 && $CourseInfo['type'] == 1){
                        $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n开播时间：".substr($InvitationCardInfo["begin_time"],0,16)."<a href='".$url."'>点此去课堂</a>\n注：马甲账号不能成为嘉宾";
                    } 
                    if($CourseInfo['type'] == 2){
                        $str = "系列课： 【".$InvitationCardInfo["cname"]."】\n开播时间：".substr($InvitationCardInfo["begin_time"],0,16)."<a href='".$url."'>点此去课堂</a>\n注：马甲账号不能成为嘉宾";
                    } 
                }else{
                    $InvitationcardModelstatus = $InvitationcardModel->where('sn',$EventKey)->update([
                    'state' => '1'
                    ]);
                    $InvitationcardUserModelData['type'] = 2;
                    $InvitationcardUserModelStatus = $InvitationcardUserModel->insert($InvitationcardUserModelData);
                    $str = "恭喜您已成为课程【".$InvitationCardInfo["cname"]."】的嘉宾，\n您可以和主讲人一同讲课。\n"."<a href='".$url."'>点击此处开始讲课</a>";
                }
            }
            return $str;
        }
        if($InvitationCardInfo['type'] == 4){
            if($InvitationcardUserIsExistStatus == 0){
                $InvitationcardUserModelData['type'] = 3;
                $InvitationcardUserModelStatus = $InvitationcardUserModel->insert($InvitationcardUserModelData);
            }
        }
        if($InvitationCardInfo['type'] == 5){
            if($InvitationcardUserIsExistStatus == 0){
                $InvitationcardUserModelData['type'] = 5;
                $InvitationcardUserModelStatus = $InvitationcardUserModel->insert($InvitationcardUserModelData);
            }
        }
        if($InvitationCardInfo['type'] == 8){
            $url = config('WX_DOMAIN')."/#/column/detail/".$InvitationCardInfo['class_id'];
            return $str = "栏目详情：【".$InvitationCardInfo["cname"]."】\n<a href='".$url."'>点此去栏目详情</a>";
        }
        
    }
    /**
     * 发送单张图片
     * @return [type] [description]
     */
    public function pusImageC($open_id){
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token.'';
        $str = '{
          "touser":"'.$open_id.'",
          "msgtype":"image",
          "image":
          {
            "media_id":"'.config('kefucode').'"
          }
        }';
        $this->https_post($url, $str);
    }
    /**
     * 接口失败返回
     * @param int $code 返回信息码
     */
    protected function errorJson($code)
    {
        $json = array();
        $json['data'] = null;
        $json['code'] = $code;
        $json['msg'] = JsonResult::$jsonMsg[$code];
        return json($json, 200);
    }
}