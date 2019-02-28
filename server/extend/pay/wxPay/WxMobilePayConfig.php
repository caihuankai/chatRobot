<?php

class WxMobilePayConfig
{
    const APPID = "wxa2bd61d553c8518a";
    const MCHID = "1445344702";
    const KEY = "dotaatadw145trbtiadkt856tbtilaf1";
    
//    const APPSECRET = "7802edae4f39e1dd10f8d1a5a0c9a836";

    // =======【商户系统后台机器IP】=====================================
    /*
     * 此参数可手动配置也可在程序中自动获取
     */
    const IP = "8.8.8.8";
    // =======【支付结果通知url】=====================================
    /*
     * 支付结果通知回调url，用于商户接收支付结果
     */
    const NOTIFY_URL = "http://183.60.136.3:9981/pay/wxcallback";
}

?>