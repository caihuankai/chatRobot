<?php
/**
 * Created by PhpStorm.
 * User: code
 * Date: 2016/11/11
 * Time: 13:39
 */

namespace app\common\validate;


use app\common\controller\JsonResult;


class ValidateBase extends \ThinkPHP\Validate
{
    
    /**
     * 默认变量名对应的规则
     *
     * @var array
     */
    private $ruleDefault = [
        'id' => 'require|number|>:0',
        'ids' => 'require|requireCallback:\app\common\validate\ValidateBase::idsValidate',
        'userId' => 'require|number|>:0',
        'content' => 'require',
        'page' => 'number|gt:0',
        'gender' => 'requireCallback:\app\common\validate\ValidateBase::genderValidate', // 性别，需要自定义$message['genderValidate']
        'telExistsValidate'      => 'requireCallback:\app\common\validate\ValidateBase::telExistsValidate', // 手机号，检测手机号是否正确并不存在
        'date' => 'date', // 2016-11-28 15:02:07
        'minNowDate' => 'requireCallback:\app\common\validate\ValidateBase::minNowDate', // 不能小于当前时间
        'repeatLimit' => 'requireCallback:repeatLimit', // 防止重复提交
    ];

    
    /**
     * message中:attribute的意思的默认值
     *
     * @var array
     */
    private $attributeMeanMapDefault = [
        'title' => '标题',
        'content' => '内容',
        'summary' => '摘要',
    ];
    
    
    /**
     * 默认message
     * 让默认message不被重写
     *
     * @var array
     */
    private $messageDefault = [
        'telExistsValidate' => JsonResult::ERR_TEL_IS_REGISTER,
        'repeatLimit' => JsonResult::ERR_SUBMIT_FAST,
    ];
    
    
    /**
     * 配置message中:attribute的意思
     *
     * @var array
     */
    protected $attributeMeanMap = [];
    
    /**
     * @inheritDoc
     */
    public function __construct(array $rules = [], array $message = [])
    {
        $this->rule = array_merge($this->ruleDefault, $this->rule);
        
        parent::__construct($rules, $message);
        $this->attributeMeanMap = array_merge($this->attributeMeanMapDefault, $this->attributeMeanMap);
        $this->message = array_merge($this->messageDefault, $this->message);
    }
    
    
    /**
     * @inheritDoc
     */
    protected function getRuleMsg($attribute, $title, $type, $rule)
    {
        $title = isset($this->attributeMeanMap[$title]) ? $this->attributeMeanMap[$title] : $title;
    
        return parent::getRuleMsg($attribute, $title, $type, $rule);
    }
    
    
    /**
     * 检测性别
     *
     * @param $value
     * @param $data
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    static public function genderValidate($value, $data)
    {
        /** @var \app\common\model\User $model */
        $model = model('common/User');
        
        return isset($data['gender']) && array_key_exists($data['gender'], $model->mysql_field_gender) ? '' : 'genderValidate';
    }
    
    /**
     * @param $value
     * @param $data
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    static public function telExistsValidate($value, $data)
    {
        $msg = JsonResult::TEL_MUST_NUMBER; // 无意义
        if (isset($data['tel']) && is_phone($data['tel'])) {
            $msg = checkTelIsExist($data['tel']);
        }
        
        return empty($msg)?'':'telExistsValidate'; // 返回空为没有报错
    }
    
    
    /**
     * 检测ids为数组或整数，过滤0
     *
     * @param $value
     * @param $data
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    static public function idsValidate($value, $data)
    {
        $func = function ($val){
            return is_numeric($val) && $val> 0;
        };
        $msg = JsonResult::ERR_PARAMETER;
        
        if (isset($data['ids'])) {
            if ($func($data['ids'])){
                $msg = '';
            }else if (is_array($data['ids']) && empty(array_filter($data['ids'], $func))){
                $msg = '';
            }
        }
    
        return empty($msg) ? '' : 'idsValidate';
    }
    
    /**
     * 检测$data是正整数或者全为正整数的数组
     * 
     * @param $data
     * @return string
     * @author liujuneng
     */
    static public function intsValidate($data)
    {
    	$func = function ($val){
    		return is_numeric($val) && is_int($val + 0) && $val > 0;
    	};
    	$msg = JsonResult::ERR_PARAMETER;
    	
    	if (!empty($data)) {
    		if ($func($data)) {
    			$msg = '';
    		}elseif (is_array($data) && !empty(array_filter($data, $func))){
    			$msg = '';
    		}
    	}
    	
    	return empty($msg) ? '' : $msg;
    }
    
    /**
     * 不能小于当前时间
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    static public function minNowDate($value, $data)
    {
        $msg = \app\common\controller\JsonResult::ERR_NOT_MIN_NOW;
        $nowDate = new \DateTime('now -1 hours');
        if (($date = date_create_from_format('Y-m-d H:i:s', $data['date'])) !== false && $date >= $nowDate){ // 一小时的差距
            $msg = '';
        }else if (($date = date_create_from_format('Y-m-d H:i', $data['date'])) !== false && $date >= $nowDate){
            $msg = '';
        }
    
        return empty($msg) ? '' : $msg;
    }
    
}