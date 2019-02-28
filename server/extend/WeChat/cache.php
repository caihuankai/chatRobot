<?php

namespace WeChat;

use Doctrine\Common\Cache\Cache as CacheInterface;

class cache implements CacheInterface
{
    public function fetch($id)
    {
        return \CacheBase::get($id);
    }
    
    public function contains($id)
    {
        return \CacheBase::has($id);
    }
    
    /**
     * 更新缓存
     *
     * @param string $id
     * @param mixed  $data
     * @param int    $lifeTime
     * @return bool
     */
    public function save($id, $data, $lifeTime = 0)
    {
        if ($this->checkWeChatId($id)){ // 微信的才，更新token时更新数据库
            /** @var \app\wechat\model\AccessToken $model */
            $model = model('wechat/AccessToken');
            $model->upAccessToken($data, $lifeTime);
        }
        
        return \CacheBase::set($id, $data, $lifeTime);
    }
    public function delete($id)
    {
        return \CacheBase::rm($id);
    }
    public function getStats()
    {
        return null;
    }
    
    
    /**
     * 获取token的缓存key
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getWeChatTokenCacheKey()
    {
        static $key = '';
        if (empty($key)) {
            $key = \WeChat\app::weChatInstance()->access_token->getCacheKey();
        }
        
        return $key;
    }
    
    
    protected function checkWeChatId($id)
    {
        $key = $this->getWeChatTokenCacheKey();
        return $id === $key &&
            strpos($key, \WeChat\app::getWeChatAppId()) !== false; // 微信app_id才保存到数据库
    }
    
}