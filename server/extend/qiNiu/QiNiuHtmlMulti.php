<?php

namespace qiNiu;


class QiNiuHtmlMulti extends \qiNiu\QiNiuHtml
{
    
    protected $uploadDivClass = 'pointer upload-a-btn';
    
    /**
     * 最大上传数
     *
     * @var int
     */
    protected $maxUploadNum = 0;
    
    
    protected $upTokenUrl = '/Index/getQiNiuUploadToken/bucket/'; // 在构造函数中拼接
    
    
    public function __construct()
    {
        parent::__construct();
        $bucket = \think\Config::get('QINIU.LIVE_BUCKET');
        $this->upTokenUrl .= $bucket;
        $this->setDomain(\Qiniu::instance('', '', $bucket)->getBucketDomain());
    }
    
    
    protected function getRootDivId()
    {
        static $id = '';
    
        if (empty($id)) {
            $id = 'admin-qi-niu-upload-multi-div-' . $this->getAutoId();
        }
        
        return $id;
    }
    
    
    protected function getImgParentDivIdName()
    {
        return $this->getRootDivId() . '-img-parent-div';
    }
    
    
    public function getUploadMultiImg(array $data, $maxNum)
    {
        return $this->setFilterImg()->getUploadMulti($data, $maxNum);
    }
    
    
    /**
     * 获取带有上传和删除按钮的图片上传html
     *
     * @param $src
     * @param $emptyStr
     * @return string
     * @author aozhuochao
     */
    public function getUploadMulti(array $data, $maxNum, $mainNodeName = 'img')
    {
        $this->setCurrentConfig(['multi_selection' => true, 'get_new_uptoken' => true]); // 允许一次选择多个
        $this->maxUploadNum = $maxNum;
        
        $uploadDivClass = $this->uploadDivClass;
        $rootDivId = $this->getRootDivId();
        $uploadDivId = $rootDivId . '-upload-div';
        $uploadParentId = $uploadDivId . '-parent-div';
        $size = $this->getCommonSize();
        
        $rootDiv = \helper\Html::createElement('div')->id($rootDivId)->attr([
            'data-main-node-name' => $mainNodeName, 'data-max-sort' => 0
        ]);
        $uploadParentButton = \helper\Html::createElement('div')->id($uploadParentId)->addClass('l');
        $uploadButton = \helper\Html::createElement('div')->id($uploadDivId)
            ->addClass($uploadDivClass . ' text-c l')
            ->setStyle($size + ['border' => '1px dashed', 'line-height' => $size['height']])->text('+');
        $uploadParentButton->addElement($uploadButton);
    
        $rootDiv->addElement(\helper\Html::createElement('div')->id($this->getImgParentDivIdName())->addClass('l'));
        $rootDiv->addElement($uploadParentButton);
        
        $this->setCurrentButton($uploadParentId, $uploadDivId, $rootDivId);
    
        $rootDiv->addElement($this->headerJs());
        
        
        $this->setJsOnFunc('FileUploaded', [$this, 'jsOnFileUploaded']);
        
        
        $rootDiv->addElement($this->getUploadJs());
    
        
        // 初始化
        $headerScript = $this->headerScript();
        // js设置data
        $jsonData = json_encode($data);
        $headerScriptHtml = \helper\Html::createElement('script')->text(<<<JS
{$headerScript}
$(function (){
    $('.moxie-shim.moxie-shim-html5').hide(); // 隐藏未知div
    
    var data = {$jsonData};
    {$this->getUploadItemJsObject()}.addUploadMulti(data);
});
JS
        );
    
    
        $rootDiv->addElement($headerScriptHtml);
        
        $this->clear();
        
        return $rootDiv;
    }
    
    protected function jsOnFileUploaded()
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
                    
                    var domain = up.getOption('domain'),
                        container = up.getOption('container'),
                        res = JSON.parse(info.response);
                    
                    var sourceLink = 'http://' + domain +"/"+ res.key; //获取上传成功后的文件的Url
                    
                    {$this->getUploadItemJsObject()}.addUploadMulti([
                        {
                            src: sourceLink,
                            key: res.key,
                            hash: res.hash
                        }
                    ]);
                    
                    
                    layer.close(load);
                };
            })()
JS;
    }
    
    
    /**
     * 增加一个显示内容的js方法
     *
     * @return string
     * @author aozhuochao
     */
    protected function headerScript()
    {
        $rootId = $this->getRootDivId();
        $imgParentDiv = $this->getImgParentDivIdName();
        $size = json_encode($this->getCommonSize());
        
        // 会出现模板方法存在多个定义的方法
        return <<<JS
{$this->getUploadItemJsObject()}.addUploadMulti = function (data){
    var html = '', main = sort = '',
        rootId = '{$rootId}',
        rootDiv = $('#' + rootId),
        mainNodeName = rootDiv.data('main-node-name'),
        imgParentDiv = $('#{$imgParentDiv}'),
        classId = rootId + '-item-div',
        maxNum = {$this->maxUploadNum},
        size = {$size};
    
    if (maxNum && maxNum <= $('.' + classId).length){ // 如果限制上传个数就限制
        return;
    }
    
    for (var i in data){
        if (data.hasOwnProperty(i)){
            
            html = $(document.createElement('div')).addClass(classId + ' pos-r l {$this->uploadDivClass}').css(size);
            // 关闭按钮
            html.append(
                $(document.createElement('div')).addClass('pos-a right bottom min-btn f-16').text('X').click(function (){
                    $(this).parent().remove();
                })
            );
            if (data[i]['sort']){
                sort = data[i]['sort'];
            }else{
                sort = 0;
            }
            
            // 排序input
            html.append( // sort
                $(document.createElement('div')).addClass(classId + '-sort pos-a left top none-sort').html(
                    $(document.createElement('input')).attr('type', 'number').val(sort).css('width', '50px').data('old-val', sort)
                    .change((function (){
                        var tempBool = false; // 防止递归
                        return function (){
                            var e = $(this),
                                val = e.val();
                            
                            if (tempBool){
                                tempBool = false;
                                return;
                            }
                            tempBool = false;
                            
                            if (val < 0){
                                tempBool = true;
                                e.val(e.data('old-val'));
                                layer.msg('必须大于0');
                            }else if(val > 99) {
                                tempBool = true;
                                e.val(e.data('old-val'));
                                layer.msg('最大不能超过99');
                            }else{
                                e.data('old-val', val);
                            }
                            
                        }
                    })())
                )
            );
            
            
            // 主元素
            main = $(document.createElement(mainNodeName)).addClass(classId + '-main none-main');
            
            main.attr({'src': data[i]['src'], 'data-key': data[i]['key'], 'data-hash': data[i]['hash'], width: '80%', height: '80%'});
            main.css({'margin': '10%'});
            
            html.append(main);
            
            imgParentDiv.append(html);
        }
    }
    
}
JS;

    }
    
    
    
    public function getCommonSize()
    {
        return ['width' => '120px', 'height' => '120px'];
    }
    
    
}