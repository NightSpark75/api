<?php

namespace Traits;

class Uuid
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