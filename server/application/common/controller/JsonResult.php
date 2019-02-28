<?php
namespace app\common\controller;
/**
 * 接口返回状态类
 * Class JsonResult
 * @package app\common\controller
 * @author Larry <sad812@163.com>
 */
class JsonResult
{
    /* 公共区间 -5001 到 -10000 */
    /** 参数错误*/
    const ERR_PARAMETER = -5001; //参数错误
    const ERR_NOT_LOGIN = -5002; // 未登录
    /** 无权限*/
    const ERR_NO_FORBIDDEN = -5004;
    /**请稍后提交*/
    const ERR_SUBMIT_FAST = -5008;
    /**  %s不存在 */
    const ERR_MSG_NOT_EXISTS = -5009;
    /** 不能小于当前时间 */
    const ERR_NOT_MIN_NOW = -5010;
    /** 保存失败 */
    const ERR_SAVE_ERROR = -5011;
    const ERR_USER_DIS = -5012;
    /** 无权限 */
    const ERR_NOT_PERMISSIONS = -5013;
    const ERR_PHONE = -5014;
    
    /** 自定义错误消息 */
    const ERR_LOCAL_MESSAGE = -5100;
    
    
    # 其他人
    const ERR_TEL_IS_REGISTER = -11001;  //该手机号已注册
    
    #caihuankai -16001 到 -18000
    const ERR_SAVE_CASHOUT_DATA = -16001;
    const ERR_WECHAT_CASHOUT = -16002;
    const ERR_SAVE_CASHOUT_RESULT = -16003;
    const ERR_USERINFO_NULL = -16004;
    const ERR_LIVE_NULL = -16005;
    const ERR_CLASS_NULL = -16006;
    const ERR_QRCODE_SAVE = -16007;
    const ERR_PASSWORD = -16008;
    const ERR_INSUFFICIENT_FUNDS = -16009;
    const ERR_CLASS_EDIT =-16010;
    const ERR_CLASS_DELETE = -16011;
    const ERR_CASHOUTNO_NULL = -16012;
    const ERR_CASHOUTNO_AMOUNT = -16013;
    const ERR_CASHOUTNO_USER = -16014;
    const ERR_RPC_ERROR = -16015;
    const ERR_CASHOUTNO_SYSTEMERROR = -16016;
    const ERR_CASHOUTNO_NOTENOUGH = -16017;
    const ERR_POWER_SEND_CLASS = -16018;
    const ERR_REPEAT_RECOMMEND = -16019;
    const ERR_ERROR_RECOMMEND = -16020;
    const ERR_FAIL_RECOMMEND = -16021;
    const ERR_ORDERNO_NOT_EXISTS = -16022;
    const ERR_ORDERNO_REPEAT = -16023;
    const ERR_ORDERNO = -16024;
    const ERR_ORDERNO_AMOUNT = -16025;
    const ERR_PAY = -16026;
    const ERR_PAY_REPEAT = -16027;
    const ERR_IMG_NOT_EXISTS = -16028;
    const ERR_IMG_DELECT = -16029;
    const ERR_IMG_UPDATE_SORT = -16030;
    const ERR_IMG_UPLOAD = -16031;
    const ERR_IMG_UPLOAD_REPEAT = -16032;
    const ERR_BANK_LIST = -16033;
    const ERR_UNBUILD = -16034;
    
    # aozhuochao -17001 到 -18000
    const ERR_CATE_NAME_NOT_EMPTY = -17001;
    const ERR_COURSE_NOT_EXIST = -17002;
    const ERR_COURSE_DISABLE = -17003;
    const ERR_COURSE_DELETE = -17007; // 课程已删除
    const ERR_COURSE_RECOMMEND = -17004;
    const ERR_USER_NOT_EXISTS = -17005;
    const ERR_COURSE_ERROR = -17006;
    const ERR_LIVE_DIS = -17007;
    const ERR_NOT_TEACHER = -17008; // 不是老师
    const ERR_ASSISTANT_NO_PERMISSIONS = -17009; // 助教无权限管理老师
    const ERR_PHONE_VERIFY_CODE_OFTEN = -17010; // 您的操作过于频繁，明天再来吧
    
    # liujuneng -18001 到19000
    const ERR_VIEWPOINT_NOT_EXIST = -18001;
    const ERR_VIEWPOINT_DELETE = -18002;
    const ERR_VIEWPOINT_RECOMMEND = -18003;
    const ERR_ASSISTANT_NO_PERMISSIONS_VIEWPOINT = -18004; // 助教无权限管理老师
    const ERR_HAS_KEYWORK = -18005;//包含敏感词

