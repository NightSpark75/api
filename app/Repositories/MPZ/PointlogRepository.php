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
                'msg' => '已成功取得檢查點資料!(#0001)',
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
            select p.point_no, p.point_name, p.device_type, d.device_name, p.point_type, p.mcu, p.mach_no, p.ch_date,
                p.hum_range, p.temp_range, p.pa_range, p.aq_range, p.point_des
            from mpz_point p, mpz_device d
            where p.state = 'Y' and d.device_no(+) = p.device_type
            order by p.point_type, p.point_no
        ");
        if (count($list) === 0) {
            throw new Exception('查詢不到檢查點資料!(#0002)');
        }
        return $list;
    }

    public function check($point_no)
    {
        try {
            $msg = '';
            $today =  (int)date('Ymd');
            $check = DB::select("
                select *
                from v_mpz_point_log
                where point_no = '$point_no' and ldate = $today
            ");
            if (count($check) > 0) {
                $result = [
                    'result' => false,
                    'msg' => '此檢查點今日已記錄完畢!(#0004)',
                ];
            } else {
                $result = [
                    'result' => true,
                    'msg' => '此檢查點今日尚未記錄(#0005)',
                    'ldate' => $today,
                ];
            }
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function noRecord($date)
    {
        $list = DB::select("
            select *
                from mpz_point p
                where p.state = 'Y'
                    and not exists(
                        select *
                            from v_mpz_point_log g
                            where g.ldate = $date and p.point_no = g.point_no
                    )
                order by point_type, point_no
        ");
        return $list;
    }

    public function noRecordByType($table, $type, $cls, $date)
    {
        $ck = $cls.'_user';
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des
                from mpz_point p  
                where p.state = 'Y' and p.point_type = '$type'
                    and not exists (
                        select *
                            from $table t
                            where t.ldate = $date and  p.point_no = t.point_no and $ck is not null
                    )
        ");
        return $list;
    }

    public function noRecordDetail($table, $date, $type, $mo, $af, $ev) {
        $list = DB::select("
            select p.point_no, p.point_name, p.point_des, t.ldate, $mo, $af, $ev
                from mpz_point p, (
                        select * 
                        from $table c where ldate = $date 
                    ) t
                where p.state = 'Y' and p.point_type = '$type' and p.point_no = t.point_no(+)
        ");
        return $list;
    }
}