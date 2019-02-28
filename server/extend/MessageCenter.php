<?php
/**
 * 消息中心工具类
 * @author liujuneng
 */
class MessageCenter
{
	use \traits\Singleton;
	
	/**
	 * 讲师/助教	成功创建课程
	 */
	const COURSE_CREATE = [
			"type"=>0,
			"title"=>"成功创建课程",
			"lead"=>"您成功创建了课程【%s】。",
			"content"=>"恭喜您，成功创建课程【%s】",
			"link"=>1
	];
	
	/**
	 * 讲师/助教	课程即将开始
	 */
	const COURSE_START_TEACHER = [
			"type"=>0,
			"title"=>"课程即将开始",
			"lead"=>"您创建的课程【%s】即将开始，请做好讲课准备。",
			"content"=>"开课通知：您创建的课程准备开始啦，记得讲课哦！
您的课程：【%s】
开课时间：%s",
			"link"=>1
	];
	
	/**
	 * 学生/流量主/嘉宾	课程即将开始
	 */
	const COURSE_START_STUDENT = [
			"type"=>0,
			"title"=>"课程即将开始",
			"lead"=>"您报名的课程【%s】即将开始啦~快来听课吧~",
			"content"=>"开播通知：您报名的课程马上开课啦，记得参加哦！
您已报名【%s】
开播时间：%s
主讲人：%s",
			"link"=>1
	];
	
	/**
	 * 学生/流量主/嘉宾	新课程上线
	 */
	const COURSE_CREATE_TO_LIVE_FOCUS = [
			"type"=>0,
			"title"=>"新课程上线",
			"lead"=>"您关注的【%s】更新了课程【%s】，快来看看吧~",
			"content"=>"新课上线通知
%s
您关注的【%s】更新课程啦！
课程名称：【%s】
开课时间：%s
开课地点：大策略",
			"link"=>1
	];
	
	/**
	 * 讲师/助教	成功创建系列课
	 */
	const COURSE_SERIES_CREATE = [
			"type"=>0,
			"title"=>"成功创建系列课",
			"lead"=>"您成功创建了系列课【%s】。",
			"content"=>"恭喜您，成功创建系列课【%s】",
			"link"=>2
	];
	
	/**
	 * 讲师/助教	成功创建系列课
	 */
	const COURSE_SERIES_CREATE_CHILD = [
			"type"=>0,
			"title"=>"成功创建系列课",
			"lead"=>"您成功创建了课程【%s】。",
			"content"=>"恭喜您，成功创建课程【%s】",
			"link"=>3
	];
	
	/**
	 * 讲师/助教	课程即将开始
	 */
	const COURSE_SERIES_START_TEACHER = [
			"type"=>0,
			"title"=>"课程即将开始",
			"lead"=>"您创建的课程【%s】即将开始，请做好讲课准备。",
			"content"=>"开课通知：您创建的课程准备开始啦，记得讲课哦！
您的课程：【%s】
开课时间：%s",
			"link"=>3
	];
	
	/**
	 * 学生/流量主/嘉宾	课程即将开始
	 */
	const COURSE_SERIES_START_STUDENT = [
			"type"=>0,
			"title"=>"课程即将开始",
			"lead"=>"您报名的课程【%s】即将开始啦~快来听课吧~",
			"content"=>"开播通知：您报名的课程马上开课啦，记得参加哦！
您已报名【%s】
开课时间：%s
主讲人：%s",
			"link"=>3
	];
	
	/**
	 * 学生/流量主/嘉宾	新课程上线
	 */
	const COURSE_SERIES_CREATE_TO_LIVE_FOCUS = [
			"type"=>0,
			"title"=>"新课程上线",
			"lead"=>"您关注的【%s】更新了课程【%s】，快来看看吧~",
			"content"=>"新课上线通知
%s
您关注的【%s】更新课程啦！
系列课程名称：【%s】
课程安排：预计更新%d节
开课地点：大策略",
			"link"=>2
	];
	
	/**
	 * 学生/流量主/嘉宾	系列课有更新
	 */
	const COURSE_SERIES_CREATE_CHILD_TO_BUY_USER = [
			"type"=>0,
			"title"=>"系列课有更新",
			"lead"=>"您购买的系列课【%s】更新课程啦~快来看看吧~",
			"content"=>"系列课新课通知
%s
您购买的系列课【%s】更新课程啦！
课程名称：【%s】
开课时间：%s
开课地点：大策略",
			"link"=>3
	];
	
