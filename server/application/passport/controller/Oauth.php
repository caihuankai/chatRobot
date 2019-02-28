<?php
namespace app\passport\controller;


use app\common\model\ThirdLogin;
use think\Exception;
use think\Log;
use thirdLogin\qq\qq;
use thirdLogin\sina\sina;
use thirdLogin\wx\wx;

class Oauth extends App
{

    public function test()
    {
        var_dump(123);
        $result = ['asdasd', 'asdasd'];
        Log::write(json_encode($result), 'oauth');
        die;
        $sina = new sina();
//        var_dump($sina->client_id);die;
        $url = $sina->login_url('http://www.mofang.social/Oauth/sinaLogin');
        return $this->redirect($url);
    }

    public function sinaLogin()
    {
        $code = request()->param('code');
        if (empty($code)) {
            return $this->erret(\C::ERR_PARAMETER);
        }
        $sina = new sina();
        $sina->access_token = $code;
        $re = $sina->api('https://api.weibo.com/oauth2/get_token_info', ['access_token' => $sina->access_token], 'POST');
        if (empty($re)) {
            return $this->erret(\C::OAUTH_SINA_ERR);
        }
        $show = $sina->api('https://api.weibo.com/2/users/show.json', ['uid' => $re['uid']], 'GET');

        $gender = $show['gender'] == 'm' ? 1 : 2;
        return $this->login(2, $re['uid'],'', $gender, $show['screen_name'], $show['avatar_large']);
    }

    public function qqLogin()
    {
        $code = request()->param('code');
//        $code = '3C21B03FFAFDF674D83CCB0182068185';
        if (empty($code)) {
            return $this->erret(\C::ERR_PARAMETER);
        }
        $qq = new qq();
        $qq->access_token = $code;
//        $qq->access_token('http://www.mofang.social', $code);
//        var_dump($qq->access_token);
        if (empty($qq->access_token)) {
            return $this->erret(\C::OAUTH_QQ_ERR);
        }
        $result = $qq->get_openid();
        if (empty($result)) {
            return $this->erret(\C::OAUTH_QQ_ERR);
        }
        $open_id = $result["openid"];
        $result = $qq->get_user_info($open_id);
        $alias = $result['nickname'];
        $gender = $result['gender'] == '男' ? 1 : 2;
        $headaddr = $result['figureurl_qq_1'];
        return $this->login(1, $open_id,'', $gender, $alias, $headaddr);
    }

    public function wxLogin()
    {
        $openid = request()->param('openid');
        $code = request()->param('code');
        if (empty($code)) {
            return $this->erret(\C::ERR_PARAMETER);
        }
        $wx = new wx();
        $wx->access_token = $code;
        $wx->openid = $openid;

        $result = $wx->getUserInfo();
        if (empty($result)) {
            return $this->erret(\C::OAUTH_WX_ERR);
        }
        $open_id = $wx->openid;
        $alias = $result['nickname'];
        $gender = $result['sex'];
        $union_id = isset($result['unionid']) ? $result['unionid'] : '';
        $headaddr = $result['headimgurl'];
        return $this->login(3, $open_id,$union_id, $gender, $alias, $headaddr);
    }

    private function login($type, $open_id, $union_id, $gender, $alias, $headaddr)
    {
        $tl = new ThirdLogin();
        if ($type == 3) {
            if (empty($union_id)) {
                $tlModel = $tl->db()->where(array('type' => $type, 'open_id' => $open_id))->find();
            } else {
                $sql = "SELECT * FROM mc_third_login WHERE `type` = $type AND (union_id = '$union_id' OR open_id = '$open_id') LIMIT 1";
                $queryResult = $tl->db()->query($sql);
                $tlModel = empty($queryResult) ? $queryResult : $queryResult[0];
            }
        } else {
            $tlModel = $tl->db()->where(array('type' => $type, 'open_id' => $open_id))->find();
        }
        if (empty($tlModel)) {
            $tl->db()->startTrans();
            try {
                $info = $this->Member->register();
                if (!$info) {
                    return $this->erret(\C::ERR_FAIL);
                }
                $rs = $tl->db()->insertGetId(['member_id' => $info['id'], 'type' => $type, 'open_id' => $open_id,'union_id' => $union_id, 'create_time' => time(), 'alias' => $alias]);
                if (!$rs) {
                    return $this->erret(\C::ERR_FAIL);
                }
                $tl->db()->commit();
            } catch (Exception $e) {
                Log::write('oauth => ' . $e->getMessage(), 'oauth');
                $tl->db()->rollback();
                return $this->erret(\C::ERR_FAIL);
            }
            $rs = $this->Member->get($info);
            return $this->ret(['member_id' => $info['id'], 'tcp_code' => $rs['code'], 'is_init' => 0, 'gender' => $gender, 'alias' => $alias, 'headaddr' => $headaddr, 'TCP_DB_HOSTNAME' => config('TCP_DB_HOST')]);
        } else {
            if ($type == 3 && empty($tlModel['union_id']) && !empty($union_id)) {
                $tl->db()->where('id', '=', $tlModel['id'])->update(['union_id' => $union_id]);
            }

            $this->Member->updateCode($tlModel['member_id']);
            $rs = $this->Member->get($tlModel['member_id']);
            if ($rs['is_multi'] == 0 && $rs['role_id'] == 0) {
                $is_init = 0;
            } else {
                $is_init = 1;
            }
            return $this->ret(['member_id' => $rs['id'], 'tcp_code' => $rs['code'], 'is_init' => $is_init, 'gender' => $gender, 'alias' => $alias, 'headaddr' => $headaddr, 'TCP_DB_HOSTNAME' => config('TCP_DB_HOST')]);
        }
    }
}
