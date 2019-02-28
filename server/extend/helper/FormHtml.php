<?php

namespace helper;


/**
 * 系统form表单
 *
 * @package helper
 */
class FormHtml
{
    use \traits\Singleton;
    
    protected $form = null;
    
    protected $formOption = [];
    
    public function getForm($action = '#')
    {
        if (is_null($this->form)) {
            $this->form = \helper\Html::createElement('form')->set([
                'action' => $action, 'method' => 'POST', 'id' => $this->getFormId(),
            ])->set($this->getFormOption());
        }
        
        return $this->form;
    }
    
    
    public function addChildren($type, $label = '', $value = '')
    {
        $form = $this->getForm();
        $labelHtml = \helper\Html::createElement('label')->addClass('form-label col-xs-4 col-sm-3')->text($label);
        $divHtml = \helper\Html::createElement('div')->addClass('formControls col-xs-8 col-sm-9');
        $rowDiv = \helper\Html::createElement('div')->addClass('row cl');
        $childrenHtml = null;
        $type = $this->handleAddChildrenType($type);
        $localId = $this->handleAddChildrenType($type, 'id');
        
        switch ($type['type']){
            case 'text':
                $childrenHtml = $this->getIdElement($localId)->tag('span')->text($value);
                break;
            case 'input':
                $childrenHtml = $this->getIdElement($localId)->tag('input')->setNameEqId()->addClass('input-text')->set('type', 'text')->set('value', $value);
                $small = $this->handleAddChildrenType($type, 'small');
    
                if (!empty($small)){
                    $divHtml->addElement(\helper\Html::createElement('p')->text($small));
                }
                break;
            case 'hidden':
                $rowDiv->setDisplay();
                $childrenHtml = $this->getIdElement($localId)->tag('input')->setNameEqId()->set(['type' => 'hidden', 'value' => $value]);
                break;
            case 'upload-pic': // 需要在控制中处理文件加载， uploaderToQiNiu全局变量
                // todo 待改为\qiNiu\QiNiuHtml实现
                $parentId = $prefixId = $this->getAutoId($localId);
                $childrenHtml = \helper\Html::createElement('div');
                $childrenHtml->addElement(\helper\Html::createElement('p')->text("（图片不能大于1024KB）"));
                // 图片
                $imgId = $prefixId . '-img';
                $picDivHtml = \helper\Html::createElement('div')->addElement(
                    \helper\Html::createElement('img')->id($imgId)->set(['width' => '100px', 'height' => '100px', 'src' => $value])
                )->getTop();
                $childrenHtml->addElement($picDivHtml);
                // 上传按钮
                $buttonId = $prefixId . '-button';
                $childrenHtml->addElement(\helper\Html::createElement('div')->id($parentId)->addElement(
                \helper\Html::createElement('div')->addClass('btn btn-primary')->id($buttonId)->text('上传图片')
                )->getTop());
                // 上传js todo
                $domain = url('/Index/getQiNiuUploadToken', '', false, true); // 这是后台
                $bucketDomain = \Qiniu::instance()->getBucketDomain();
                $script = <<<SCRIPT
                $(function (){
                    var picUploader = Qiniu.uploader({
                        runtimes: 'html5,flash,html4',    //上传模式,依次退化
                        domain: 'http://{$bucketDomain}',   //bucket 域名，下载资源时用到，**必需**
                        get_new_uptoken: false,  //设置上传文件的时候是否每次都重新获取新的token
                        container: '{$parentId}',           //上传区域DOM ID，默认是browser_button的父元素，
                        max_file_size: '100mb',           //最大文件体积限制
                        max_retries: 3,                   //上传失败最大重试次数
                        dragdrop: true,                   //开启可拖曳上传
                        drop_element: '{$imgId}',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
                        chunk_size: '4mb',                //分块上传时，每片的体积
                        
                        browse_button: "{$buttonId}", //上传选择的点选按钮，**必需**
                        uptoken_url: '{$domain}',
                        flash_swf_url: 'https://cdn.staticfile.org/plupload/2.1.9/Moxie.swf',  //引入flash,相对路径
                        auto_start: true,
                        init: {
                              'FilesAdded': function(up, files) {
                                  plupload.each(files, function(file) {
                                      // 文件添加进队列后，处理相关的事情
                                  });
                              },
                              'BeforeUpload': function(up, file) {
                                     // 每个文件上传前，处理相关的事情
                              },
                              'UploadProgress': function(up, file) {
                                     // 每个文件上传时，处理相关的事情
                              },
                              'FileUploaded': function(up, file, info) {
                                    console.log(arguments);
                                    // 每个文件上传成功后，处理相关的事情
                                    // 其中info是文件上传成功后，服务端返回的json，形式如：
                                    // {
                                    //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                                    //    "key": "gogopher.jpg"
                                    //  }
                                    // 查看简单反馈
                                    var domain = up.getOption('domain');
                                    var res = JSON.parse(info);
                                    var sourceLink = domain +"/"+ res.key; //获取上传成功后的文件的Url
                                    $('#{$imgId}').attr('src', sourceLink);
                                    console.log([sourceLink, res]);
                              },
                              'Error': function(up, err, errTip) {
                                     //上传出错时，处理相关的事情
                              },
                              'UploadComplete': function() {
                                     //队列文件处理完毕后，处理相关的事情
                              },
                              'Key': function(up, file) {
                                  // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                                  // 该配置必须要在unique_names: false，save_key: false时才生效
                                  if (file.hash){
                                      return file.hash;
                                  }
                              }
                          }
                    });
                
                
                });
SCRIPT;
                $childrenHtml->addElement(\helper\Html::createElement('script')->text($script));
                               
                break;
            case 'radio':
                $data = $this->handleAddChildrenType($type, 'data');
                $name = $this->handleAddChildrenType($type, 'name')?:$this->getRadioId($localId); // name
                if (!empty($data) && is_array($data)) {
                    $childrenHtml = $this->getIdElement($localId);
                    foreach ($data as $key => $item) {
                        $radioBoxHtml = \helper\Html::createElement('div')->addClass('radio-box');
                        $id = $this->getRadioId() . '-radio';
                        
                        // radio
                        /** @var \helper\Html $radioHtml */
                        $radioHtml = $this->getIdElement($id)->tag('input')->set('type', 'radio')
                            ->set('name', $name . '[]')->set('value', $key);
                        if ($key == $value){ // 默认值
                            $radioHtml->set('checked', true);
                        }
                        
                        // label
                        $radioBoxHtml->addElement($this->getIdElement()->tag('label')->set('for', $id)->text($item));
    
                        // input-radio
                        $radioBoxHtml->addElement($radioHtml);
                        $radioBoxHtml->addElement(\helper\Html::space());
                        $childrenHtml->addElement($radioBoxHtml);
                    }
                    
                }
                break;
            case 'submit':
                $labelHtml = null;
                $text = $this->handleAddChildrenType('text')?:'&nbsp;&nbsp;提交&nbsp;&nbsp;';
                $childrenHtml = $this->getIdElement()->tag('input')->addClass('btn btn-primary')->set('type', 'submit')->text($text);
                $divHtml->set('class', 'formControls col-xs-offset-4 col-sm-offset-3');
                break;
                
            default:
                break;
        }
    
        !is_null($childrenHtml) && $divHtml->addElement($childrenHtml, true);
        !is_null($labelHtml) && $rowDiv->addElement($labelHtml);
        !is_null($divHtml) && $rowDiv->addElement($divHtml);
        $form->addElement($rowDiv);
        
        
        return $this;
    }
    
    
    
    
    
    
    /** protected */
    

    
    
