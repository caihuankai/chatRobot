<?php
namespace WeChat;

use app\common\controller\JsonResult;


class appJiahua extends \EasyWeChat\Foundation\Application
{
    
    use \traits\Singleton,
        \traits\sysJson;
    
    
    /**
     * ajax请求
     * 静态方法保证单例时不变
     *
     * @var bool
     */
    protected static $ajaxBool = true;
    
    /**
     * 跳转key
     *
     * @var string
     */
    public $redirectKey = 'loginRedirectUrl';
    
    
    /**
     * 保存oauthData
     * 减少\think\Session::get('weChat_user')
     *
     * @var array
     */
    protected $oauthData = null;
    
    
    /**
     * @var string
     */
    protected $oauthUnionIdSessionKey = 'oauthUnionId';
    
    
    protected $extendConfig;
    
    
    /**
     * 用户授权域
     *
     * @var null|array
     */
    protected $scopes = null;
    
    
    /**
     * WeChat constructor.
     *
     * @param bool $extendConfig 1为默认，2为pc，3为app
     */
    public function __construct($extendConfig = 1)
    {
        $config = self::getSysConfig();
        $config['cache'] = new \WeChat\cache(); // 使用自定义的缓存类
        // 切换应用
        if ($extendConfig === 3){ // app
            $config = array_merge($config, $config['open-app']);
        }else if ($extendConfig === 2){ // 非微信浏览器
        	$config = array_merge($config, $config['open-web-jiahua']);
        }

        $this->extendConfig = $extendConfig;
        
        // 重写
        $this->addProvider(\WeChat\MaterialServiceProvider::class);
        
        parent::__construct($config);
    }
    
    
    /**
     * weChat配置的实例化
     *
     * @return static
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function weChatInstance()
    {
        return self::instance(1);
    }
    
    
    /**
     * app配置的实例化
     *
     * @return static
     * @author aozhuochao
     */
    public static function appInstance()
    {
        return self::instance(3);
    }
    
    
    /**
     * 返回配置数据
     *
     * @param $name
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getSysConfig($name = null)
    {
        return \helper\ConfigSys::instance()->setMergeBool(true)->get($name, null, 'wechat');
    }
    
    /**
     * 获取微信配置的app_id
     *
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getWeChatAppId()
    {
        return self::getSysConfig('app_id');
    }
    
    
    /**
     * 获取微信开放平台的app_id
     *
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function getWeChatPcAppId()
    {
        return self::getSysConfig('open-web-jiahua')['app_id'];
    }
    
    /**
     * 微信回调
     * 可用->toArray()转数组
     *
    Overtrue\Socialite\User::__set_state(array(
        'attributes' =>
        array (
            'id' => 'oASvRwTBpRy1hpcfKC-wfGsbuAGs',
            'name' => NULL,
            'nickname' => NULL,
            'avatar' => NULL,
            'email' => NULL,
            'original' =>
            array (
            'access_token' => 'OLFdLAoVTL45JasCnyQwNIMOEI4iZT0UKOZ0kCxzWNzeLnovS6n31KyJ5n9EIu-iDnZQNMqhJXsR98GFc3tJYQ',
            'expires_in' => 7200,
            'refresh_token' => 'cKpPM9o40dImDwWZco2kzoc-hDxP3vP6FYmh0y1zgWHIC-7uOaF6jvouLpVOXPob2VKq-YbN--hGur4t0CyGXA',
            'openid' => 'oASvRwTBpRy1hpcfKC-wfGsbuAGs',
            'scope' => 'snsapi_base',
            'unionid' => 'oIs-x0SWnzKwKt6zFcqDhCg31O-I',
        ),
        'token' =>
        Overtrue\Socialite\AccessToken::__set_state(array(
        'attributes' =>
            array (
            'access_token' => 'OLFdLAoVTL45JasCnyQwNIMOEI4iZT0UKOZ0kCxzWNzeLnovS6n31KyJ5n9EIu-iDnZQNMqhJXsR98GFc3tJYQ',
            'expires_in' => 7200,
            'refresh_token' => 'cKpPM9o40dImDwWZco2kzoc-hDxP3vP6FYmh0y1zgWHIC-7uOaF6jvouLpVOXPob2VKq-YbN--hGur4t0CyGXA',
            'openid' => 'oASvRwTBpRy1hpcfKC-wfGsbuAGs',
            'scope' => 'snsapi_base',
            'unionid' => 'oIs-x0SWnzKwKt6zFcqDhCg31O-I',
            ),
        )),
        ),
    ))
     *
     * @see https://easywechat.org/zh-cn/docs/oauth.html#获取已授权用户
     * @return \Overtrue\Socialite\User|mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function weChatOAuthUser()
    {
        $user = $this->oauth->user();
        
        // 检测unionid
        $data = $user->getOriginal();
        if (!isset($data['unionid'])) { // 偶尔base没有获取到union_id时，用弹框确定来登录
            self::$ajaxBool = false;
            $this->setScopes(['snsapi_userinfo']);
            $this->getOauthData(true);
        }
        
        $this->oauthData = $user;
        
        // 之后写入session
        
        return $user;
    }
    
    
    /**
     * 获取自动注册的数据
     * 要保证当前方法不用到微信appId
     *
     * @param bool $bool 强制提示登录
     * @return false|\Overtrue\Socialite\User
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getOauthData($bool = false)
    {
        $userData = is_null($this->oauthData) ? \think\Session::get('weChat_user') : $this->oauthData;
        $request = request();
        $redirectKey = $this->redirectKey;
        $urlJoin = '';
        $redirectUrl = $request->param($redirectKey, ''); // 指定会跳地址
        if (empty($userData) || $bool) { // 未登录
            $oauth = $this->oauth;
    
            $oauth->scopes($this->getScopes());
            if ($redirectUrl) {
                $urlJoin = "?{$redirectKey}=" . urlencode($redirectUrl);
            } else if (!self::$ajaxBool) { // ajax不获取HTTP_REFERER
                $redirectUrl = $request->server('HTTP_REFERER');
            }
            $registerUserUrl = url('Login/registerUser', '', true, get_config_host('JH_DOMAIN')); // php的注册url
//             $registerUserUrl = url('Login/registerUser', '', true, request()->domain()); // php的注册url
            $oauth->setRedirectUrl($registerUserUrl . $urlJoin);
    
            $oauth = $oauth->redirect();
            
            if (self::$ajaxBool){ // ajax输出
                $redirectUrl && \think\Session::set('registerUserRedirectUrl', $redirectUrl);
                abort($this->errSysJson(
                    [
                        'url' => $oauth->getTargetUrl(),
                        'registerUserUrl' => $registerUserUrl,
                        'state' => md5(time()), // todo 临时放置，待改为全局调用$oauth->stateless()
                    ],
                    JsonResult::getMsg(JsonResult::ERR_NOT_LOGIN),
                    JsonResult::ERR_NOT_LOGIN
                ));
            }else{
                $redirectUrl && \think\Session::set('registerUserRedirectUrl', $redirectUrl ? $redirectUrl : $request->url());
                $oauth->send();
            }
            
            exit;
        }
        
        return $userData;
    }
    
    public function getOauthOpenId()
    {
        $data = $this->getOauthData()->getOriginal();
    
        return !empty($data['openid']) ? $data['openid'] : '';
    }
    
    
    public function getOauthUnionId()
    {
        $id = \think\Session::get($this->oauthUnionIdSessionKey);
        if (!empty($id)){
            return $id;
        }
        
        $data = $this->getOauthData()->getOriginal();
    
        return !empty($data['unionid']) ? $data['unionid'] : '';
    }
    
    
    public function setOauthUnionId($unionId)
    {
        \think\Session::set($this->oauthUnionIdSessionKey, $unionId);
    }
    
    
    /**
     *
     *
     * 返回数据
    object(EasyWeChat\Support\Collection)#175 (1) {
        ["items":protected] => array(13) {
            ["subscribe"] => int(1)
            ["openid"] => string(28) "oASvRwTBpRy1hpcfKC-wfGsbuAGs"
            ["nickname"] => string(24) "名字"
            ["sex"] => int(1)
            ["language"] => string(5) "zh_CN"
            ["city"] => string(6) "广州"
            ["province"] => string(6) "广东"
            ["country"] => string(6) "中国"
            ["headimgurl"] => string(129) "http://wx.qlogo.cn/mmopen/eVKreJDXibZlFrKqu0ATrsbA3V639nzVGkPJhmpukrHDnibBO7dOOuN0WzRYniaU4wXGf7ichts6harDvVnia16PYZLgxVcedJ6rn/0"
            ["subscribe_time"] => int(1495178459)
            ['unionid'] => 'oIs-x0SWnzKwKt6zFcqDhCg31O-I',
            ["remark"] => string(0) ""
            ["groupid"] => int(0)
            ["tagid_list"] => array(0) {
            }
        }
    }
     *
     * @return array
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getOauthUserData()
    {
        $data = $this->getOauthData();
        if (!empty($data->getName())) { // 网页不能获取用户详情
            return $data->getOriginal();
        }
        
//        $original = $data->getAttribute('original');
//        $id = !empty($original['unionid']) ? $original['unionid'] : $data->getId();
        /** @var \Overtrue\Socialite\User $data */
        $data = $this->user->get($data->getId());
        