	/**
	 * 讲师/助教	成功发布观点
	 */
	const VIEWPOINT_CREATE = [
			"type"=>0,
			"title"=>"成功发布观点",
			"lead"=>"您成功创建了观点【%s】。",
			"content"=>"恭喜您，成功发布了观点【%s】",
			"link"=>4
	];
	
	/**
	 * 学生/流量主/嘉宾	新观点发布
	 */
	const VIEWPOINT_CREATE_TO_LIVE_FOCUS = [
			"type"=>0,
			"title"=>"新观点发布",
			"lead"=>"您关注的【%s】发布了新观点【%s】，快来看看吧~",
			"content"=>"新观点发布通知
%s
您关注的【%s】发表观点啦！
观点名称：【%s】
快去看看吧~",
			"link"=>4
	];
	
	/**
	 * 学生/流量主/嘉宾	评论审核通过
	 */
	const REVIEW_LIVE_ROOM_MESSAGE_PASS = [
			"type"=>0,
			"title"=>"评论审核通过",
			"lead"=>"您发表的评论审核通过了，快去看看吧~",
			"content"=>"Live直播间聊天室评论审核通过
您在【%s】的Live直播间聊天室发表的评论审核通过了，快去看看吧~",
			"link"=>6
	];
	
	/**
	 * 学生/流量主/嘉宾	评论审核通过
	 */
	const REVIEW_COURSE_ROOM_MESSAGE_PASS = [
			"type"=>0,
			"title"=>"评论审核通过",
			"lead"=>"您发表的评论审核通过了，快去看看吧~",
			"content"=>"课程直播间聊天室评论审核通过
您在【%s】的【%s】课程直播间聊天室发表的评论审核通过了，快去看看吧~",
			"link"=>7
	];
	
	/**
	 * 学生/流量主/嘉宾	直播间有新更新
	 * C++完成
	 */
	const LIVE_ROOM_UPDATE = [
			"type"=>0,
			"title"=>"直播间有新更新",
			"lead"=>"您关注【%s】的直播间更新了内容，快来瞧瞧吧~",
			"content"=>"【%s】的Live直播间有新内容更新",
			"link"=>5
	];
	
	/**
	 * 学生/流量主/嘉宾	评论被回复
	 * C++完成
	 */
	const ANSWER_LIVE_ROOM_QUESTION = [
			"type"=>1,
			"title"=>"评论被回复",
			"lead"=>"【%s】回复了您，快来瞧瞧~",
			"content"=>"Live直播间聊天室评论被回复
【%s】回复：%s
我：%s",
			"link"=>6
	];
	
	/**
	 * 学生/流量主/嘉宾	评论被回复
	 * C++完成
	 */
	const ANSWER_COURSE_ROOM_QUESTION= [
			"type"=>1,
			"title"=>"评论被回复",
			"lead"=>"【%s】回复了您，快来瞧瞧~",
			"content"=>"课程直播间聊天室评论被回复
【%s】回复：%s
我：%s",
			"link"=>7
	];
	
	/**
	 * 学生/流量主/嘉宾	评论审核通过
	 */
	const REVIEW_COURSE_MESSAGE_PASS = [
			"type"=>0,
			"title"=>"评论审核通过",
			"lead"=>"您发表的评论审核通过了，快去看看吧~",
			"content"=>"课程评论审核通过
您在【%s】发表的评论审核通过了，快去看看吧~",
			"link"=>8
	];
	
	/**
	 * 学生/流量主/嘉宾	评论审核通过
	 */
	const REVIEW_COURSE_SERIES_MESSAGE_PASS = [
			"type"=>0,
			"title"=>"评论审核通过",
			"lead"=>"您发表的评论审核通过了，快去看看吧~",
			"content"=>"系列课评论审核通过
您在【%s】发表的评论审核通过了，快去看看吧~",
			"link"=>2
	];
	
	/**
	 * 学生/流量主/嘉宾	评论审核通过
	 */
	const REVIEW_VIEWPOINT_MESSAGE_PASS = [
			"type"=>0,
			"title"=>"评论审核通过",
			"lead"=>"您发表的评论审核通过了，快去看看吧~",
			"content"=>"观点评论审核通过
您在【%s】发表的评论审核通过了，快去看看吧~",
			"link"=>4
	];
	
