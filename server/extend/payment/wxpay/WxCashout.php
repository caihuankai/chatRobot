<?php
namespace payment\wxpay;
require_once 'WxPay.Data.php';
require_once 'WxPay.NativePay.php';
require_once 'WxPay.Api.php';
require_once 'WxPay.Config.php';
require_once 'WxPay.Exception.php';

class WxCashout
{
    public function HandleOrder($wxCashoutData)
    {
        $input = new WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new WxPayConfig ();

        $data['mch_appid'] = $mConfig::APPID;
        $data['mchid'] = $mConfig::MCHID;
        $data['partner_trade_no'] = $wxCashoutData['partner_trade_no'];
        $data['openid'] = $wxCashoutData['openid'];
        $data['check_name'] = $wxCashoutData['check_name'];
        $data['amount'] = $wxCashoutData['amount'];
        $data['desc'] = $wxCashoutData['desc'];
        $data['spbill_create_ip'] = $wxCashoutData['spbill_create_ip'];

        $order = WxPayApi::cashoutOrder($data);
        return $order;
    }


    public function PayResults($xml)
    {
        try {
            $result = WxPayResults::Init($xml);
            return $result;
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
//            \Think\Log::record('验签时出错 => ' . $msg);
            \think\log::write('WxPay:验签时出错 => ' . $msg, 'pay');
            return false;
        }
    }
}

