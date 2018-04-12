<?php
/**
 * 檢查點記錄資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/07/21
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPZ;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class PointlogRepository
 *
 * @package App\Repositories
 */
class PointlogRepository
{   
    use Sqlexecute;

    public function __construct() {

    }

    public function init()
    {
        try{
            $point = $this->getPoint();
            $result = [
                'result' => true,
                'msg' => 'get point list success',
                'point' => $point,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    private function getPoint()
    {
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, p.device_type, p.point_type, d.device_name
                from mpz_point p, mpz_device d
                where p.state = 'Y' and p.device_type = d.device_no(+)
                order by p.point_type, p.point_no
        ");
        if (count($list) === 0) {
            throw new Exception('can not found data');
        }
        return $list;
    }

    public function noRecord($date) {
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, c.ldate, p.point_type
                from mpz_point p, (
                    select point_no, ldate from mpz_catchlog where ldate = $date
                    union
                    select point_no, ldate from mpz_wetestlog where ldate = $date
                    union
                    select point_no, ldate from mpz_templog where ldate = $date
                    union
                    select point_no, ldate from mpz_pressurelog where ldate = $date
                    union
                    select point_no, ldate from mpz_refrilog where ldate = $date
                ) c
                where p.point_no = c.point_no(+) and p.state = 'Y' and c.ldate is null
                order by point_type, point_no
        ");
        return $list;
    }

    public function noRecordByType($table, $type, $period, $date) {
        $where = $peroid . '_user is null';
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, c.ldate
                from mpz_point p, (
                    select *
                        from $table
                        where ldate = $date and $where
                ) c
                where p.point_no = c.point_no and p.point_type = '$type' and p.state = 'Y'
                order by point_no
        ");
        return $list;
    }

    public function noRecordDetail($table, $date, $type, $mo, $af, $ev) {
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, c.ldate, $mo, $af, $ev
                from mpz_point p, (
                    select *
                        from $table
                        where ldate = $date
                ) c
                where p.point_no = c.point_no(+) and p.point_type = '$type' and p.state = 'Y'
                order by point_no
        ");
        return $list;
    }
}