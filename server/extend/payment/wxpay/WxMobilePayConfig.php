<?php

class WxMobilePayConfig
{



    const APPID = "wx2b76dd0059adc7a4";
    const MCHID = "1480322232";
    const KEY = "ffer8h5w5d4t6s598t5w5cd5ftgw4f5r";

    const APPSECRET = "e3a5aa57b9c621a7a38522f57a3818cf";

    // =======【商户系统后台机器IP】=====================================
    /*
     * 此参数可手动配置也可在程序中自动获取
     */
    const IP = "8.8.8.8";
    // =======【支付结果通知url】=====================================
    /*
     * 支付结果通知回调url，用于商户接收支付结果
     */
//    const NOTIFY_URL = "http://dev.h5.dacelue.com.cn/PayNotify/WxNotify";
    public $NOTIFY_URL;

    public function __construct()
    {
        $this->NOTIFY_URL = \think\Config::get('H5_DOMAIN') . '/PayNotify/WxNotify';
    }
}

?>