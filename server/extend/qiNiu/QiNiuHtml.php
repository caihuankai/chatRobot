<?php

namespace qiNiu;


class QiNiuHtml extends \qiNiu\QiNiuHtmlBase
{
    
    /**
     * 主要显示的元素id
     *
     * @var array
     */
    protected $mainEleData = [];
    
    
    protected $autoInsert = [];
    
    
    protected $upTokenUrl = '/Index/getQiNiuUploadToken';
    
    
    public function __construct()
    {
        $this->setDomain(\Qiniu::instance()->getBucketDomain());
    }
    
    
    /**
     * 获取图片上传的html，有上传和删除功能
     *
     * @return string
     * @author aozhuochao
     */
    public function getImgBeUploadAndDel($src, $emptyStr, $type = false, $attr = [])
    {
        $this->setAutoInsert($type);
        if (!empty($attr)) {
            $this->setMainEleData('attr', $attr);
        }
        
        return $this->setCurrentConfig(['max_file_size' => '10mb'])->setFilterImg()
            ->getQiNiuUploaderHtmlBeUploadAndDel($src, $emptyStr);
    }
    
    
    
    public function getVideoHtmlById($id, $type, $emptyStr = '没有视频上传', $attr = [])
    {
        /** @var \app\wechat\model\QiniuGallerys $picModel */
        $picModel = model('QiniuGallerys');
        $src = $picModel->getQiNiuUrl($id);
        
        return $this->getVideoHtml($src, $type, $emptyStr, array_merge(['data-id' => $id, $attr]));
    }
    
    
    
    public function getVideoHtml($src, $type = false, $emptyStr = '没有视频上传', $attr = [])
    {
        $this->setAutoInsert($type);
        if (!empty($attr)) {
            $this->setMainEleData('attr', $attr);
        }
    
        // 视频上传大小，产品说没有限制；格式还没有控制
        // 上传大文件到七牛不要分块上传
        return $this->setCurrentConfig(['max_file_size' => false, 'chunk_size' => false, 'max_retries' => 10])
            ->getVideoHtmlBeUploadAndDel($src, $emptyStr);
    }
    
    
    
    
    /*****我是短小的分割线******/
    
    
    
    /**
     * 获取带有上传和删除按钮的图片上传html
     *
     * @param $src
     * @param $emptyStr
     * @return string
     * @author aozhuochao
     */
    public function getVideoHtmlBeUploadAndDel($src, $emptyStr)
    {
        $i = $this->getAutoId();
        $buttonClass = 'c-blue pointer upload-a-btn';
        $rootDivId = 'admin-qi-niu-upload-div-be-upload-del-' . $i;
        $uploadButtonId = $rootDivId . '-upload-button';
        $videoId = $rootDivId . '-video';
        $rootDiv = \helper\Html::createElement('div')->id($rootDivId);
        $uploadButton = \helper\Html::createElement('span')->id($uploadButtonId)
            ->addClass($buttonClass)->text('上传');
        $delButton = \helper\Html::createElement('span')->id($rootDivId . '-del-button')->addClass($buttonClass)->text('删除');
        $video = \helper\Html::createElement('video')->id($videoId)
            ->attr(
                array_merge(['src' => $src, 'width' => '150px', 'height' => '100px', 'preload' => 'meta', 'controls' => 'controls'],
                $this->getMainEleData('attr', []))
            );
        
        if (empty($src)) {
            $emptySpan = \helper\Html::createElement('span')->id($rootDivId . '-empty-span')->text($emptyStr);
            
            $video->addClass('hide-ele');
            $delButton->addClass('hide-ele');
            
            $rootDiv->addElement($emptySpan);
        }
        
        
        $rootDiv->addElement($video);
        $rootDiv->addElement(\helper\Html::createElement('span')->addElement($uploadButton)->getTop()); // 要用元素包裹
        $rootDiv->addElement($delButton);
        
        $this->setCurrentButton($rootDivId, $uploadButtonId, $rootDivId, $videoId);
        
        $rootDiv->addElement($this->headerJs());
        
        
        $this->setJsOnFunc('FileUploaded', [$this, 'jsOnFileUploaded']);
        
        
        $rootDiv->addElement($this->getUploadAndDelJs());
        
        return $rootDiv;
    }
    
    
    
    
    
    
    
