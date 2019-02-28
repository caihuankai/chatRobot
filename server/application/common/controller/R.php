<?php
// namespace app\common\controller;
/**
 * RPC接口返回状态类
 * Class JsonResult
 * @package app\common\controller
 */
class R
{
    const ERR_CODE_SUCCESS = 0;    //成功
    const ERR_CODE_FAILED = 1000;    //失败 1000~1999
    const ERR_CODE_FAILED_PACKAGEERROR = 1001;    //请求包长度错误
    const ERR_CODE_FAILED_DBERROR = 1002;    //数据库类型错误
    const ERR_CODE_FAILED_INVALIDCHAR = 1003;    //输入了非法字符
    const ERR_CODE_FAILED_USERNOTFOUND = 1004;    //找不到该用户
    const ERR_CODE_FAILED_USERFROSEN = 1005;    //用户被冻结
    const ERR_CODE_FAILED_UNKNONMESSAGETYPE = 1006;    //未知消息类型
    const ERR_CODE_FAILED_REQUEST_OUTOFRANGE = 1007;    //请求数据过多或者内容过长
    const ERR_CODE_FAILED_SAMEUSERLOGIN = 1008; //完全相同的用户加入房间
    const ERR_CODE_FAILED_AREAIDNOTFOUND = 1009; //没有找到区域ID
    const ERR_CODE_FAILED_ROOMIDNOTFOUND = 1010; //没有找到房间ID
    const ERR_CODE_FAILED_CRC = 1011; //CRC校验错误
    const ERR_CODE_FAILED_CREATEUSER = 1012; //没有找到创建用户失败
    const ERR_CODE_FAILED_KEYWORDFOUND = 1013; //发现关键词
    const ERR_CODE_FAILED_NOT_ENOUGH_GOLD = 1014; //金币不足
    const ERR_CODE_FAILED_ALREADY_BUY = 1015; //已经购买
    const ERR_CODE_FAILED_PRIVATENOTFOUND = 1016; //没有该私人订制
    const ERR_CODE_FAILED_TEAMNOTFOUND = 1017; //没有找到战队ID
    const ERR_CODE_FAILED_GIFTNOTFOUND = 1018; //没有找到礼物ID
    const ERR_CODE_FAILED_COURSENOTFOUND = 1019;    //找不到该课程
    const ERR_CODE_FAILED_COURSEISCLOSE = 1020;    //该课程已经结束
    const ERR_CODE_FAILED_COURSEISFULL = 1021;    //该课程已经满额
    const ERR_CODE_FAILED_COURSEHADBUY = 1022;    //已经购买该课程
    const ERR_CODE_FAILED_COURSESTART = 1023;    //该课程已经开讲超过后台配的分钟数，不允许购买
    const ERR_CODE_FAILED_DB = 1024;    //DB操作失败
    const ERR_CODE_FAILED_REDIS = 1025;    //Redis操作失败
    const ERR_CODE_FAILED_RPCSVR_NOTFOUND = 1026;    //RPC服务端没找到
    const ERR_CODE_FAILED_RPCCALL = 1027;    //RPC调用失败
    const ERR_CODE_INVALID_PARAMETER = 1101;    //无效的请求参数
    const ERR_CODE_SET_GROUP_MSG_MUTE = 2001; //用户设置群消息免打扰失败
    const ERR_CODE_NOT_ALLOW_JOINGROUP = 2002;    //该群不允许加入
    const ERR_CODE_SET_USER_ROLETYPE = 2003;    //用户设置角色失败
    const ERR_CODE_USER_HAS_NO_RIGHT = 2004;    //用户没有权限
    const ERR_CODE_USER_QUIT_GROUP = 2005;    //用户退群失败
    const ERR_CODE_KICK_USER_OUT = 2006;    //踢出用户失败
    const ERR_CODE_SET_ROOMINFO = 2007;    //设置群信息失败
    const ERR_CODE_QRY_GROUPINFO = 2008;    //查询群信息失败
    const ERR_CODE_USER_JOIN_GROUP = 2009;    //用户加群失败
    const ERR_CODE_JOINGROUP_NEED_PACKET = 2010;    //加群需要发红包
    const ERR_CODE_SAMEUSER_LOGIN_OTHER_PLACE = 2011;    //同号在别的地方登陆
    const ERR_CODE_USER_MODINFO_ERR = 2012; //用户修改资料失败
    const ERR_CODE_QUREY_USERGROUPINFO_ERR = 2013;    //用户查询群信息失败
    const ERR_CODE_REDPACKET_EXPIRE = 2014;    //红包已过期
    const ERR_CODE_REDPACKET_HAS_NOLEFT = 2015;    //红包已被抢完
    const ERR_CODE_REDPACKET_NOT_YOURS = 2016;    //红包不是你的
    const ERR_CODE_REDPACKET_NOT_FOUND = 2017;    //找不到该红包
    const ERR_CODE_REDPACKET_HAS_TAKEN = 2018;    //该用户已经领过该红包
    const ERR_CODE_REDPACKET_TAKE = 2019;    //用户领取红包失败
    const ERR_CODE_REDPACKET_CATCH = 2020;    //用户抢红包失败
    const ERR_CODE_REDPACKET_SEND = 2021;    //用户发红包失败
    const ERR_CODE_USER_NOT_IN_GROUP = 2022;    //用户不属于该群
    const ERR_CODE_REDPACKET_NOT_NEED = 2023;    //不需要红包
    const ERR_CODE_GROUP_PRIVATE_CHAT_VISITOR = 2051; //游客不允许发起群私聊
    const ERR_CODE_GROUP_PRIVATE_CHAT_LIMIT = 2052;    //个人贡献未达到私聊门槛

