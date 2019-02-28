<?php
use \qiNiu\Auth;

/**
 * 七牛云存储
 * User: Administrator
 * Date: 2017/5/4
 * Time: 16:13
 */
class Qiniu
{
    use \traits\Singleton;
    
    private $accessKey;
    private $secretKey;
    private $bucket;
    /**
     * @var Auth
     */
    private $auth;
    
    /**
     * @var \Qiniu\Zone
     */
    private $zone = null;
    
    protected $domainList = [];
    

    public function __construct($accessKey = '', $secretKey = '', $bucket = '')
    {
        if (empty($accessKey)) {
            $this->accessKey = config('QINIU.ACCESS_KEY');
        } else {
            $this->accessKey = $accessKey;
        }

        if (empty($secretKey)) {
            $this->secretKey = config('QINIU.SECRET_KEY');
        } else {
            $this->secretKey = $secretKey;
        }

        if (empty($bucket)) {
            $this->bucket = config('QINIU.BUCKET');
        } else {
            $this->bucket = $bucket;
        }

        $this->auth = new Auth($this->accessKey, $this->secretKey);
    }
    
    /**
     * 获取七牛上传token
     *
     * @param null|array $policy
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getUploadToken($policy = null)
    {
        // 上传文件到七牛后， 七牛将文件名和文件大小回调给业务服务器.
        // 可参考文档: http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
        $token = $this->getAuth()->uploadToken($this->bucket, null, 3600, $policy);
        return $token;
    }
    
    
    /**
     * 根据微信的serverId获取图片地址
     *
     * @param $img
     * @param $width
     * @param $height
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getWeChatImg($img, $width = '', $height = '')
    {
        if (empty($img)) {
            return '';
        }
        $urlArr = parse_url($img);
        if (!empty($urlArr['host'])){ // 一个url地址
            return $img;
        }
    
        $content = $this->getWeChatImgContent($img);
    
        if (empty($content)) { // 微信中没找到
            return $img;
        }
    
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new \Qiniu\Storage\UploadManager();
    
        $data = $uploadMgr->put($this->getUploadToken(), $img, $content);
        $domain = $this->getBucketDomain();
        
        // 手动设置命名空间别名
        \think\Loader::addNamespaceAlias('\Qiniu\Processing', '\qiNiu');
        \think\Loader::import('qiNiu.ImageUrlBuilder', EXTEND_PATH);

        return \Qiniu\thumbnail(url('/' . $data[0]['key'], '', false, $domain), 0, $width, $height, 'png');
    }
    
    
    /**
     * 上传微信mp3到七牛
     *
     * @param $mediaId
     * @return bool|array
     * @author aozhuochao
     */
    public function upWeChatMp3($mediaId, $callbackUrl, $fetchEventFunc = null)
    {
        if (empty($mediaId)) {
            return false;
        }
        $urlArr = parse_url($mediaId);
        if (!empty($urlArr['host'])){ // 是一个http地址
            return false;
        }
    
        $url = $this->getWeChatStreamUrl($mediaId);
    
        // 初始化 UploadManager 对象并进行文件的上传
        $upload = new \qiNiu\BucketManager($this->getAuth(), $this->getZone());
        $name = $this->getUniqueName($mediaId);
        list($data, $error) = $upload->fetch($url, $this->bucket, $name);
    
        if (!is_null($error) || !isset($data['key'])){
            \think\Log::error($error);
            return false;
        }
        if (!is_null($fetchEventFunc) && is_callable($fetchEventFunc)) {
            $fetchEventFunc($data);
        }
        
        $fop = new \Qiniu\Processing\PersistentFop(
            $this->getAuth(), $this->bucket, config('QINIU.persistentPipeline_mp3'),
            url($callbackUrl, 'key=' . $data['key'], false, \helper\UrlSys::getWxHost())
        );
        list($id, $error) = $fop->execute($data['key'], ['avthumb/mp3']);
    
        if (!is_null($error)) {
            \think\Log::error($error);
            return false;
        }else{
            \think\Log::record('persistentId为' . $id, 'debug');
        }
        
        return [$data, $id];
    }
    
    
    /**
     * 获取唯一名称
     *
     * @param $mediaId
     * @return string
     * @author aozhuochao
     */
    public function getUniqueName($mediaId)
    {
        $time = time();
        return hash('md5', $mediaId . $time) . $time;
    }
    
    
    
