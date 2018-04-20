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
            $date = date('Ymd');
            $point = $this->getPoint();
            $unrecorded = $this->getUnrecordedList($date);
            $result = [
                'result' => true,
                'msg' => 'get point list success',
                'point' => $point,
                'unrecorded' => $unrecorded,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    /**
     * send today no record list
     * 
     * @param int $date
     */
    public function getUnrecordedList($date)
    {
        $catch = $this->catchDetailHandler($date);
        $temp = $this->tempDetailHandler($date);
        $wetest = $this->wetestDetailHandler($date);
        $refri = $this->refriDetailHandler($date);
        $pressure = $this->pressureDetailHandler($date);
        $list = array_collapse([$catch, $temp, $wetest, $refri, $pressure]);
        return $list;
    }

    private function catchDetailHandler($date)
    {   
        $mo = "case when ldate is null then 'X' else '' end mo";
        $af = "'' af";
        $ev = "'' ev";
        $catch = $this->getDetail('mpz_catchlog', $date, 'C', $mo, $af, $ev);
        return $catch;
    }

    private function getDetail($table, $date, $type, $mo, $af, $ev) {
        $arr = [];
        $list = $this->noRecordDetail($table, $date, $type, $mo, $af, $ev);
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]->mo === 'X' || $list[$i]->af === 'X'|| $list[$i]->ev === 'X') {
                array_push($arr, $list[$i]);
            }
        }
        return $arr;
    }

    private function tempDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $temp = $this->getDetail('mpz_templog', $date, 'T', $mo, $af, $ev);
        return $temp;
    }

    private function wetestDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $wetest = $this->getDetail('mpz_wetestlog', $date, 'W', $mo, $af, $ev);
        return $wetest;
    }

    private function refriDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "'' af";
        $ev = "case when af_user is null then 'X' else '' end ev";
        $refri = $this->getDetail('mpz_refrilog', $date, 'R', $mo, $af, $ev);
        return $refri;
    }

    private function pressureDetailHandler($date)
    {
        $mo = "case when mo_user is null then 'X' else '' end mo";
        $af = "case when af_user is null then 'X' else '' end af";
        $ev = "case when ev_user is null then 'X' else '' end ev";
        $pressure = $this->getDetail('mpz_pressurelog', $date, 'P', $mo, $af, $ev);
        return $pressure;
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
        $where = $period . '_user is null';
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, c.ldate
                from mpz_point p, (
                    select *
                        from $table
                        where ldate = $date and $where
                ) c
                where p.point_no = c.point_no and p.point_type = '$type' and p.state = 'Y'
            union
            select p.point_no, p.point_name, p.point_des, c.ldate
                from mpz_point p, (
                    select point_no, ldate from $table where ldate = $date
                ) c
                where p.point_no = c.point_no(+) and p.state = 'Y' and c.ldate is null and p.point_type = '$type'
            order by point_no
        ");
        return $list;
    }

    public function noRecordDetail($table, $date, $type, $mo, $af, $ev) {
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, c.ldate, p.mcu, p.floor, $mo, $af, $ev
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