    /**
     * 处理addChildren的type参数
     *
     * @param        $type
     * @param string $key 获取指定的key
     * @return array|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function handleAddChildrenType($type, $key = '')
    {
        $result = ['type' => '', 'options' => []];
        if (is_array($type)) {
            $result = array_merge($result, $type);
        }else if (is_string($type)){
            $result['type'] = $type;
        }
        
        if (!empty($key)){ // 获取指定的key
            return isset($result[$key]) ? $result[$key] : (isset($result['options'][$key])?$result['options'][$key]:'');
        }
        
        return $result;
    }
    
    
    
    protected function getFormId()
    {
        return 'form';
    }
    
    
    /**
     *
     *
     * @return \helper\Html
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getIdElement($id = null)
    {
        $ele = \helper\Html::createElement();
        $ele->id($this->getAutoId($id));
        
        return $ele;
    }
    
    /**
     * 获取form的子元素的id，每调用一次就自增
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getAutoId($id = null, $joinKey = '-children-', $idPrefix = '')
    {
        return $this->getFormId() . $joinKey . (!empty($id) && $id !== 0 ?$id: \helper\TempData::instance()->getAutoInc([__CLASS__, $idPrefix]));
    }
    
    
    /**
     * 获取自增的radio的id
     *
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function getRadioId($id = null)
    {
        return $this->getAutoId($id, '-children-radio-', 'radio');
    }
    
    
    
    /** __get __set */
    
    
    /**
     * @return array
     */
    public function getFormOption()
    {
        return $this->formOption;
    }
    
    /**
     * @param array $formOption
     * @return $this
     */
    public function setFormOption($formOption)
    {
        $this->formOption = $formOption;
        
        return $this;
    }
    
    

    
}