	/**
	 * 学生/流量主/嘉宾	评论被回复
	 */
	const ANSWER_COURSE_QUESTION = [
			"type"=>1,
			"title"=>"评论被回复",
			"lead"=>"【%s】回复了您，快来瞧瞧~",
			"content"=>"课程评论被回复
【%s】回复：%s
我：%s",
			"link"=>8
	];
	
	/**
	 * 学生/流量主/嘉宾	评论被回复
	 */
	const ANSWER_COURSE_SERIES_QUESTION = [
			"type"=>1,
			"title"=>"评论被回复",
			"lead"=>"【%s】回复了您，快来瞧瞧~",
			"content"=>"系列课评论被回复
【%s】回复：%s
我：%s",
			"link"=>2
	];
	
	/**
	 * 学生/流量主/嘉宾	评论被回复
	 */
	const ANSWER_VIEWPOINT_QUESTION = [
			"type"=>1,
			"title"=>"评论被回复",
			"lead"=>"【%s】回复了您，快来瞧瞧~",
			"content"=>"观点评论被回复
【%s】回复：%s
我：%s",
			"link"=>4
	];
	
	/**
	 * 栏目订阅即将过期提醒
	 */
	const COLUMN_EXPIRY_REMIND = [
			"type"=>"%u",
			"title"=>"您订阅的【%s】栏目，将于%u天后到期",
			"lead"=>"您订阅的【%s】栏目，将于%u天后到期",
			"content"=>"您订阅的【%s】栏目，将于%u天后到期",
			"link"=>0
	];
	
	/**
	 * 学生	特训班开课
	 */
	const TRAIN_COURSE_START_STUDENT = [
			"type"=>0,
			"title"=>"特训班开课",
			"lead"=>"您报名的特训班【%s】开课啦~进来听课吧！",
			"content"=>"开播通知：您报名的特训班开课啦，马上来听课吧~
特训班名称：【%s】
开播时间：%s",
			"link"=>9
	];
	
	/**
	 * 学生	特训班报名
	 */
	const TRAIN_COURSE_ENROLL_STUDENT = [
			"type"=>0,
			"title"=>"特训班报名",
			"lead"=>"您已报名【%s】，请留意开课提醒！",
			"content"=>"您已报名【%s】，请留意开课提醒！
开课时间：%s
主讲人：%s",
			"link"=>10
	];
	
	/**
	 * 学生/流量主/嘉宾	月度课/季度课 课程即将开始
	 */
	const COURSE_SERIES_H5_START_STUDENT = [
			"type"=>3,
			"title"=>"课程即将开始",
			"lead"=>"您报名的课程【%s】即将开始啦~快来听课吧~",
			"content"=>"开播通知：您报名的课程马上开课啦，记得参加哦！
您已报名【%s】
开课时间：%s
主讲人：%s",
			"link"=>11
	];
	
	/**
	 * 学生/流量主/嘉宾	月度课/季度课 系列课有更新
	 */
	const COURSE_SERIES_H5_CREATE_CHILD_TO_BUY_USER = [
			"type"=>3,
			"title"=>"课程有更新",
			"lead"=>"您购买的月度/季度课【%s】更新课程啦~快来看看吧~",
			"content"=>"系列课新课通知
%s
您购买的月度/季度课【%s】更新课程啦！
课程名称：【%s】
开课时间：%s
开课地点：大策略",
			"link"=>11
	];
	
	/**
	 * 学生/流量主/嘉宾	月度课/季度课  订阅即将过期提醒
	 */
	const COURSE_SERIES_H5_EXPIRY_REMIND = [
			"type"=>"%u",
			"title"=>"到期信息提醒",
			"lead"=>"您购买的课程【%s】，将于：%s到期。请联系助理进行续费。",
			"content"=>"课程即将到期
您购买的【%s】，将于：%s到期。请联系助理进行续费。",
			"link"=>0
	];
	
	
	protected function getMessageInfo($const, array $linkInfos, array $replaceArray){
		$formatArray = [];
		if (is_string($const)) {
			$formatArray = constant('self::' . $const);
		}elseif (is_array($const)) {
			$formatArray = $const;
		}
		$messageInfo = $this->getMsg($formatArray, $replaceArray);
		$messageInfo['link_info'] = $this->getLinkInfo($const, $linkInfos);
		return $messageInfo;
	}
	
