<?php

namespace app\common\model;


class CoursePptImg extends \app\common\model\ModelBs
{
    
    
    public function saveUrl($courseId, $urlList, $userId = 0)
    {
        if (empty($courseId)) {
            return false;
        }
        
        $this->where(['course_id' => ['eq', $courseId], 'type' => 1])->delete();
    
        if (!empty($urlList) && is_array($urlList)) {
            $save = [];
            
            foreach ($urlList as $item) {
                if (empty($item['src'])){
                    continue;
                }
    
                $key = isset($item['key']) ? $item['key'] : parse_url($item['src'], PHP_URL_PATH);
                $save[] = [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'qiniuKey' => trim($key, '/'),
                    'imgUrl' => $item['src'],
                    'sort' => isset($item['sort']) ? $this->disSort($item['sort']) : 0,
                ];
            }
            
            $this->insertAll($save);
        }
        
        return true;
    }
    
    
    public function disSort($sort)
    {
        return $sort > 99 ? 99 : abs($sort);
    }
    
}