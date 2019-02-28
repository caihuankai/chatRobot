<?php

namespace app\common\model;


class Gift extends ModelBs
{
    
    public static function giftType() {
        return [
            '1' => '常规礼物',
            // '3' => 'vip会员礼物',
        ];
    }
    
    public static function giftPosition() {
        return [
            '1' => 'Live直播间-赠送礼物',       
        ];
    }
    
    public static function giftStatus(){
        return [
            '1' => '上架',       
            '2' => '下架',       
        ];
    }
    
    
}