    public static $jsonMsg = array(
        /* 公共区间 -5001 到 -10000 */
        self::ERR_PARAMETER => '参数错误',
        self::ERR_NOT_LOGIN => '未登录',
        self::ERR_NO_FORBIDDEN => '无权限',
        self::ERR_SUBMIT_FAST => '请稍后%s提交',
        self::ERR_MSG_NOT_EXISTS => '%s不存在',
        self::ERR_NOT_MIN_NOW => '不能小于当前时间',
        self::ERR_SAVE_ERROR => '保存失败',
        self::ERR_USER_DIS => '用户被禁用',
        self::ERR_NOT_PERMISSIONS => '无权限',
        self::ERR_PHONE => '手机号格式不正确',
    
        # 其他人
        self::ERR_TEL_IS_REGISTER => '该手机号已注册',

        #caihuankai
        self::ERR_SAVE_CASHOUT_DATA => '付款信息保存失败，请联系客服人员',
        self::ERR_SAVE_CASHOUT_RESULT => '付款返回信息保存失败，请联系客服人员',
        self::ERR_WECHAT_CASHOUT => '提现失败',
        self::ERR_USERINFO_NULL => '用户不存在',
        self::ERR_LIVE_NULL => '直播间不存在',
        self::ERR_CLASS_NULL => '课程不存在',
        self::ERR_QRCODE_SAVE => '二维码数据保存失败',
        self::ERR_PASSWORD => '密码错误',
        self::ERR_INSUFFICIENT_FUNDS => '账户余额不足',
        self::ERR_CLASS_EDIT => '课程编辑失败',
        self::ERR_CLASS_DELETE => '课程删除失败',
        self::ERR_CASHOUTNO_NULL => '订单不存在',
        self::ERR_CASHOUTNO_AMOUNT => '订单金额错误',
        self::ERR_CASHOUTNO_USER => '订单用户错误',
        self::ERR_RPC_ERROR => '调用RPC失败',
        self::ERR_CASHOUTNO_SYSTEMERROR => '微信服务器繁忙，请使用原单号以及原请求参数重试，否则可能造成重复支付等资金风险 或 联系开发人员',
        self::ERR_CASHOUTNO_NOTENOUGH => '汇款账户余额不足',
        self::ERR_POWER_SEND_CLASS  => '没有权限发送课程',
        self::ERR_REPEAT_RECOMMEND => '重复推荐',
        self::ERR_ERROR_RECOMMEND => '推荐数据不存在',
        self::ERR_FAIL_RECOMMEND => '推荐失败',
        self::ERR_ORDERNO_NOT_EXISTS => '订单不存在',
        self::ERR_ORDERNO_REPEAT => '订单重复通知',
        self::ERR_ORDERNO => '订单错误',
        self::ERR_ORDERNO_AMOUNT => '支付金额错误',
        self::ERR_PAY => '购买失败',
        self::ERR_PAY_REPEAT => '重复购买',
        self::ERR_IMG_NOT_EXISTS => '图片不存在',
        self::ERR_IMG_DELECT => '图片删除失败',
        self::ERR_IMG_UPDATE_SORT => '修改图片序号失败',
        self::ERR_IMG_UPLOAD => '图片上传失败',
        self::ERR_IMG_UPLOAD_REPEAT => '上传图片重复',
        self::ERR_BANK_LIST => '没有绑定的银行卡',
        self::ERR_UNBUILD => '解绑失败',
        
        # aozhuochao
        self::ERR_CATE_NAME_NOT_EMPTY => '分类名称不能为空',
        self::ERR_COURSE_NOT_EXIST => '课程不存在',
        self::ERR_COURSE_DISABLE => '课程已屏蔽',
        self::ERR_COURSE_DELETE => '课程已删除',
        self::ERR_COURSE_RECOMMEND => '课程已推荐过此类型',
        self::ERR_USER_NOT_EXISTS => '不存在的用户',
        self::ERR_COURSE_ERROR => '课程分类错误',
        self::ERR_LIVE_DIS => '直播间已禁用',
    	self::ERR_NOT_TEACHER => '不是一个老师',
    	self::ERR_ASSISTANT_NO_PERMISSIONS => '该老师暂无课程权限，请修改指定老师后再创建课程',
    	self::ERR_PHONE_VERIFY_CODE_OFTEN => '您的操作过于频繁，明天再来吧',
    		
    	# liujuneng
    	self::ERR_VIEWPOINT_NOT_EXIST => '观点不存在',
    	self::ERR_VIEWPOINT_DELETE => '观点已删除',
    	self::ERR_VIEWPOINT_RECOMMEND => '观点已推荐过此类型',
    	self::ERR_ASSISTANT_NO_PERMISSIONS_VIEWPOINT => '该老师暂无观点权限，请修改指定老师后再创建观点',
    	self::ERR_HAS_KEYWORK => '%s含有敏感词，请修改',
    
    );
    
    
    
    
    /**
     * 用于给msg传参
     *
     * @var array
     */
    protected static $msgArgs = [];
    
    
    /**
     * 获取可传参的错误信息
     *
     * @param int|string $code
     * @return mixed|string
     */
    public static function getMsg($code)
    {
        if (empty(self::$jsonMsg[$code])) {
            return $code;
        }
        
        if (empty(self::$msgArgs[$code])) {
            return self::$jsonMsg[$code];
        }
        
        return vsprintf(self::$jsonMsg[$code], self::$msgArgs[$code]);
    }
    
    
    /**
     * 设置传参信息
     *
     * @param $code
     * @param array|string $arr
     */
    public static function setMsgArgs($code, $arr)
    {
        self::$msgArgs[$code] = (array)$arr;
    }
}