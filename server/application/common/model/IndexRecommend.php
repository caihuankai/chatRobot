<?php

namespace app\common\model;


class IndexRecommend extends ModelBs
{
    use \app\common\traits\MysqlTinyintModel;
    
    
    protected static $mysqlTinyintMap = [
        'type' => [
            1 => [], // 明显流量主
            2 => [],
            3 => [],
            4 => [],
            5 => [], // 人气直播
        ],
    ];
    
    
    public function whereShow()
    {
        $this->where(['status' => 1]);
        
        return $this;
    }
    
    
    public function joinSwitch($type, $join = 'inner')
    {
        switch ($type){
            case 1: // 明星流量主
                $this->joinLiveUser(null, $join);
                break;
            case 2: // 精品课程
                $this->joinCourse(null, $join);
                break;
            case 3: // 名师推荐
                $this->joinTeacherLive(null, $join);
                break;
            case 4: // 深度阅读
                $this->joinViewpoint(null, $join);
                break;
            case 5: //
                $this->joinHotUser(null, $join);
                break;
        }
        
        return $this;
    }
    
    
    /**
     * 明星流量主
     *
     * @param null $model
     * @return \think\db\Query
     */
    public function joinLiveUser($model = null, $join = 'inner')
    {
        return $this->disFormalModel($model)->join('user u', 'u.user_id = ir.type_id and ir.type = 1 and u.freeze = 0', $join);
    }
    
    /**
     * 名师推荐
     *
     * @param null $model
     * @return \think\db\Query
     */
    public function joinTeacherLive($model = null, $join = 'inner')
    {
        return $this->disFormalModel($model)->join('live l', 'l.id= ir.type_id and ir.type = 3 and l.open_status = 1', $join)
            ->join('user u', 'u.user_id = l.user_id and u.freeze = 0', $join);
    }
    
    
    public function joinHotUser($model = null, $join = 'inner')
    {
        return $this->disFormalModel($model)->join('user u', 'u.user_id = ir.type_id and ir.type = 5 and u.freeze = 0', $join)
        	->join('live l', 'l.user_id = ir.type_id and l.open_status = 1', $join);
    }
    
    
    public function joinCourse($model = null, $join = 'inner')
    {
        return $this->disFormalModel($model)->join('course c', 'c.id = ir.type_id and ir.type = 2
                    and c.status <> 5 and c.open_status = 1', $join)
            ->join('user u', 'u.user_id = c.uid and u.freeze = 0', $join);
    }
    
    
    public function joinViewpoint($model = null, $join = 'inner')
    {
        return $this->disFormalModel($model)->join('viewpoint v', 'v.id = ir.type_id and ir.type = 4 and v.status <> 2', $join)
            ->join('user u', 'u.user_id = v.uid and u.freeze = 0', $join);
    }
}