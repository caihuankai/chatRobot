<?php
// namespace app\common\controller;
/**
 * 接口返回状态类
 * Class JsonResult
 * @package app\common\controller
 * @author Larry <sad812@163.com>
 */
class C
{
    /*+---------------------------------------------
     *| 公共区间  10001 到 10999
     *+--------------------------------------------*/
    
    const SUCCESS = 0;
    
    /**
     * 参数错误
     */
    const ERR_PARAMETER = 10001;
    /**
     * db参数出错
     */
    const ERR_DB = 10002;
    /**
     * 没登录
     */
    const ERR_NO_LOGIN = 10003;
    /**
     * 无权限
     */
    const ERR_NO_FORBIDDEN = 10004;
    /**
     * 未成功 或 系统错误
     */
    const ERR_FAIL = 10005;
    /**
     * 已删除
     */
    const ERR_DELETED = 10006;
    /**
     * 获取数据失败
     */
    const ERR_NO_FIND_DATA = 10007;
    /**
     * 请稍后提交
     */
    const ERR_SUBMIT_FAST = 10008;
    /**
     * 登录token已失效
     */
    const ERR_TOKEN_EXPIRED = 10009;
    /**
     * 未找到该用户
     */
    const ERR_NO_FIND_USER = 10010;

    //自定义错误
    const ERR_CUSTOM = 10011;

    /**
     * yar通讯失败
     */
    const ERR_YAR_FAIL = 10012;


    /*+---------------------------------------------
     *| 注册登录 12000 到 12999
     *+--------------------------------------------*/
    /**
     * 电话错误
     */
    const TEL_ERROR = 12001;
    /**
     * 号码已超过每天上限
     */
    const SMS_TEL_IS_BEYOND = 12002;
    /**
     * Ip已超过每天上线
     */
    const SMS_IP_IS_BEYOND = 12003;
    /**
     * 验证码错误
     */
    const VERIFY_ERROR = 12004;
    /**
     * 号码已注册
     */
    const TEL_REGISTERED = 12005;
    /**
     * 昵称长度过长
     */
    const ALIAS_LENTH_ERR = 12006;
    /**
     * 用户已处理过初始化
     */
    const USER_INITED = 12007;
    /**
     * 未注册
     */
    const USER_UNREGISTER = 12008;
    /**
     * 短信发送失败
     */
    const SMS_SEND_FAILD = 12009;
    /**
     * 密码格式错误
     */
    const PASSWORD_LENGTH_FAILD = 12010;
    /**
     * 密码错误
     */
    const PASSWORD_FAILD = 12011;
    /**
     * 改账号尚未设置密码, 请用验证码登录
     */
    const PASSWORD_NO_SETED = 12012;

    /*+---------------------------------------------
     *| 个人中心 13000 到 13999
     *+--------------------------------------------*/
    //提现金额范围为5~100000000
    const MC_MEMBER_WITHDRAW_PRICE_NUM = 13001;

    //糖果数量不足
    const MC_MEMBER_SWEET_NOT_ENOUGH = 13002;

    //用户不存在
    const MC_MEMBER_NOT_EXIST = 13003;
    
    /**
     * 相册数量已满, 无法上传
     */
    const PHOTO_TOTAL_IS_FULL = 13004;
    
    /**
     * 提现失败
     */
    const WITHDRAW_FAIL = 13101; 
    /**
     * 提现失败
     */
    const WITHDRAW_INSUFFICIENT_FUNDS = 13102;
    /**
     * 返还提现失败
     */
    const WITHDRAW_RETURN_FAIL = 13103;    
    /**
     * 修改提现信息状态失败
     */
    const WITHDRAW_EDIT_INFO_FAIL = 13104;    
    
    
    

    /*+---------------------------------------------
     *| 群 14000 到 4999
     *+--------------------------------------------*/
    //群不存在
    const MC_GROUP_NOT_EXIST = 14000;

    //用户不是该群成员
    const MC_GROUP_USER_NOT_IN = 14001;

    //插件不存在
    const MC_GROUP_PLUGIN_NOT_EXIST = 14002;

    //不可修改
    const MC_GROUP_NOT_ALLOW_CHANGE = 14003;

    //用户不在黑名单中
    const MC_GROUP_NOT_IN_BLACKLIST = 14004;

    //查询不到群插件设置
    const MC_GROUP_NOT_FIND_PLUGIN_SETTING = 14005;

