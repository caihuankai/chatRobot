<?php

/**
 * 自定义系统标签
 */
class SysCx extends \think\template\TagLib
{
    
    /**
     * 定义标签
     *
     * @var array
     */
    protected $tags = [
        'option' => ['attr' => 'data,selected,notHeader', 'close' => 0],
        'replace_html' => ['attr' => 'id,replace', 'close' => 0],
        'date_range' => ['attr' => 'title', 'close' => 0], // <{date_range title=""}> 必须要有title
        'checkbox' => ['attr' => 'data,name',  'close' => 0],
    ];
    
    
    /**
     * 输出option
     *
     * <code>
     *      <{option data="$statusArr"}>
     *      <{option data="$statusArr" notHeader="true"}>
     * </code>
     *
     * @param $tag
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function tagOption($tag)
    {
        $html = $selectedHtml = '';
        if (!empty($tag['data'])) {
            
            !isset($tag['notHeader']) && ($html .= '<option value="">请选择</option>');
            
            $html .= '<?php ';
            $data = $this->autoBuildVar($tag['data']);
            $selected = $this->autoBuildVar($tag['selected']); // 默认选择
            $disabledList = $this->autoBuildVar($tag['disabledList']); // 禁用
            $disabledList = !empty($disabledList) ? $disabledList : '[]';
            
            
            $html .= "if (!empty({$data})){";
            $html .= "foreach ({$data} as \$key => \$item){";
            $selectedHtml .= '".(!empty('.$disabledList.') && in_array($key, (array)'.$disabledList.')?"disabled":"")."';
            $selected && ($selectedHtml .= '".(!empty('.$selected.') && $key == '.$selected.'?"selected":"")."');
            
            $html .= 'echo "<option '.$selectedHtml.' value=\'{$key}\'>{$item}</option>";';
            $html .= '}';
            $html .= '}';
            
            $html .= ' ?>';
        }
        
        return $html;
    }
    
    
    /**
     * 组织foreach的html代码
     *
     * @param $data
     * @param $itemHtml
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function htmlForeach($data, $itemHtml)
    {
        $data = $this->autoBuildVar($data);
        $html = '<?php ';
        $html .= "if (!empty({$data})) { ";
        $html .= "foreach ({$data} as \$key => \$item) { ";
        $html .= ' ?>';
        $html .= $itemHtml;
        $html .= "<?php ";
        $html .= " }";
        $html .= ' }';
        $html .= ' ?>';
        
        return $html;
    }
    
    
    /**
     * 需要输出的数据
     *
     * @param $data
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    protected function echoHtml($data, $quotes = true)
    {
        return $quotes?"<?php echo '{$data}';?>":"<?php echo {$data};?>";
    }
    
    
    /**
     * 多选框
     *
     * @param $tag
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function tagCheckbox($tag)
    {
        $html = '';
        $id = isset($tag['name']) ? $tag['name'] : '';
        $name = !empty($id) ? $id . '[]' : '';
        
        
        if (!empty($tag['data'])) {
            $itemHtml = "<span><label><input type='checkbox' name='{$name}' class='{$id}' value='".
                $this->echoHtml('$key', false)
                ."' />".
                $this->echoHtml('$item', false).
                "</label>&emsp;</span>";
            $html = $this->htmlForeach($tag['data'], $itemHtml);
        }
        
        return $html;
    }
    
    
    
    
    /**
     * 将一个id写入到当前文档中
     *
     * @param $tag
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function tagReplace_html($tag)
    {
        $html = '';
        if (!empty($tag['id'])){
            $id = '<?php echo ' . $this->autoBuildVar($tag['id']) . '?>';
            $replaceId = $tag['replace'];
            $html .= <<<HTML
<style>
#{$id}{
    display: none;
}
</style>
<script>
    $(function (){
        $('#{$replaceId}').html($('#{$id}').html())
    });
</script>
HTML;

        }
        
        return $html;
    }
    
    
    /**
     * 时间范围搜索
     *
     * @param $tag
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function tagDate_range($tag)
    {
        $title = isset($tag['title']) ? $tag['title'] : '';
        $html = <<<HTML
    <input type="text" onfocus="WdatePicker({maxDate:'#F{\$dp.\$D(\'search-dateMax\')||\'%y-%M-%d\'}'})" id="search-dateMin" placeholder="{$title}开始时间"
           class="input-text Wdate" style="width:120px;">-
    <input type="text" onfocus="WdatePicker({minDate:'#F{\$dp.\$D(\'search-dateMin\')}'})" id="search-dateMax" placeholder="{$title}结束时间"
           class="input-text Wdate" style="width:120px;">
HTML;
        
        
        return $html;
    }
    
    
    
}