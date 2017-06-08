<?php

namespace Services;

class CommonService
{
    /*
    |--------------------------------------------------------------------------
    | Common Class
    |--------------------------------------------------------------------------
    |
    | 共用元件函式庫
    |
    */


    /**
     * 取得UUID
     *
     * @return void
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