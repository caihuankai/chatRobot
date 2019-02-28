<?php

namespace app\common\model;


class LiveCate extends \app\common\model\ModelBs
{
    
    protected $allData = [];
    
    /**
     * 获取所有分类，key为id
     *
     * @return false|\PDOStatement|string|\think\Collection
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getAllCateData($status = 1, $type = 1)
    {
        if (empty($this->allData)) {
            $where = [];
            if (!empty($status)) {
                $where['status'] = $status;
            }
            if (!empty($type)) {
            	$where['type'] = $type;
            }
            $this->allData = $this->where($where)->column(true, 'id');
        }
        
        return $this->allData;
    }
    
    
    /**
     * 获取第一个一级分类
     *
     * @param     $field
     * @param int $type
     * @return array|false|\PDOStatement|string|\think\Model
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getFirstFloorCate($field, $type = 1)
    {
        $where = ['pid' => 0, 'type' => $type, 'status' => 1];
        
        return $this->where($where)->field($field)->order('sort asc')->find();
    }
    
    
    
    /**
     * 获取分类名称
     *
     * @param $id
     * @return string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCateName($id)
    {
        if (empty($id)) { // 为空时返回空
            return '';
        }
        
        $data = $this->getAllCateData();
    
        return !empty($data[$id]) ? $data[$id]['name'] : '';
    }
    
    
    /**
     * 获取所有分类
     *
     * @return array|string
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getAllCate($htmlBool = false, $selected = 0, $status = 0, $type = 1)
    {
    	$data = $this->getAllCateData($status, $type);
        $result = [];
        
        if ($htmlBool){
            
            $html = $selectedAttr = '';
            // 返回只有一层的
            foreach ($data as $item) {
                if (empty($item['pid'])) {
                    $disabled = 'disabled';
                } else {
                    if ($selected && $selected == $item['id']){
                        $selectedAttr = 'selected';
                    }
                    $disabled = '';
                }
                $html .= "<option {$selectedAttr} {$disabled} value='{$item['id']}'></option>";
            }
            
            return $html;
        }else{
            
            foreach ($data as $item) {
                if (empty($item['pid'])){
                    $result[$item['id']] = $item;
                }else{
                    $result[$item['pid']]['children'][$item['id']] = $item;
                }
            }
            
            return $result;
        }
    }
    
    
    /**
     * 根据id和pid检测分类是否存在
     *
     * @param     $id
     * @param int $pid
     * @param int $status
     * @return bool
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function checkCate($id, $pid = 0, $status = 1)
    {
        if (empty($id)) {
            return false;
        }
        $where = ['id' => $id, 'pid' => $pid];
    
        if (!empty($status)) {
            $where['status'] = $status;
        }
        
        return !!$this->where($where)->field('id')->find();
    }
    
    
    /**
     * 获取课程分类
     *
     * @param     $pid
     * @param     $field
     * @param int $status
     * @return array|false|\PDOStatement|string|\think\Collection
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getCourseCate($pid, $field = 'id', $status = 1)
    {
    
        if (empty($pid)) {
            return [];
        }
    
        $where = ['pid'=>$pid, 'type'=>1];
    
        if (!empty($status)) {
            $where['status'] = $status;
        }
        
        return $this->where($where)->field($field)->order('sort asc')->fetchClass('\CollectionBase')->select()->column(null, 'id');
    }
    
    /**
     * 获取一级分类
     * @param unknown $pageNo
     * @param number $perPage
     * @param string $field
     * @param string $sortBy
     * @param number $status
     * @return \think\Collection|\think\db\false|PDOStatement|string
     * @author liujuneng
     */
    public function getFirstCateData($pageNo, $perPage = 20, $field = 'id', $orderBy = 'sort asc', $status = 1, $type = 1)
    {
    	if (!empty($status)) {
    		$where['status'] = $status;
    	}
    	if (!empty($type)) {
    		$where['type'] = $type;
    	}
    	
    	$data = $this->where($where)
    	->where('pid', 0)
    	->field($field)
    	->order($orderBy)
    	->fetchClass('\CollectionBase')
    	->page($pageNo, $perPage)
    	->select()
    	->column(null, 'id');
    	
    	return $data;
    }
    
    
}