<?php

namespace app\common\model;


use think\Db;

class LiveImg extends ModelBs
{
    
    /**
     * 获取图片列表
     *
     * @param     $typeId
     * @param int $type
     * @return array|false|\PDOStatement|string|\think\Collection
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function getImgList($typeId, $type = 1)
    {
        if (empty($typeId)) {
            return [];
        }
        
        return $this->where(['type_id' => $typeId, 'type' => $type])->field('id, type_id, src, explain')->select();
    }
    
    
    
    /**
     * 保存图片
     *
     * @param       $typeId
     * @param array $data
     * @param int   $type
     * @return bool
     * @author aozhuochao<aozhuochao@99cj.com.cn>
     */
    public function saveImg($typeId, array $data = [], $type = 1)
    {
        $bool = false;
        if (empty($typeId)) {
            return $bool;
        }
    
    
        if (!empty($data)) {
            $bool = Db::transaction(function () use ($typeId, $data, $type){
                $this->where(['type_id' => $typeId, 'type' => $type])->delete();
    
                $qiNiu = \Qiniu::instance();
                $save = [];
                foreach ($data as $item) {
                    if (!empty($item['src']) && !empty($item['type'])) {
                        // 1为修改过的，2为服务器返回的
                        $src = $item['type'] == 1 ? $qiNiu->getWeChatImg($item['src']) : $item['src'];
                        $save[] = [
                            'type_id' => $typeId,
                            'src' => $src,
                            'explain' => $item['explain'],
                            'type' => $type,
                        ];
                    }
                }
                $this->insertAll($save);
                
                return true;
            });
        }else{ // 删除
            $this->where(['type_id' => $typeId, 'type' => $type])->delete();
            $bool = true;
        }

        
        
        return $bool;
    }

    public function saveCourseImg($typeId, array $data, $type = 2)
    {
        $bool = false;
        if (empty($typeId)) {
            return $bool;
        }
    
    
        if (!empty($data)) {
            $bool = Db::transaction(function () use ($typeId, $data, $type){
                $this->where(['type_id' => $typeId, 'type' => $type])->delete();
        
                $save = [];
                foreach ($data as $item) {
                    if (!empty($item['src'])) {
                        $save[] = [
                            'type_id' => $typeId,
                            'src' => $item['src'],
                            'explain' => $item['explain'],
                            'type' => $type,
                        ];
                    }
                }
                $this->insertAll($save);
                
                return true;
            });
        }else{
            $this->where(['type_id' => $typeId, 'type' => $type])->delete();
            $bool = true;
        }

        
        
        return $bool;
    }
    
    
}