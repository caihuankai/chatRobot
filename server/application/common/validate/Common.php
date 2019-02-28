<?php
/**
 * Created by PhpStorm.
 * User: code
 * Date: 2016/11/14
 * Time: 17:22
 */

namespace app\common\validate;


use app\common\controller\JsonResult;

/**
 * 公共验证
 *
 * @package app\common\validate
 */
class Common extends ValidateBase
{
    protected $rule = [
        'table' => 'number',
        'pageNumber' => 'number|>:0',
        'pageSize' => 'number|>:0',
    ];
    
    
    protected $scene = [
        'id' => ['id'],
        'ids' => ['ids'],
        'userId' => ['userId'],
        'telExists' => ['telExistsValidate'], // 只有一种报错信息
        'date' => ['date'],
        'url' => ['url'],
        'adminTable' => ['table', 'pageNumber', 'pageSize'],
        'page' =>['page'],
    ];
    
    
    protected $message = [
        'id' => JsonResult::ERR_PARAMETER,
        'ids' => JsonResult::ERR_PARAMETER,
        'userId' => JsonResult::ERR_PARAMETER,
        'date' => JsonResult::ERR_PARAMETER,
        'url' => JsonResult::ERR_PARAMETER,
        'page' => JsonResult::ERR_PARAMETER,

        'table' => JsonResult::ERR_PARAMETER,
        'pageNumber' => JsonResult::ERR_PARAMETER,
        'pageSize' => JsonResult::ERR_PARAMETER,
    ];
    
    
}