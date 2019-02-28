<?php

/* *
 * 配置文件
 * 版本：1.2
 * 日期：2014-06-13
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201408071000001543
$llpay_config['oid_partner'] = '201712290001333864';

//秘钥格式注意不能修改（左对齐，右边有回车符）
$llpay_config['RSA_PRIVATE_KEY'] ='-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDNj/EQjSRW1x4ruVbKpv6iPpIPVKSH5D/ZLcvWXgzlMYNGp6+D
/2v3bHcjRCAw+/vOssuppeYJMEdy+GKWzxuWKUBzBOPah/UIGkwi0tlSuysU7SQf
puZ04CuUQjojPliwZ95UcHoG3nRo3sNYdaXWzWIha+n2EXrxfM7sWjqAewIDAQAB
AoGAF6cpcOMcvFVSZmuUHgtrH1Ydzl/J8s0Dv8SyQL9fsnupBFdFLeYVEUpMxyUO
ozRLfDQ8lQ++0W3ZutPz3DCGluLB4k35zgjhx+LpjY93LbcljRtFAWN/5ewptej4
tVEKkZMY+paaWhSVDi4hcBeJFypLVKL8VwHBwvx8v7j8i5ECQQD4G1ttSyYJNMW2
qUK2tsDNkjGGEyT518QK5/KR/shfNxW7YVunm77LsSbR4oJ8XFdjcOX16VKfgsMt
HWfMFWWDAkEA1BoXSplq6DLxVRd4TAtPRxMOwo09VQ4ZbIw+TNiF9bXd+DTyAmC4
NHfePNNRK8uPzCuXPrPQoFmV8gGx/H//qQJAAvPwZqCaV0m1gLMLBDmwmcG/rSTV
L9QNlUOlc29g2yFAtPY3rQsBflMhbyYO/4Pp1lklo4OfZB6eTA8piRhIGQJBALb5
fyha65A/ClSm959ajly5Qx1xLPzoOeSbo881Z3NOHpxWSITmnWKeGfmNL1RBut6e
qE5uX0dFoYZyEfLLFWkCQAiVtD8g4Rve709fHLgYGUiQKsZnTIL3Xq8uXZZMILly
jNXIgKiei3yVc40HNPmFcq5cGnId6wuRZ1WMYNPJ5rM=
-----END RSA PRIVATE KEY-----
';	


//安全检验码，以数字和字母组成的字符
$llpay_config['key'] = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCSS/DiwdCf/aZsxxcacDnooGph3d2JOj5GXWi+q3gznZauZjkNP8SKl3J2liP0O6rU/Y/29+IUe+GTMhMOFJuZm1htAtKiu5ekW0GlBMWxf4FPkYlQkPE0FtaoMP3gYfh+OwI+fIRrpW3ySn3mScnc6Z700nU/VYrRkfcSCbSnRwIDAQAB';

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

//版本号
$llpay_config['version'] = '1.2';

//请求应用标识 为wap版本，不需修改
$llpay_config['app_request'] = '3';


//签名方式 不需修改
$llpay_config['sign_type'] = strtoupper('RSA');

//订单有效时间  分钟为单位，默认为10080分钟（7天） 
$llpay_config['valid_order'] ="10080";

//字符编码格式 目前支持 gbk 或 utf-8
$llpay_config['input_charset'] = strtolower('utf-8');

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$llpay_config['transport'] = 'http';
?>