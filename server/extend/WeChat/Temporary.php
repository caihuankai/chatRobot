<?php

namespace WeChat;


class Temporary extends \EasyWeChat\Material\Temporary
{
    
    const API_JS_SDK_GET = 'https://api.weixin.qq.com/cgi-bin/media/get/jssdk';
    
    
    protected $highBool = false;
    
    
    /**
     * 获取高清资源
     *
     * @param $mediaId
     * @return \Psr\Http\Message\StreamInterface
     * @author aozhuochao
     */
    public function getHighStream($mediaId)
    {
        $response = $this->getHttp()->get(self::API_JS_SDK_GET, ['media_id' => $mediaId]);
    
        return $response->getBody();
    }
    
    
    public function getStream($mediaId)
    {
        if ($this->highBool){
            return $this->getHighStream($mediaId);
        }
        
        return parent::getStream($mediaId);
    }
    
    
    /**
     * 获取资源链接
     *
     * @param $mediaId
     * @return string
     * @author aozhuochao
     */
    public function getStreamUrl($mediaId)
    {
        $url = self::API_GET;
        if ($this->highBool){
            $url = self::API_JS_SDK_GET;
        }
        
        return $url . '?' . 'access_token=' . $this->accessToken->getToken() . '&media_id=' . $mediaId;
    }
    
    
    /**
     * @param bool $highBool
     * @return Temporary
     */
    public function setHighBool($highBool = true)
    {
        $this->highBool = $highBool;
        
        return $this;
    }
    
    
}