	/**
	 * 获取linkInfo信息
	 * @param unknown $type
	 * @param unknown $params
	 * @return string
	 */
	protected function getLinkInfo($const, array $params){
		$linkInfo = "";
		$needKeys = [];
		$link = 0;
		if (is_string($const)) {
			$link = constant('self::' . $const)['link'];
		}elseif (is_array($const) && isset($const['link'])) {
			$link = $const['link'];
		}
		switch ($link) {
			case 0://仅显示消息不跳转页面
				break;
			case 1://课程详情页
				$needKeys = ['courseId'];
				break;
			case 2://系列课详情页
				$needKeys = ['courseId'];
				break;
			case 3://系列课-子课程详情页
				$needKeys = ['courseId'];
				break;
			case 4://观点详情页
				$needKeys = ['viewpointId'];
				break;
			case 5://讲师的Live直播间
				$needKeys = ['teacherId'];
				break;
			case 6://讲师Live直播间的聊天室
				$needKeys = ['teacherId'];
				break;
			case 7://讲师课程直播间的聊天室
				$needKeys = ['courseId'];
				break;
			case 8://课程介绍页
				break;
			case 9://特训班详情页
				$needKeys = ['courseId'];
				break;
			case 10://特训班预告页
				$needKeys = ['courseId'];
				break;
			case 11://月度课/季度课介绍页
				$needKeys = ['courseId'];
				break;
		}
		
		if ($this->checkLinkInfoKey($needKeys, $params)) {
			$linkInfo = json_encode($params);
		}else {
			//必传值不存在，直接返回false
			\think\Log::error(['创建消息中心记录是缺少link_info参数', $const, $params]);
			return false;
		}
		
		return $linkInfo;
	}
	
	/**
	 * 检查linkInfo的必传值是否都存在
	 * @param unknown $needKeys
	 * @param unknown $params
	 * @return boolean
	 */
	protected function checkLinkInfoKey($needKeys, array $params){
		$isOK = true;
		foreach ($needKeys as $key){
			if (!array_key_exists($key, $params)) {
				$isOK = false;
				break;
			}
		}
		return $isOK;
	}
	
	/**
	 * sprintf处理字符串
	 *
	 * @param string $format
	 * @param array  $arg
	 * @return string
	 */
	protected function getMsg(array $formatArray, array $arg = []){
		foreach ($formatArray as $key=>$format){
			if (isset($arg[$key])) {
				$formatArray[$key] = sprintf($format, ...$arg[$key]);
			}
		}
		return $formatArray;
	}
	
	/**
	 * 批量创建消息记录
	 * @param unknown $const	本文定义的文案模板名或具体文案数组
	 * @param array $linkInfos	着陆页额外参数
	 * @param array $replaceArray	文案中要替换的内容，按键名和替换内容拼接数组
	 * @param array $userIdList		消息的所有者
	 * @return boolean|number|string|boolean
	 */
	public function createMessageRecords($const, array $linkInfos, array $replaceArray, array $userIdList){
		$records = [];
		$insertNum = 100;
		$i = 0;
		
		$messageInfo = $this->getMessageInfo($const, $linkInfos, $replaceArray);
		$messageModel = new \app\admin\model\Message();
		$userCount = count($userIdList);
		foreach ($userIdList as $userId){
			$records[] = array_merge($messageInfo, ['user_id'=>$userId]);
			$i++;
			if ($i >= $userCount || $i >= $insertNum) {
				$result = $messageModel->createMessageBatch($records);
				if ($result === true) {
					$records = [];
					$i = 0;
				}else {
					\think\Log::error(['创建消息记录(talk_message)出错:' . $result, $messageInfo, $userIdList]);
					return $result;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * 获取数组中特定值，适用数组：
	 * array(
	 * 	0=>array(
	 * 		'key'=>'value1'
	 * 		),
	 * 	1=>array(
	 * 		'key'=>'value2'
	 * 		),
	 * )
	 * @param unknown $array
	 * @param unknown $key
	 * @return unknown[]
	 */
	public function getArrayValueByKey($array, $key){
		$resultList = [];
		foreach ($array as $infos){
			$resultList[] = $infos[$key];
		}
		return $resultList;
	}
	
	
	
	
	
	
	
	
	
	
	
	
}