    /**
     * 获取带有上传和删除按钮的图片上传html
     *
     * @param $src
     * @param $emptyStr
     * @return string
     * @author aozhuochao
     */
    public function getQiNiuUploaderHtmlBeUploadAndDel($src, $emptyStr)
    {
        $i = $this->getAutoId();
        $buttonClass = 'c-blue pointer upload-a-btn';
        $rootDivId = 'admin-qi-niu-upload-div-be-upload-del-' . $i;
        $uploadButtonId = $rootDivId . '-upload-button';
        $imgId = $rootDivId . '-img';
        $rootDiv = \helper\Html::createElement('div')->id($rootDivId);
        $uploadButton = \helper\Html::createElement('span')->id($uploadButtonId)
            ->addClass($buttonClass)->text('上传');
        $delButton = \helper\Html::createElement('span')->id($rootDivId . '-del-button')->addClass($buttonClass)->text('删除');
        $img = \helper\Html::createElement('img')->id($imgId)
            ->attr(array_merge(['src' => $src, 'width' => '100px', 'height' => '100px'], $this->getMainEleData('attr', [])));
        
        if (empty($src)) {
            $emptySpan = \helper\Html::createElement('span')->id($rootDivId . '-empty-span')->text($emptyStr);
    
            $img->addClass('hide-ele');
            $delButton->addClass('hide-ele');
            
            $rootDiv->addElement($emptySpan);
        }
        
        
        $rootDiv->addElement($img);
        $rootDiv->addElement($uploadButton);
        $rootDiv->addElement($delButton);
    
        $this->setCurrentButton($rootDivId, $uploadButtonId, $rootDivId, $imgId);
    
        $rootDiv->addElement($this->headerJs());
    
        
        $this->setJsOnFunc('FileUploaded', [$this, 'jsOnFileUploaded']);
        
        
        $rootDiv->addElement($this->getUploadAndDelJs());
        
        return $rootDiv;
    }
    
    
    /**
     * 获取一个上传js类的名字
     *
     * @return string
     * @author aozhuochao
     */
    public function getUploadItemJsObject()
    {
        return 'uploader' . $this->getAutoId(false);
    }
    
    
    public function getUploadJs()
    {
        $i = $this->getAutoId(false);
        $config = $this->getQiNiuJsConfig();
    
        return \helper\Html::createElement('script')->text(
<<<JS
    var {$this->getUploadItemJsObject()} = (new QiniuJsSDK()).uploader({$config});
JS
        );
    }
    
    
    
    protected function getUploadAndDelJs()
    {
        $i = $this->getAutoId(false);
        $html = $this->getUploadJs();
        
        $html->text(
<<<JS
    (function (){
        var rootDiv = $(uploader{$i}.getOption('container'));
        
        // 删除按钮
        $('#' + rootDiv.attr('id') + '-del-button').click(function (){
            $('#{$this->getMainEleData('id', '')}').attr('src', '').data('id', '').data('key', '').data('size', '');
        });
    })()
JS
        );
        // 清空配置
        $this->clear();
        
        return $html;
    }
    
    
    
