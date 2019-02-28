<?php

namespace app\common\model;

class UserPhone extends ModelBs
{
    use \app\wechat\traits\Common,
        \app\common\traits\MysqlTinyintModel;


    protected static $mysqlTinyintMap = [
        'type' => [
            1 => [
                \helper\Tinyint::TITLE => '购买课程后',
            ],
        	2 => [
        		\helper\Tinyint::TITLE => 'app注册',
        	],
        ],
        
        'binding_type_map' => [
            4 => [
                \helper\Tinyint::VALUE => 1,
            ],
        	3 => [
        		\helper\Tinyint::VALUE => 2,
        	],
        ],
        
    ];
    
    
    /**
     * 检测是否绑定
     *
     * @param $phone
     * @param $type
     * @return bool
     * @author aozhuochao
     */
    public function checkBinding($phone, $type, $userId)
    {
        if (empty($phone) || empty($type)) {
            return true;
        }
        
        return !!$this->where(['user_id'=>$userId, 'phone' => $phone, 'type' => $type])->field('id')->find();
    }
    
}