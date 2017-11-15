<?php
/**
 * 執行SQL語法
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/26
 * @since 1.0.0 spark: 完成基本功能
 * 
 */
namespace App\Traits;

use DB;
use PDO;

/**
 * trait Sqlexecute
 *
 * @package App\Traits
 */
trait Sqlexecute
{
    /**
     * 執行SQL語法
     * 
     * @param array $bindings parames
     * @param string $query query string
     * @return 
     */
    private function query($bindings, $query) 
    {
        try {
            $statement = DB::getPdo()->prepare($query);
            DB::bindValues($statement, DB::prepareBindings($bindings));
            $statement->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 執行SQL查詢
     * 
     * @param string $query query string
     * @return 
     */
    private function select($query)
    {
        return DB::selectOne($query);
    }

    /**
     * 執行procedure
     * 
     * @param string $name procedure name
     * @param array $par_in  
     * @param array $par_out 
     * @return array
     */
    private function procedure($name, $pars)
    {
        $pdo = DB::getPdo();
        list($key, $val) = array_divide($pars);
        $par_str = implode(',', $key);

        $stmt = $pdo->prepare("begin $name($par_str); end;");
        for ($i = 0; $i < count($key); $i++) {
            if (is_numeric($val[$i])) {
                $stmt->bindParam($key[$i], $val[$i], PDO::PARAM_INT);
            } else {
                $stmt->bindParam($key[$i], $val[$i], PDO::PARAM_STR, 4000);
            }
        }
        $stmt->execute();
        $new_array = array_combine($key, $val);
        return $new_array;
    }

    /**
     * 回傳exception訊息
     * 
     * @param string $exception exception
     * @return array
     */
    private function exception($exception, $result = [])
    {
        $result['result'] = false;
        $result['msg'] = $exception->getMessage();
        return $result;
    }

    /**
     * return error page and error message
     * 
     * @param string $message error message
     * @return view
     */
    private function errorPage($message)
    {
        return view('error')->with('message', $message);
    }

    /**
     * return success info
     * 
     * @param string $array success params
     * @return Mix
     */
    private function success($info = [])
    {
        $info['result'] = true;
        return $info;
    }
}