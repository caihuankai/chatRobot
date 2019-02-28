<?php
namespace payment\wxpay;
require_once 'WxPay.Data.php';
require_once 'WxPay.NativePay.php';
require_once 'WxPay.Api.php';
require_once 'WxPay.Config.php';
require_once 'WxPay.Exception.php';

class WxPay
{
    public function HandleOrder($wxPayData)
    {
        $input = new WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new WxPayConfig ();

        $input->SetBody($wxPayData ['body']);
        $input->SetAttach('test');
        $input->SetOut_trade_no($wxPayData ['out_trade_no']);
        $input->SetTotal_fee($wxPayData ['total_fee']);
        $input->SetTime_start($wxPayData ['time_start']);
        $input->SetTime_expire($wxPayData ['time_expire']);
       // $input->SetGoods_tag("test");
        $input->SetNotify_url($wxPayData ['notify_url']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($wxPayData ['openId']);
        // $input->SetProduct_id('0');

        $input->SetAppid($mConfig::APPID);
        $input->SetMch_id($mConfig::MCHID);
        // $input->SetSpbill_create_ip($mConfig::IP);

        $order = WxPayApi::unifiedOrder($input);

        // echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        // print_r($order);

        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order);

        return $jsApiParameters;die;

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();
        // $result = $notify->GetPayUrl($input);
        
        
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

    //NATIVE扫码支付
    public function HandleOrder_native($wxPayData)
    {
        $input = new WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new WxPayConfig ();

        $input->SetBody($wxPayData ['body']);
        $input->SetAttach('test');
        $input->SetOut_trade_no($wxPayData ['out_trade_no']);
        $input->SetTotal_fee($wxPayData ['total_fee']);
        $input->SetTime_start($wxPayData ['time_start']);
        $input->SetTime_expire($wxPayData ['time_expire']);
       // $input->SetGoods_tag("test");
        $input->SetNotify_url($wxPayData ['notify_url']);
        $input->SetTrade_type("NATIVE");
        $input->SetOpenid($wxPayData ['openId']);
        $input->SetProduct_id($wxPayData ['out_trade_no']);

        $input->SetAppid($mConfig::APPID);
        $input->SetMch_id($mConfig::MCHID);
        // $input->SetSpbill_create_ip($mConfig::IP);

        $order = WxPayApi::unifiedOrder($input);

        return $order;die;
    }

    //app支付
    public function HandleOrder_app($wxPayData){
        $input = new WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new WxPayConfig ();

        $input->SetBody($wxPayData ['body']);
        $input->SetAttach('test');
        $input->SetOut_trade_no($wxPayData ['out_trade_no']);
        $input->SetTotal_fee($wxPayData ['total_fee']);
        $input->SetTime_start($wxPayData ['time_start']);
        $input->SetTime_expire($wxPayData ['time_expire']);
       // $input->SetGoods_tag("test");
        $input->SetNotify_url($wxPayData ['notify_url']);
        $input->SetTrade_type("APP");
        // $input->SetOpenid($wxPayData ['openId']);
        $input->SetProduct_id($wxPayData ['out_trade_no']);
        $wx_config = config('pay.wxpay');
        $input->SetAppid($wx_config['appid']);
        $input->SetMch_id($wx_config['mch_id']);
        // $input->SetSpbill_create_ip($mConfig::IP);

        $order = WxPayApi::unifiedMobileOrder($input);
        //如果上一次请求成功，那么我们将返回的数据重新拼装，进行第二次签名
        $resignData = array(
            'appid'    =>    $order['appid'],
            'partnerid'    =>    $order['mch_id'],
            'prepayid'    =>    $order['prepay_id'],
            'noncestr'    =>    $order['nonce_str'],
            'timestamp'    =>    (string)time(),
            'package'    =>    'Sign=WXPay'
        );
        $sign = $this->getSign($resignData);
        $resignData['sign'] = $sign['sign'];
        // $resignData['string'] = $sign['string'];
        // $resignData['md5'] = $sign['md5'];
        $resignData['noncestr'] = $sign['noncestr'];
        // var_dump($sign);
        return $resignData;
    }
    /**
     * 小程序支付
     * @param [type] $wxPayData [description]
     */
    public function HandleOrder_mini($wxPayData)
    {
        $input = new WxPayUnifiedOrder ();
        $notify = new \NativePay ();
        require_once 'WxMobilePayConfig.php';
        $mConfig = new WxPayConfig ();

        $input->SetBody($wxPayData ['body']);
        $input->SetAttach('test');
        $input->SetOut_trade_no($wxPayData ['out_trade_no']);
        $input->SetTotal_fee($wxPayData ['total_fee']);
        $input->SetTime_start($wxPayData ['time_start']);
        $input->SetTime_expire($wxPayData ['time_expire']);
        $input->SetNotify_url($wxPayData ['notify_url']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($wxPayData ['openId']);

        $order = WxPayApi::unifiedOrder_mini($input);

        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order);

        return $jsApiParameters;
    }
    /**
     * 获取参数签名；
     * @param  Array  要传递的参数数组
     * @return String 通过计算得到的签名；
     */
    private function getSign($params) {
        $params['noncestr'] = md5($params['timestamp']);
        $params['noncestr'] = strtoupper($params['noncestr']);
        $sign['noncestr'] = $params['noncestr'];
        ksort($params);        
        $string = $this->ToUrlParams($params);
        // 签名步骤二：在string后加入KEY
        $wx_config = config('pay.wxpay');
        $string = $string . "&key=".$wx_config['key'];
        $sign['string'] = $string;
        $string = md5($string);       //将字符串进行MD5加密
        $sign['md5'] = $string;
        $sign['sign'] = strtoupper($string);      //将所有字符转换为大写
        return $sign;
    }
    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($params)
    {
        ksort($params);
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
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

