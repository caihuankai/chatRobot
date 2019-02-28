<?php

namespace app\common\model;


class QiniuGallerys extends ModelBs
{
    
    /**
     * 保存一个七牛url地址到表中
     *
     * @param     $url
     * @param     $type
     * @param int $userId
     * @return bool|int|string
     * @author aozhuochao
     */
    public function saveQiNiuUrl($url, $type, $id = 0, $userId = 0)
    {
        $url = parse_url($url);
        // 只拿path保存到key中
        if (empty($url['host']) || empty($url['path'])) {
            return false;
        }
    
        $domain = \Qiniu::instance()->getBucketDomain();
        if ($url['host'] !== $domain){
            return false;
        }
        
        if ($id){ // 更新
            $this->update([
                'qiniuKey' => trim($url['path'], '/'),
                'imgUrlDisplay' => $url,
            ], ['id' => ['eq', $id]]);
            
            return $id;
        }else{ // 插入
            return $this->insertGetId([
                'userId' => $userId,
                'qiniuKey' => trim($url['path'], '/'),
                'positionType' => $type,
                'imgUrlDisplay' => $url,
            ]);
        }
    }
    

    
    
    
    /**
     * 获取七牛url
     *
     * @param $id
     * @return string
     * @author aozhuochao
     */
    public function getQiNiuUrl($id,$flg=false)
    {
        if (empty($id)) {
            return '';
        }
        
        $key = 'qiniuKey';
        $data = $this->where(['id' => ['eq', $id]])->field($key)->find();
        if (empty($data) || empty($data[$key])) {
            return '';
        }

        if ($flg==true){
            return "http://oqt46pvmm.bkt.clouddn.com/{$data[$key]}";
        }else{
            return $this->getUrlByKey($data[$key]);
        }
    }
    
    
    /**
     * 获取key的七牛url地址
     *
     * @param $key
     * @return string
     * @author aozhuochao
     */
    public function getUrlByKey($key, $bucket = '')
    {
        return url('/' . ltrim($key, '/'), '', false, \Qiniu::instance('', '', $bucket)->getBucketDomain());
    }
    
    
    /**
     * 根据hash获取数据
     *
     * @param        $type
     * @param        $hash
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Model
     * @author aozhuochao
     */
    public function getFieldByHash($type, $hash, $field = 'id')
    {
        $type = intval($type);
        if (empty($hash)) {
            return [];
        }
        
        
        return $this->where(['hash' => $hash, 'positionType' => $type])->field($field)->find();
    }
    
    
    /**
     * @param       $id
     * @param  string $url 图片链接
     * @param  int $mode 缩略模式
     * @param  int $width 宽度
     * @param  int $height 长度
     * @param  string $format 输出类型
     * @param  int $quality 图片质量
     * @param  int $interlace 是否支持渐进显示
     * @param  int $ignoreError 忽略结果
     * @return string
     * @author aozhuochao
     */
    public function getQiNiuImg($id, ...$args)
    {
        $url = $this->getQiNiuUrl($id);
    
        if (empty($url)) {
            return '';
        }
    
        return (new \qiNiu\ImageUrlBuilder())->thumbnail($url, ...$args);
    }
    
    
    /**
     * 获取一个二维码格式的图片
     *
     * @param $id
     * @return string
     * @author aozhuochao
     */
    public function getImgForQR($id)
    {
        return $this->getQiNiuImg($id, 3, 300, 300);
    }
    
}