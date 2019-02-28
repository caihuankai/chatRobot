 <?php
  	require_once("alipay.config.php");
	require_once("lib/alipay_notify.class.php");
	//计算得出通知验证结果
	$Params = '{"act":"payment.finish","agent_id":"24","zone":"4","discount":"0.00","payment_type":"1","subject":"\u8d2d\u4e7060\u6c34\u6676","trade_no":"2014073128377325","buyer_email":"406135981@qq.com","gmt_create":"2014-07-31 16:50:17","notify_type":"trade_status_sync","quantity":"1","out_trade_no":"201407311122105663","seller_id":"2088411124265853","notify_time":"2014-07-31 16:50:18","body":"\u8d2d\u4e7060\u6c34\u6676","trade_status":"TRADE_FINISHED","is_total_fee_adjust":"N","total_fee":"0.10","gmt_payment":"2014-07-31 16:50:18","seller_email":"91ku@91ku.com","gmt_close":"2014-07-31 16:50:18","price":"0.10","buyer_id":"2088602054432255","notify_id":"b2b3b767af36f3af26e74eb23a4bd7d83e","use_coupon":"N","sign_type":"RSA","sign":"OfK7j\/972ybcOIL1eLECB1oj+7YYvQVoKc0zM4iCPXgRItcRCKURib2x8z8ku+IyR0qIhqkZp2BbcUPgzwhZUqKg7\/\/drG3xJfIagan2PvsQquelnyA2\/kMYTsJC\/OWgy8ftPmW6xXWJavwIHgvdjKZS4+eBZr2UiaHKHVkCx4A="}';
	// var_dump(json_decode($Params));
	// die;
	$Params = json_decode($Params,true);
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyNotify($Params);
	return $verify_result;
?>