    /**
     * getWeChatImg  - -  用户头像
     *
     * @param $img
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getWeChatUserImg($img)
    {
        return $this->getWeChatImg($img, 100, 100);
    }
    
    
    
    /**
     * 获取微信临时素材
     *
     * @param $mediaId
     * @return \GuzzleHttp\Psr7\Stream|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getWeChatImgContent($mediaId, $highBool = null)
    {
        $content = '';
        try{
            /** @var \WeChat\Temporary $temporary */
            $temporary = \WeChat\app::weChatInstance()->material_temporary;
            if (!is_null($highBool)){
                $temporary->setHighBool($highBool);
            }
            // 获取内容
            /** @var \GuzzleHttp\Psr7\Stream $content */
            $content = $temporary->getStream($mediaId);
        }catch (\Exception $exception){
        }
        $content = (string)$content;
        
        if (strpos($content, '{') === 0 && is_null(json_decode($content))){ // 报错啦
            \EasyWeChat\Support\Log::debug('getWeChatImgContent error: ', [$content]);
            return '';
        }else {
            return $content;
        }
    }
    
    
    /**
     * 获取微信资源链接
     *
     * @param      $mediaId
     * @param null $highBool
     * @return string
     * @author aozhuochao
     */
    public function getWeChatStreamUrl($mediaId, $highBool = null)
    {
        if (empty($mediaId)) {
            return '';
        }
        
        /** @var \WeChat\Temporary $temporary */
        $temporary = \WeChat\app::weChatInstance()->material_temporary;
        if (!is_null($highBool)){
            $temporary->setHighBool($highBool);
        }
        
        return $temporary->getStreamUrl($mediaId);
    }
    
    
    
    /**
     * 获取七牛当前空间的域名列表
     *
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getDomainList()
    {
        if (!isset($this->domainList[$this->bucket])) {
            $this->domainList[$this->bucket] = \CacheBase::cacheData([$this->bucket], function (){
                $host = 'api.qiniu.com';
                $url = "http://{$host}/v6/domain/list?tbl=" . $this->bucket;
                $list = $this->getAuth()->authorization($url, null);
                $list += [
                    'host' => $host,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ];
                $listData = \Qiniu\Http\Client::get($url, $list);
    
                $listData = $listData->json();
                return !empty($listData) ? $listData : [];
            }, 86400);
        }
        
        return $this->domainList[$this->bucket];
    }
    
    
    /**
     * 获取当前空间的domain|host
     *
     * @return mixed
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getBucketDomain()
    {
        return current($this->getDomainList());
    }
    
    
    /**
     * 处理七牛返回的数据，并组成数据库存储的url
     *
     * @param $key
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function handleQiNiuResultUrl($key)
    {
        if (empty($key)) {
            return '';
        }
    
        if (strpos($key, '/') !== 0) {
            $key = '/' . $key;
        }
        
        return url($key, '', false, $this->getBucketDomain());
    }
    
    
    /**
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }
    
    
    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        
        return $this;
    }
    
    
    /**
     * @return Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }
    
    /**
     * @return \Qiniu\Zone
     */
    public function getZone()
    {
        if (is_null($this->zone)) {
            $this->zone = new \Qiniu\Zone();
        }
        
        return $this->zone;
    }
    
    
    /**
     * 根据upToken获取一个上传host
     *
     * @param null $token
     * @return mixed|string
     */
    public function getOneUpHostByToken($token = null)
    {
        // \Qiniu\Zone::getBucketHostsFromCache有缓存的
        $token = is_null($token) ? $this->getUploadToken() : $token;
        $arr = $this->getZone()->getUpHostByToken($token);
        if (is_array($arr)) {
            foreach ($arr as $item) {
                if (!empty($item)) {
                    return $item;
                }
            }
        }
        
        return 'http://upload.qiniu.com';
    }
    
    
    /**
     * 获取
     *
     * @see https://developer.qiniu.com/kodo/kb/1326/how-to-upload-photos-to-seven-niuyun-base64-code
     * @param int $len
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getPutB64Url($len = -1)
    {
        return $this->getOneUpHostByToken() . '/putb64/' . $len;
    }
    
}