<?php
/**
 * common function
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/25
 * @since 1.0.0 spark: initial
 * 
 */
namespace App\Traits;

trait Common {

    public function getException($e)
    {
        $message = $e->getMessage();
        $code = $e->getCode();
        $line = $e->getLine();
        $file = $e->getfile();
        return compact('message', 'code', 'line', 'file');
    }
}