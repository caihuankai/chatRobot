<?php

namespace helper;

/**
 * 处理字符串
 *
 * @package helper\StringBash
 */
class StringBash
{
    
    /**
     * 输入框
     *
     * @param $varchar
     * @return string
     */
    static public function varchar($varchar)
    {
        return htmlentities($varchar, ENT_QUOTES | ENT_COMPAT | ENT_HTML401); // ENT_QUOTES会将单引号也转为实体
    }
    
    
    /**
     * 富文本
     *
     * @param $text
     * @return string
     */
    static public function ueText($text)
    {
        return preg_replace_callback('/<\/?script[^<]*>/i', function ($matches) {
            // htmlspecialchars替换的少点
            return !empty($matches[0]) ? htmlspecialchars($matches[0], ENT_QUOTES | ENT_COMPAT | ENT_HTML401) :
                $matches[0];
        }, $text);
    }
}