        return $data->toArray();
    }
    
    

    
    /**
     * 设置是否ajax请求
     *
     * @param bool $ajaxBool
     */
    public function setAjaxBool($ajaxBool = true)
    {
        self::$ajaxBool = $ajaxBool;
    }

    
    
    /**
     * 发送用户消息
     *
     * @param $message
     * @param $openId
     * @return bool
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public static function staffMsg($message, $openId)
    {
        if (strlen($openId) < 10){ // 被去除了的open_id
            return false;
        }
        
        
        $bool = true;
        try{
            self::weChatInstance()->staff->message($message)->to($openId)->send();
        }catch (\Exception $exception){
            // 48小时未和公众号互动，则无法给你推送信息
            $code = $exception->getCode();
            $msgHeader = '发送用户失败' . $openId . '。错误码：' . $exception->getCode();
            if ($code === 45015) {
                \think\log::error($msgHeader . '-------48小时未和公众号互动，则无法给你推送信息');
            }else if ($code === 45047){
                \think\log::error($msgHeader . '-------短时间发送次数过多，需要用户与公众号交互后，会重置限制');
            }else{
                \think\log::error($msgHeader . '。错误信息：' . $exception->getMessage());
            }
            
            $bool = false;
        }
        
        return $bool;
    }
    
    /**
     * @return null
     */
    public function getScopes()
    {
        return $this->scopes ?: ($this->extendConfig == 1 ? ['snsapi_base'] : ['snsapi_login']);
    }
    
    /**
     * @param null|array $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
        
        return $this;
    }
    
    
}