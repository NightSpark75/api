<?php
/**
 * 取得UUID
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 完成基本功能
 * 
 */
namespace App\Traits;

/**
 * trait Uuid
 *
 * @package App\Traits
 */
trait Uuid
{
    /**
     * 取得UUID
     *
     * @return String
     */
    function getUuid() 
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);  // "-"
        $uuid = ''.substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}