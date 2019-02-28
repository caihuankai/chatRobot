<?php

namespace ThinkPHP;

/**
 * Class Validate
 *
 * @package ThinkPHP
 */
class Validate extends \think\Validate
{
    protected function is($value, $rule, $data = [])
    {
        switch ($rule){
            case 'noEmoJi':
                // 不允许emoJi表情
                $result = !$this->regex($value, (new \Kozz\Components\Emoji\EmojiParser())->getPattern());
                break;
            default:
                $result = parent::is($value, $rule, $data);
                break;
        }
        
        return $result;
    }
    
    
}