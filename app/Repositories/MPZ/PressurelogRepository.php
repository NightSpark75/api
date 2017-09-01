<?php
/**
 * 壓差管控記錄資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/09/01
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPZ;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class PressurelogRepository
 *
 * @package App\Repositories
 */
class PressurelogRepository
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
                from mpz_pressurelog
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
                $check = $this->checkPressurelog($params);
                if ($check) {
                    DB::table('mpz_pressurelog')->insert($params);
                    $params = [
                        'point_no' => $params['point_no'],
                        'ldate' => $params['ldate'],
                        'duser' => $params['duser'],
                        'ddate' => $params['ddate'],
                        'point_type' => 'P', 
                    ];
                    DB::table('mpz_point_log')->insert($params);
                } else {
                    DB::update("
                        update mpz_pressurelog
                        set duser = :duser, ddate = :ddate, state = :state, 
                            mo_pa = :mo_pa, mo_aq = :mo_aq, mo_time = :mo_time, mo_user = :mo_user, mo_err = :mo_err,
                            af_pa = :af_pa, af_aq = :af_aq, af_time = :af_time, af_user = :af_user, af_err = :af_err,
                            ev_pa = :ev_pa, ev_aq = :ev_aq, ev_time = :ev_time, ev_user = :ev_user, ev_err = :ev_err,
                            rmk = :rmk
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
            from mpz_pressurelog
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
        if ($params[$cls.'_pa'] !== null || $params[$cls.'_aq']) {
            $params[$cls.'_pa'] = (int) $params[$cls.'_pa'];
            $params[$cls.'_aq'] = (int) $params[$cls.'_aq'];
            if (!isset($log[$cls.'_pa']) || !isset($log[$cls.'_aq'])])) {
                $params[$cls.'_time'] = (int) $date;
                $params[$cls.'_user'] = $params['duser'];
            } else {
                $params[$cls.'_time'] = (int) $log[$cls.'_time'];
                $params[$cls.'_user'] = $log[$cls.'_user'];
            }
        }
        return $params;
    }

    private function checkPressurelog($params)
    {
        $point_no = $params['point_no'];
        $ldate = $params['ldate'];
        $check = DB::selectOne("
            select count(*) count
            from mpz_point_log
            where point_no = '$point_no' and ldate = $ldate and point_type = 'P'
        ");

        if ($check->count === '0') {
            return true;
        }
        return false;
    }
}