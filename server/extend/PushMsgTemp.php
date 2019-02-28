<?php

/**
 * 推送消息的模板
 * 方便统一管理
 */
class PushMsgTemp
{
    use \traits\Singleton;
    
    /**
     * 课程创建时推送关注直播间的用户
     */
    const COURSE_CREATE_TO_LIVE_FOCUS = '新课上线通知
%s
您关注的【%s】更新课程啦！
课程名称：【%s】
开课时间：%s
开课地点：大策略
<a href="%s">点击进入即可参加</a>';
    const COURSE_SERIES_CREATE_TO_LIVE_FOCUS = '新课上线通知
%s
您关注的【%s】更新课程啦！
系列课程名称：【%s】
课程安排：预计更新%d节
开课地点：大策略
<a href="%s">点击进入即可参加</a>';
    
    /**
     * 购买系列课的用户需要推送给所有购买者的消息
     */
    const COURSE_SERIES_THE_CREATE_TO_COURSE_BUY_USER = '系列课新课通知
%s
您购买的系列课【%s】更新课程啦！
课程名称：【%s】
开课时间：%s
开课地点：大策略
<a href="%s">点击进入即可参加</a>';
    
    
    /**
     * 个人创建课程提醒
     */
    const COURSE_CREATE = "恭喜您，成功创建课程【%s】。<a href='%s'>点击即可进入讲课</a>";
    const COURSE_SERIES_CREATE = '恭喜您，成功创建系列课【%s】
课程安排：预计更新%d节
<a href="%s">点击即可新增课程</a>';
    const COURSE_TRAIN_CREATE = "您已报名【%s】，请留意开课提醒！
开课时间：%s
主讲人：%s";
    const COURSE_TRAIN_START = "开播通知：您报名的特训班开课啦，马上来听课吧~
特训班名称：【%s】
开播时间：%s
<a href='%s'>点击进入>></a>";
    
    
    const COURSE_FORBIDDEN = "您的%s已被禁用\n您可在大策略公众号回复课程名称+解禁，来申请解禁以及了解原因。";
    
    
    /**
     * 菜单上的联系我们
     */
    const MENU_CONTACT_US = '您好，欢迎使用大策略，很高兴为您服务！在使用产品的过程中如有疑问，可直接在后台留言咨询，我们会有专门的工作人员给到您回复！

咨询时间 上午9：00-下午5:30

客服热线：400-9012-600';

    const MENU_SMALL_PROGRAM = '大策略小程序正在研发测试中，敬请期待';

    const MENU_CUSTOMER_SERVICE = '我叫小策君，我已经等您很久了！
欢迎您来到大策略平台
这里有最新的金融资讯、最实战的课程
还有知名投资达人讲师团分享的干货
不管您是股市小白，还是老股龄投资者
在这里，您都能找到你想要的
请添加我的微信“dacelue666”为好友
或拨打客服服务电话020-22922466
我将一对一为您全程服务
';

    const MENU_MATERIAL = '全方位剖析热点话题、多角度解读政策消息、深度挖掘行业研报尽在【大策略订阅号】，带你轻松布局板块牛股。逢交易日12点准时更新，还不赶紧来看看！';
    
    
    /**
     * sprintf处理字符串
     *
     * @param string $format
     * @param array  $arg
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getMsg($format, array $arg = [])
    {
        return sprintf($format, ...$arg);
    }
    
}