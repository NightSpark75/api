<?php
/**
 * 溫溼度管控記錄資料處理
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
 * Class TemplogRepository
 *
 * @package App\Repositories
 */
class TemplogRepository
{   
    use Sqlexecute;

    public function __construct() {

    }

    public function init($point_no)
    {
        try{
            $ldate = date('Ymd');
            $data = DB::selectOne("
                select *
                from mpz_templog
                where point_no = '$point_no' and ldate = $ldate
            ");
            $zone = $this->getZoneInfo($point_no);
            $result = [
                'result' => true,
                'log_data' => $data,
                'ldate' => $ldate,
                'temp_high' => $zone->temp_high,
                'temp_low' => $zone->temp_low,
                'humi_high' => $zone->humi_high,
                'humi_low' => $zone->humi_low,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    private function getZoneInfo($point_no)
    {
        $zone = DB::selectOne("
            select nvl(temp_high, 0) temp_high, nvl(temp_low, 0) temp_low, nvl(humi_high, 0) humi_high, nvl(humi_low, 0) humi_low
            from mpz_point p
            where p.point_no = '$point_no' and p.point_type = 'T'
        ");
        return $zone;
    }
    
    public function save($params)
    {
        try{
            DB::transaction( function () use($params) {
                $user = auth()->user()->id;
                $params['duser'] = $user;
                $params['ddate'] = date("Y-m-d H:i:s");
                $params['state'] = 'Y';
                $params = $this->setLogDate($params, $user);
                $check = $this->checkTemplog($params);
                if ($check) {
                    DB::table('mpz_templog')->insert($params);
                    $params = [
                        'point_no' => $params['point_no'],
                        'ldate' => $params['ldate'],
                        'duser' => $params['duser'],
                        'ddate' => $params['ddate'],
                        'point_type' => 'T', 
                    ];
                    DB::table('mpz_point_log')->insert($params);
                } else {
                    DB::update("
                        update mpz_templog
                        set duser = :duser, ddate = :ddate, state = :state, 
                            mo_temp = :mo_temp, mo_hum = :mo_hum, mo_time = :mo_time, mo_err = :mo_err, 
                                mo_type = :mo_type, mo_rmk = :mo_rmk, mo_user = :mo_user,
                            af_temp = :af_temp, af_hum = :af_hum, af_time = :af_time, af_err = :af_err, 
                                af_type = :af_type, af_rmk = :af_rmk, af_user = :af_user,
                            ev_temp = :ev_temp, ev_hum = :ev_hum, ev_time = :ev_time, ev_err = :ev_err, 
                                ev_type = :ev_type, ev_rmk = :ev_rmk , ev_user = :ev_user
                        where point_no = :point_no and ldate = :ldate
                    ", $params);
                }
            });
            DB::commit();
            $result = [
                'result' => true,
                'msg' => '新增檢查點資料成功(#0003)'
            ];
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }

    private function setLogDate($params, $user)
    {
        $point_no = $params['point_no'];
        $ldate = (int)$params['ldate'];
        $params['mo_time'] = null;
        $params['mo_user'] = null;
        $params['af_time'] = null;
        $params['af_user'] = null;
        $params['ev_time'] = null;
        $params['ev_user'] = null;

        $log = DB::selectOne("
            select *
            from mpz_templog
            where point_no = '$point_no' and ldate = $ldate
        ");

        $params = $this->setLogTime($params, $log, 'mo');
        $params = $this->setLogTime($params, $log, 'af');
        $params = $this->setLogTime($params, $log, 'ev');

        return $params;
    }

    private function setLogTime($params, $log, $cls)
    {
        $date = (int)date("Hi");
        $log = json_decode(json_encode($log), true);
        if ($params[$cls.'_temp'] !== null || $params[$cls.'_hum'] !== null) {
            $params[$cls.'_temp'] = (float) $params[$cls.'_temp'];
            $params[$cls.'_hum'] = (float) $params[$cls.'_hum'];
            if (!isset($log[$cls.'_temp']) || !isset($log[$cls.'_hum'])) {
                $params[$cls.'_time'] = (int) $date;
                $params[$cls.'_user'] = $params['duser'];
            } else {
                $params[$cls.'_time'] = (int) $log[$cls.'_time'];
                $params[$cls.'_user'] = $log[$cls.'_user'];
            }
        }
        return $params;
    }

    private function checkTemplog($params)
    {
        $point_no = $params['point_no'];
        $ldate = $params['ldate'];
        $check = DB::selectOne("
            select count(*) count
            from mpz_point_log
            where point_no = '$point_no' and ldate = $ldate and point_type = 'T'
        ");

        if ($check->count === '0') {
            return true;
        }
        return false;
     }
}