    public function clear()
    {
        $this->setCurrentConfig(false);
        $this->mainEleData = [];
        $this->autoInsert = [];
        
        return $this;
    }
    
    
    public function setAutoInsert($type, $hashUnique = false)
    {
        $this->autoInsert = [
            'type' => $type, // 对应数据库的positionType
            'hashUnique' => $hashUnique, // 是否hash唯一判断
        ];
        
        return $this;
    }
    
    
    public function setMainEleData($key, $data)
    {
        $this->mainEleData[$key] = $data;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getMainEleData($key, $default = [])
    {
        return isset($this->mainEleData[$key]) ? $this->mainEleData[$key] : $default;
    }
    
    
    protected function jsOnFileUploaded()
    {
        $autoInsertJs = '';
        
        if (isset($this->autoInsert['type']) && $this->autoInsert['type'] !== false) {
            $autoInsertUrl = url('/Index/uploadQiNiuUrl', '', '', true);
            $hashUnique = isset($this->autoInsert['hashUnique']) ? intval($this->autoInsert['hashUnique']) : 0;
            
            $autoInsertJs = <<<JS
(function (res, mainEle){
    var postData = {
        hash: res.hash,
        key: res.key,
        size: mainEle.data('size'),
        hashUnique: {$hashUnique},
        type: {$this->autoInsert['type']}
    };
    
    requestAjax(postData, {
        url: '{$autoInsertUrl}',
        localSuccess: function (data){
            mainEle.data('id', data['id']);
            
            if(postData.hashUnique) { // 可能会改变key和url
                mainEle.data('key', data['key']);
                mainEle.attr('src', data['url']);
             }
            
            layer.close(load);
        }
    }, true);
})(res, mainEle);
JS;

        }
        
        return <<<JS
            (function (){
                return function(up, file, info) {
                    // 每个文件上传成功后，处理相关的事情
                    // 其中info.response是文件上传成功后，服务端返回的json，形式如：
                    // {
                    //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                    //    "key": "gogopher.jpg"
                    //  }
                    // 查看简单反馈
                    var domain = up.getOption('domain'),
                        container = up.getOption('container'),
                        res = JSON.parse(info.response);
                    
                    var sourceLink = 'http://' + domain +"/"+ res.key; //获取上传成功后的文件的Url
                    
                    var mainEle = $(container).find('#{$this->getMainEleData('id', '')}').attr('src', sourceLink).show();
                    
                    mainEle.data('key', res.key);
                    mainEle.data('size', file.size);
                    
                    $('#{$this->currentConfig['container']}-empty-span').hide(); // 空提示文案隐藏
                    $('#{$this->currentConfig['container']}-del-button').show(); // 删除按钮显示
                    

                    
                    {$autoInsertJs}
                    layer.close(load);
                };
            })()
JS;

    }
    

    
    
    
    /**
     * @return string|\helper\Html
     * @author aozhuochao
     */
    public function headerJs()
    {
        static $output = true;
        $scriptRootDiv = \helper\Html::createElement('div');
        // 只会输出一次
        $scriptRootDiv->setToStringCallback(function ()use(&$output){
            if ($output){
                $output = !$output;
                return true;
            }else{
                return $output;
            }
        });
        $scriptRootDiv->addElement(\helper\Html::createElement('script')->attr('src', '/lib/plupload-2.3.1/js/moxie.min.js'));
        $scriptRootDiv->addElement(\helper\Html::createElement('script')->attr('src', '/lib/plupload-2.3.1/js/plupload.full.min.js'));
        $scriptRootDiv->addElement(\helper\Html::createElement('script')->attr('src', '/lib/qi-niu-sdk-1.0.22/dist/qiniu.min.js'));
        
        // 公共js
        $headerJsScript = $this->headerJsScript();
        if (!empty($headerJsScript)) {
            $scriptRootDiv->addElement(\helper\Html::createElement('script')->text($headerJsScript));
        }
        
        return $scriptRootDiv;
    }
    
    
    public function setCurrentButton($container, $browseButton, $dropElement, $mainEleId = '')
    {
        $this->setMainEleData('id', $mainEleId);
        parent::setCurrentButton($container, $browseButton, $dropElement);
        
        return $this;
    }
    
    
    protected function headerJsScript()
    {
        return '';
    }
    
    /**
     * 获取上传apk时的html
     * @param unknown $src
     * @param string $type
     * @param string $emptyStr
     * @param array $attr
     * @return \HtmlGenerator\Markup|\helper\Html
     * @author liujuneng
     */
    public function getAndroidApkUploadHtml($src, $type = false, $emptyStr = '请选择文件', $attr = [])
    {
    	$this->setAutoInsert($type);
    	if (!empty($attr)) {
    		$this->setMainEleData('attr', $attr);
    	}
    	
    	// 视频上传大小，产品说没有限制；格式还没有控制
    	return $this->setCurrentConfig(['max_file_size' => false, 'chunk_size' => '10mb', 'max_retries' => 10])
    	->getAndroidApkHtmlBeUpload($src, $emptyStr);
    }
    
    public function getAndroidApkHtmlBeUpload($src, $emptyStr)
    {
    	$i = $this->getAutoId();
    	$buttonClass = 'c-blue pointer upload-a-btn';
    	$rootDivId = 'admin-qi-niu-upload-div-be-upload-del-' . $i;
    	$uploadButtonId = $rootDivId . '-upload-button';
    	$rootDiv = \helper\Html::createElement('div')->id($rootDivId);
    	$uploadButton = \helper\Html::createElement('span')->id($uploadButtonId)
    	->addClass($buttonClass)->text('上传');    	
    	
    	if (empty($src)) {
    		$emptySpan = \helper\Html::createElement('span')->id($rootDivId . '-empty-span')->addClass('showfileUrl')->text($emptyStr);
    		
    		
    		$rootDiv->addElement($emptySpan);
    	}
    	
    	
    	$rootDiv->addElement(\helper\Html::createElement('span')->addElement($uploadButton)->getTop()); // 要用元素包裹
    	
    	$this->setCurrentButton($rootDivId, $uploadButtonId, $rootDivId);
    	
    	$rootDiv->addElement($this->headerJs());
    	
    	
    	$this->setJsOnFunc('FileUploaded', [$this, 'jsOnFileUploadedForAndroidApk']);
    	
    	
    	$rootDiv->addElement($this->getUploadJs());
    	
    	return $rootDiv;
    }
    
    protected function jsOnFileUploadedForAndroidApk()
    {
    	
    	return <<<JS
            (function (){
                return function(up, file, info) {
                    // 每个文件上传成功后，处理相关的事情
                    // 其中info.response是文件上传成功后，服务端返回的json，形式如：
                    // {
                    //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                    //    "key": "gogopher.jpg"
                    //  }
                    // 查看简单反馈
                    var domain = up.getOption('domain'),
                        res = JSON.parse(info.response);
                        
                    var sourceLink = 'http://' + domain +"/"+ res.key; //获取上传成功后的文件的Url
                    
                    $('#{$this->currentConfig['container']}-empty-span').attr('data-link', sourceLink).attr('data-key', res.key).html(file.name);
                    
                };
            })()
JS;
                    
    }
    
    
    
}