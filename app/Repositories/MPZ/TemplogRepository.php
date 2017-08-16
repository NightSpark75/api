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
            select z.temp_high, z.temp_low, z.humi_high, z.humi_low
            from mpz_zone_type z, mpz_point p
            where p.point_no = '$point_no' and p.point_type = 'T' and p.zone = z.zone_name
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
                $check = $this->checkTemplog($params['point_no']);
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
                        set duser = :duser, ddate = :ddate, ldate = :ldate, state = :state, 
                            mo_temp = :mo_temp, mo_hum = :mo_hum, mo_time = :mo_time, mo_err = :mo_err, 
                                mo_type = :mo_type, mo_rmk = :mo_rmk, mo_user = :mo_user,
                            af_temp = :af_temp, af_hum = :af_hum, af_time = :af_time, af_err = :af_err, 
                                af_type = :af_type, af_rmk = :af_rmk, af_user = :af_user,
                            ev_temp = :ev_temp, ev_hum = :ev_hum, ev_time = :ev_time, ev_err = :ev_err, 
                                ev_type = :ev_type, ev_rmk = :ev_rmk , ev_user = :ev_user
                        where point_no = :point_no
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
        $date = (int)date("Hi");
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

        if ($params['mo_temp'] !== null || $params['mo_hum'] !== null) {
            if (!isset($log->mo_temp) || !isset($log->mo_hum)) {
                $params['mo_time'] = $date;
                $params['mo_user'] = $user;
            } else {
                $params['mo_time'] = $log->mo_time;
                $params['mo_user'] = $log->mo_user;
            }
        }

        if (($params['af_temp'] !== null || $params['af_hum'] !== null)) {
            if ((!isset($log->af_temp) || !isset($log->af_hum))) {
                $params['af_time'] = $date;
                $params['af_user'] = $user;
            } else {
                $params['af_time'] = $log->af_time;
                $params['af_user'] = $log->af_user;
            }
        }

        if ($params['ev_temp'] !== null || $params['ev_hum'] !== null) {
            if (!isset($log->ev_temp) || !isset($log->ev_hum)) {
                $params['ev_time'] = $date;
                $params['ev_user'] = $user;
            } else {
                $params['ev_time'] = $log->ev_time;
                $params['ev_user'] = $log->ev_user;
            }
        }

        return $params;
    }

    private function checkTemplog($point_no)
    {
        $check = DB::selectOne("
            select count(*) count
            from mpz_point_log
            where point_no = '$point_no' and point_type = 'T'
        ");

        if ($check->count === '0') {
            return true;
        }
        return false;
     }
}