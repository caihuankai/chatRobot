<?php
namespace app\common\model;

use app\common\model\User;
use app\common\model\ThirdLogin;
use app\common\model\InvitationcardUser;


/**
 * 粉丝表（用于购买提成）
 * @author xiaok
 * @time 2017/06/13
 */
class Fans extends ModelBs{
    
    
    /**
     * 获取一个用户的粉丝数（邀请卡邀请到的唯一用户）
     *
     * @param array $userIds
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getFansNumByUserId(array $userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        
        return $this->where(['f.invita_userid' => ['in', $userIds]])->alias('f')
            ->field('count(f.id) num, invita_userid user_id')
            ->group('f.invita_userid')
            ->fetchClass('\CollectionBase')->select()->column('num', 'user_id');
    }

    /**
     * 绑定粉丝
     * @param  [type] $user_id 邀请人id
     * @param  [type] $fans_id 粉丝id
     * @return [type]          [description]
     */
    public function bindFans($user_id,$fans_id){
        //判断邀请人是否为讲师或流量主
        $UserModel = new User();
        $UserInfo = $UserModel->where('user_id',$user_id)->find();
        $ThirdLoginModel = new ThirdLogin();
        $fanInfo = $ThirdLoginModel
        ->alias('t')
        ->join('user u','u.user_id = t.user_id')
        ->where('t.user_id',$fans_id)
        ->find();
        if(empty($UserInfo) || empty($fanInfo)){
            return;
        }
        //马甲身份不走逻辑
        if($fanInfo['user_type'] == 2){
            return;
        }

        //邀请记录
        $InvitationcardUserModel = new InvitationcardUser();
        $InvitationcardUserData['create_user_id'] = $user_id;
        $InvitationcardUserData['get_user_id'] = $fanInfo['open_id'];
        $InvitationcardUserData['user_id'] = $fanInfo['user_id'];
        $InvitationcardUserData['type'] = 3;
        //助教不绑定粉丝
        if($UserInfo['is_assistant'] == 1){
            $InvitationcardUserData['create_user_id_type'] = 2;
            $InvitationcardUserModel->insert($InvitationcardUserData);
            return;
        }
        //讲师或流量主才可以绑定粉丝
        if($UserInfo['user_level'] == 2 || $UserInfo['user_level'] == 3){
            //判断粉丝是否已有绑定关系
            // if(empty($fanInfo)){
            //     return;
            // }else{
            //     $status = $this->where('open_id',$fanInfo['open_id'])->find();
            //     if(empty($status)){
            //         $FansData['open_id'] = $fanInfo['open_id'];
            //         $FansData['invita_userid'] = $user_id;
            //         $FansData['user_id'] = $fanInfo['user_id'];
            //         $saveFans = $this->insert($FansData);

            //         $UserModel->update(['invite_user_id' => $user_id], ['user_id' => $fans_id]);
            //     }
            // }
            $InvitationcardUserModel->insert($InvitationcardUserData);
        }
    }
    
}