    public static $msg = array(
        self::ERR_CODE_SUCCESS => "成功",
        self::ERR_CODE_FAILED => "失败",
        self::ERR_CODE_FAILED_PACKAGEERROR => "请求包长度错误",
        self::ERR_CODE_FAILED_DBERROR => "数据库类型错误",
        self::ERR_CODE_FAILED_INVALIDCHAR => "输入了非法字符",
        self::ERR_CODE_FAILED_USERNOTFOUND => "找不到该用户",
        self::ERR_CODE_FAILED_USERFROSEN => "用户被冻结",
        self::ERR_CODE_FAILED_UNKNONMESSAGETYPE => "未知消息类型",
        self::ERR_CODE_FAILED_REQUEST_OUTOFRANGE => "请求数据过多或者内容过长",
        self::ERR_CODE_FAILED_SAMEUSERLOGIN => "完全相同的用户加入房间",
        self::ERR_CODE_FAILED_AREAIDNOTFOUND => "没有找到区域ID",
        self::ERR_CODE_FAILED_ROOMIDNOTFOUND => "没有找到房间ID",
        self::ERR_CODE_FAILED_CRC => "CRC校验错误",
        self::ERR_CODE_FAILED_CREATEUSER => "没有找到创建用户失败",
        self::ERR_CODE_FAILED_KEYWORDFOUND => "发现关键词",
        self::ERR_CODE_FAILED_NOT_ENOUGH_GOLD => "金币不足",
        self::ERR_CODE_FAILED_ALREADY_BUY => "已经购买",
        self::ERR_CODE_FAILED_PRIVATENOTFOUND => "没有该私人订制",
        self::ERR_CODE_FAILED_TEAMNOTFOUND => "没有找到战队ID",
        self::ERR_CODE_FAILED_GIFTNOTFOUND => "没有找到礼物ID",
        self::ERR_CODE_FAILED_COURSENOTFOUND => "找不到该课程",
        self::ERR_CODE_FAILED_COURSEISCLOSE => "该课程已经结束",
        self::ERR_CODE_FAILED_COURSEISFULL => "该课程已经满额",
        self::ERR_CODE_FAILED_COURSEHADBUY => "已经购买该课程",
        self::ERR_CODE_FAILED_COURSESTART => "该课程已经开讲超过后台配的分钟数，不允许购买",
        self::ERR_CODE_FAILED_DB => "DB操作失败",
        self::ERR_CODE_FAILED_REDIS => "Redis操作失败",
        self::ERR_CODE_FAILED_RPCSVR_NOTFOUND => "RPC服务端没找到",
        self::ERR_CODE_FAILED_RPCCALL => "RPC调用失败",
        self::ERR_CODE_INVALID_PARAMETER => "无效的请求参数",
        self::ERR_CODE_SET_GROUP_MSG_MUTE => "用户设置群消息免打扰失败",
        self::ERR_CODE_NOT_ALLOW_JOINGROUP => "该群不允许加入",
        self::ERR_CODE_SET_USER_ROLETYPE => "用户设置角色失败",
        self::ERR_CODE_USER_HAS_NO_RIGHT => "用户没有权限",
        self::ERR_CODE_USER_QUIT_GROUP => "用户退群失败",
        self::ERR_CODE_KICK_USER_OUT => "踢出用户失败",
        self::ERR_CODE_SET_ROOMINFO => "设置群信息失败",
        self::ERR_CODE_QRY_GROUPINFO => "查询群信息失败",
        self::ERR_CODE_USER_JOIN_GROUP => "用户加群失败",
        self::ERR_CODE_JOINGROUP_NEED_PACKET => "加群需要发红包",
        self::ERR_CODE_SAMEUSER_LOGIN_OTHER_PLACE => "同号在别的地方登陆",
        self::ERR_CODE_USER_MODINFO_ERR => "用户修改资料失败",
        self::ERR_CODE_QUREY_USERGROUPINFO_ERR => "用户查询群信息失败",
        self::ERR_CODE_REDPACKET_EXPIRE => "红包已过期",
        self::ERR_CODE_REDPACKET_HAS_NOLEFT => "红包已被抢完",
        self::ERR_CODE_REDPACKET_NOT_YOURS => "红包不是你的",
        self::ERR_CODE_REDPACKET_NOT_FOUND => "找不到该红包",
        self::ERR_CODE_REDPACKET_HAS_TAKEN => "该用户已经领过该红包",
        self::ERR_CODE_REDPACKET_TAKE => "用户领取红包失败",
        self::ERR_CODE_REDPACKET_CATCH => "用户抢红包失败",
        self::ERR_CODE_REDPACKET_SEND => "用户发红包失败",
        self::ERR_CODE_USER_NOT_IN_GROUP => "用户不属于该群",
        self::ERR_CODE_REDPACKET_NOT_NEED => "不需要红包",
        self::ERR_CODE_GROUP_PRIVATE_CHAT_VISITOR => "游客不允许发起群私聊",
        self::ERR_CODE_GROUP_PRIVATE_CHAT_LIMIT => "个人贡献未达到私聊门槛",
    );

}