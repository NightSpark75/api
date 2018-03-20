<?php
/**
 * Oacle相關功能
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
trait Oracle
{
    /**
     * unix time to Date
     *
     * @return String
     */
    public function unixToDate($unix) 
    {
        $date = date('d-M-y', $unix);

        return $date;
    }

    /**
     * unix time to Date
     *
     * @return String
     */
    public function toDate($date) 
    {
        $formatDate = date('Y-m-d', $date);

        return $formatDate;
    }
}