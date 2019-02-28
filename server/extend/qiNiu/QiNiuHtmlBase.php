<?php

namespace qiNiu;


/**
 * 七牛上传类
 *
 * @package qiNiu
 */
class QiNiuHtmlBase
{
    
    use \traits\Singleton;
    
    
    protected $currentConfig = [];
    
    
    protected $jsOnFuncArr = [];
    
    
    
    protected $domain = null;
    
    
    
    protected $upTokenUrl = '';
    
    
    /**
     * @param string $container 上传区域DOM ID，默认是browser_button的父元素
     * @param string|string[] $browseButton 上传选择的点选按钮，如果是数组就是指定多个
     * @param string $dropElement 拖曳上传区域元素的ID
     * @return $this
     */
    public function setCurrentButton($container, $browseButton, $dropElement)
    {
        $this->currentConfig['container'] = $container;
        $this->currentConfig['browse_button'] = $browseButton;
        $this->currentConfig['drop_element'] = $dropElement;
        
        return $this;
    }
    
    
    /**
     * 设置必须为图片格式
     *
     * @return $this
     */
    public function setFilterImg()
    {
        $this->setCurrentConfig([
            'filters' => [
                'mime_types' => [
                    ['title' => 'Image files', 'extensions' => 'jpg,jpeg,gif,png',],
                ]
            ]
        ]);
        
        return $this;
    }
    
    
    public function getJsOnFunc($onName)
    {
        if (!isset($this->jsOnFuncArr[$onName])) {
            return 'function(){}';
        }
        
        return is_string($this->jsOnFuncArr[$onName])?
            $this->jsOnFuncArr[$onName]:
            call_user_func($this->jsOnFuncArr[$onName]);
    }
    
    
    /**
     * 设置js事件
     *
     * @param          $onName
     * @param callable $callback
     * @return $this
     */
    public function setJsOnFunc($onName, callable $callback)
    {
        $this->jsOnFuncArr[$onName] = $callback;
        
        return $this;
    }
    
    
    
    /**
     * @return array
     */
    public function getCurrentConfig()
    {
        return json_encode($this->currentConfig);
    }
    
    
    
    /**
     * @param array|false $currentConfig
     * @return $this
     */
    public function setCurrentConfig($currentConfig)
    {
        if ($currentConfig === false){ // 清空
            $this->currentConfig = [];
        }else{
            $this->currentConfig += $currentConfig;
        }
    
        return $this;
    }
    
    
    /**
     * 获取自增id
     *
     * @param bool|string $auto
     * @return int|string
     */
    protected function getAutoId($auto = true)
    {
        static $i = 0;
        if ($auto === true){
            ++$i;
        }else if($auto !== false){
            $i = $auto;
        }
     
        return $i;
    }
    
    
    
    /**
     * 获取js的七牛配置对象
     *
     * @return string
     * @author aozhuochao
     */
    public function getQiNiuJsConfig()
    {
        $config = $this->getCurrentConfig();
        $domain = $this->getDomain();
        return /** @lang JavaScript */
            <<<JS
(function (){
    var load = 0;
    
    return $.extend({
        runtimes: 'html5,flash,html4',    //上传模式,依次退化
        domain: '{$domain}',   //bucket 域名，下载资源时用到，**必需**
        get_new_uptoken: false,  //设置上传文件的时候是否每次都重新获取新的token，true可用于一个页面多个域上传
        
        container: '',           //上传区域DOM ID，默认是browser_button的父元素，
        browse_button: "", //上传选择的点选按钮，**必需**
        drop_element: '',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
        
        multi_selection: false,           // 一次选择多个上传
        max_file_size: '100mb',           //最大文件体积限制
        max_retries: 3,                   //上传失败最大重试次数
        dragdrop: true,                   //开启可拖曳上传
        chunk_size: '4mb',                //分块上传时，每片的体积
        
        uptoken_url: '{$this->upTokenUrl}',
        flash_swf_url: '/lib/plupload-2.3.1/js/Moxie.swf',  //引入flash,相对路径
        auto_start: true,
          //x_vars : {
          //    查看自定义变量
          //    'time' : function(up,file) {
          //        var time = (new Date()).getTime();
          // do something with 'time'
          //        return time;
          //    },
          //    'size' : function(up,file) {
          //        var size = file.size;
          // do something with 'size'
          //        return size;
          //    }
          //},
          filters:{
            mime_types:[
                // {title : "Image files", extensions : "jpg,jpeg,gif,png"} // 限制图片格式
            ]
          },
        init: {
            FilesAdded: function(up, files) {
              plupload.each(files, function(file) {
                // 文件添加进队列后，处理相关的事情
              });
            },
            BeforeUpload: function(up, file) {
              // 每个文件上传前，处理相关的事情
              load = layer.load(1); // 等太久就关闭，否则无法操作界面
            },
            UploadProgress: function(up, file) {
              // 每个文件上传时，处理相关的事情
            },
            FileUploaded: {$this->getJsOnFunc('FileUploaded')},
            Error: function(up, err, errTip) {
              layer.close(load);
              //上传出错时，处理相关的事情
              if (err && err.code == -600){ // 文件过大
                  layer.msg('上传文件过大，限制：' + up.getOption('max_file_size'));
              }else if (err && err.code == -601){
                  layer.msg('上传文件格式不正确');
              }else{
                  layer.msg(errTip);
              }
            },
            UploadComplete: function() {
              //队列文件处理完毕后，处理相关的事情
              layer.close(load);
            },
            Key: function(up, file) {
              // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
              // 该配置必须要在unique_names: false，save_key: false时才生效
              
                var arr = file.name.split('.').splice(-1);
                
                if (arr[0]){
                    return file.id + '.' + arr[0];
                }
            }
        }
    }, {$config});
})()
JS;
    
    }
    
    
    
    
    /**
     * @return null
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    /**
     * @param null $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
    
}