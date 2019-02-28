<?php
namespace pay\wxPay;
require_once 'WxPay.Data.php';
require_once 'WxPay.NativePay.php';

class WxPay
{
    public function MobileHandleOrder($wxPayData, $notify_url=false)
    {
        $input = new \WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new \WxMobilePayConfig ();

        $input->SetBody($wxPayData ['body']);
        $input->SetAttach('test');
        $input->SetOut_trade_no($wxPayData ['out_trade_no']);
        $input->SetTotal_fee($wxPayData ['total_fee']);
        $input->SetTime_start($wxPayData ['time_start']);
        $input->SetTime_expire($wxPayData ['time_expire']);
//        $input->SetGoods_tag("test");
        $input->SetNotify_url($notify_url!==false ? $notify_url : $mConfig::NOTIFY_URL);
        $input->SetTrade_type("APP");
//        $input->SetProduct_id('0');

        $input->SetAppid($mConfig::APPID);
        $input->SetMch_id($mConfig::MCHID);
        $input->SetSpbill_create_ip($mConfig::IP);
        $result = $notify->GetPayUrl($input);
        if ($result ["return_code"] == "SUCCESS") {
            $inp = new \WxPayResults ();
            $inp->SetData("appid", $mConfig::APPID);

            $inp->SetData("noncestr", \WxPayApi::getNonceStr());
            $inp->SetData("package", "Sign=WXPay");
            $inp->SetData("partnerid", $mConfig::MCHID);
            $inp->SetData("prepayid", $result ["prepay_id"]);
            $inp->SetData("timestamp", time());

            $string = $inp->ToUrlParams();
            // 签名步骤二：在string后加入KEY
            $string = $string . "&key=" . $mConfig::KEY;
            // 签名步骤三：MD5加密
            $string = md5($string);
            // 签名步骤四：所有字符转为大写
            $sign = strtoupper($string);
            $inp->SetData("sign", $sign);
            return $inp->GetValues();
        }

        return null;
    }


    public function PayResults($xml)
    {
        try {
            $result = \WxPayResults::Init($xml);
            return $result;
        } catch (\WxPayException $e) {
            $msg = $e->errorMessage();
//            \Think\Log::record('验签时出错 => ' . $msg);
            \think\log::write('WxPay:验签时出错 => ' . $msg, 'pay');
            return false;
        }
    }
}