    //搜索内容长度不正确
    const MC_GROUP_SEARCH_DATA_ERROR_LENGTH = 14006;
    
    //不能重复提交视频审核
    const MC_GROUP_VIDEO_AUTH_REPEAT = 14007;    
    
    //你已成功通过视频审核, 不需要再提交申请
    const MC_GROUP_VIDEO_AUTH_HAD_PASS = 14008;
    
    //你不是该群成员, 无法提交审核
    const MC_GROUP_VIDEO_ID_ERR = 14009;
    
    //该申请已处理
    const MC_GROUP_VIDEO_HAD_AUDITED = 14010;
    /**
     * 群名称或群介绍长度过长
     */
    const MC_GROUP_NAME_LEN_ERR = 14011;
    /**
     * 建群数超过规定上限
     */
    const MC_GROUP_NUM_EXCEED = 14012;
    
    //该申请已失效
    const MC_GROUP_VIDEO_HAD_DEL = 14013;

    //该用户已经加入群
    const MC_GROUP_USER_EXISTS = 14014;
    
    
    /*+---------------------------------------------
     *| 第三方登录 15000 到 15999
     *+--------------------------------------------*/
    //新浪认证错误
    const OAUTH_SINA_ERR = 15000;

    //QQ认证错误
    const OAUTH_QQ_ERR = 15001;

    //微信认证错误
    const OAUTH_WX_ERR = 15002;
    
    /*+---------------------------------------------
     *| 充值  16000 到 16999
     *+--------------------------------------------*/
    /**
     * 订单已成功提交, 不用重复提交
     */
    const PAYMENT_POST_REPEAT = 16001;
    

    public static $msg = array(
        /* 公共区间 -10001 到 -10999 */
        self::ERR_PARAMETER => '参数错误',
        self::ERR_DB => 'db参数出错',
        self::ERR_NO_LOGIN => '用户未登录',
        self::ERR_NO_FORBIDDEN => '无权限',
        self::ERR_FAIL => '系统错误', //  未成功 或 系统错误
        self::ERR_DELETED => '已删除', //  已删除
        self::ERR_NO_FIND_DATA => '获取数据失败', //  已删除
        self::ERR_NO_FIND_USER => '用户未找到',
        self::ERR_SUBMIT_FAST => '请稍后提交',

        self::TEL_ERROR => '电话错误',
        self::SMS_TEL_IS_BEYOND => '号码已超过每天上限',
        self::SMS_IP_IS_BEYOND => 'Ip已超过每天上线',

        /* 个人中心 13000~13999*/
        self::MC_MEMBER_WITHDRAW_PRICE_NUM => '提现金额范围为5~100000000',
        self::MC_MEMBER_NOT_EXIST => '用户不存在',

        /* 群 14000 到 14999 */
        self::MC_GROUP_NOT_EXIST => '群不存在',
        self::MC_GROUP_USER_NOT_IN => '用户不是该群成员',
        self::MC_GROUP_PLUGIN_NOT_EXIST => '插件不存在',
        self::MC_GROUP_NOT_ALLOW_CHANGE => '不允许修改',
        self::MC_GROUP_NOT_IN_BLACKLIST => '用户不在黑名单中',
        self::MC_GROUP_NOT_FIND_PLUGIN_SETTING => '查询不到群插件设置',
        self::MC_GROUP_SEARCH_DATA_ERROR_LENGTH => '长度不正确',
        self::MC_GROUP_VIDEO_AUTH_REPEAT=>'不能重复提交视频审核',
        self::MC_GROUP_VIDEO_AUTH_HAD_PASS=>'你已成功通过视频审核, 不需要再提交申请',
        self::MC_GROUP_VIDEO_ID_ERR=>'你不是该群成员, 无法提交审核',
        self::MC_GROUP_VIDEO_HAD_AUDITED=>'该申请已处理',
        self::MC_GROUP_NUM_EXCEED=>'您已建群',
        self::MC_GROUP_USER_EXISTS => '当前用户已经存在群中！',

        /* 第三方登录 15000 到 15999 */
        self::OAUTH_SINA_ERR => '新浪认证错误',
        self::OAUTH_QQ_ERR => 'QQ认证错误',
        self::OAUTH_WX_ERR => '微信认证错误',
        self::VERIFY_ERROR => '验证码错误',
        self::USER_INITED => '用户已处理过初始化'
    );

}