<?php
/**
 * 最濕點管控記錄資料處理
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
 * Class WetestlogRepository
 *
 * @package App\Repositories
 */
class WetestlogRepository
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
                from mpz_wetestlog
                where point_no = '$point_no' and ldate = $ldate
            ");
            $result = [
                'result' => true,
                'log_data' => $data,
                'ldate' => $ldate,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
    
    public function save($params)
    {
        try{
            DB::transaction( function () use($params) {
                $user = auth()->user()->id;
                $params['duser'] = $user;
                $params['ddate'] = date("Y-m-d H:i:s");
                $params['state'] = 'Y';
                $params = $this->setLogData($params, $user);
                $check = $this->checkWetestlog($params);
                if ($check) {
                    DB::table('mpz_wetestlog')->insert($params);
                    $params = [
                        'point_no' => $params['point_no'],
                        'ldate' => $params['ldate'],
                        'duser' => $params['duser'],
                        'ddate' => $params['ddate'],
                        'point_type' => 'W', 
                    ];
                    DB::table('mpz_point_log')->insert($params);
                } else {
                    DB::update("
                        update mpz_wetestlog
                        set duser = :duser, ddate = :ddate, state = :state, 
                            mo_hum = :mo_hum, mo_max = :mo_max, mo_min = :mo_min, mo_time = :mo_time, mo_user = :mo_user, mo_rmk = :mo_rmk,
                            af_hum = :af_hum, af_max = :af_max, af_min = :af_min, af_time = :af_time, af_user = :af_user, af_rmk = :af_rmk,
                            ev_hum = :ev_hum, ev_max = :ev_max, ev_min = :ev_min, ev_time = :ev_time, ev_user = :ev_user, ev_rmk = :ev_rmk,
                            zero = :zero, rmk = :rmk
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

    private function setLogData($params, $user)
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
            from mpz_wetestlog
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
        if ($params[$cls.'_hum'] !== null || $params[$cls.'_max'] !== null || $params[$cls.'_min']) {
            $params[$cls.'_hum'] = (int) $params[$cls.'_hum'];
            $params[$cls.'_max'] = (int) $params[$cls.'_max'];
            $params[$cls.'_min'] = (int) $params[$cls.'_min'];
            if (!isset($log[$cls.'_hum']) || !isset($log[$cls.'_max']) || !isset($log[$cls.'_min'])) {
                $params[$cls.'_time'] = (int) $date;
                $params[$cls.'_user'] = $params['duser'];
            } else {
                $params[$cls.'_time'] = (int) $log[$cls.'_time'];
                $params[$cls.'_user'] = $log[$cls.'_user'];
            }
        }
        return $params;
    }

    private function checkWetestlog($params)
    {
        $point_no = $params['point_no'];
        $ldate = $params['ldate'];
        $check = DB::selectOne("
            select count(*) count
            from mpz_point_log
            where point_no = '$point_no' and ldate = $ldate and point_type = 'W'
        ");

        if ($check->count === '0') {
            return true;
        }
        return false;
     }
}