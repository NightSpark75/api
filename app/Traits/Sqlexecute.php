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

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\View\View;
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
     * @param array $bindings bindings parameters
     * @param string $query query string
     * @return void
     * @throws Exception
     */
    public function query($bindings, $query) 
    {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $statement = DB::getPdo()->prepare($query);
            /** @noinspection PhpUndefinedMethodInspection */
            DB::bindValues($statement, DB::prepareBindings($bindings));
            /** @noinspection PhpUndefinedMethodInspection */
            $statement->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 執行SQL查詢
     *
     * @param string $query query string
     * @return \stdClass
     */
    public function select($query)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return DB::selectOne($query);
    }

    /**
     * 執行procedure
     *
     * @param string $name procedure name
     * @param $pars
     * @return array
     */
    public function procedure($name, $pars)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $pdo = DB::getPdo();
        list($key, $val) = array_divide($pars);
        $par_str = implode(',', $key);

        /** @noinspection PhpUndefinedMethodInspection */
        $stmt = $pdo->prepare("begin $name($par_str); end;");
        for ($i = 0; $i < count($key); $i++) {
            if (is_numeric($val[$i])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $stmt->bindParam($key[$i], $val[$i], PDO::PARAM_INT);
            } else {
                /** @noinspection PhpUndefinedMethodInspection */
                $stmt->bindParam($key[$i], $val[$i], PDO::PARAM_STR, 4000);
            }
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $stmt->execute();
        $new_array = array_combine($key, $val);
        return $new_array;
    }

    /**
     * 回傳exception訊息
     *
     * @param string $exception exception
     * @param array $result
     * @return array
     */
    public function exception($exception, $result = [])
    {
        $result['result'] = false;
        /** @noinspection PhpUndefinedMethodInspection */
        $result['msg'] = $exception->getMessage();
        return $result;
    }

    /**
     * return error page and error message
     * 
     * @param string $message error message
     * @return View
     */
    public function errorPage($message)
    {
        return view('error')->with('message', $message);
    }

    /**
     * return success info
     *
     * @param array $info
     * @return array
     */
    public function success($info = [])
    {
        $info['result'] = true;
        return $info;
    }
}