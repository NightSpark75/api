<?php
/**
 * 冷藏櫃管控記錄資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/08/29
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPZ;

use DB;
use Exception;
use App\Traits\Sqlexecute;

/**
 * Class RefrilogRepository
 *
 * @package App\Repositories
 */
class RefrilogRepository
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
                from mpz_refrilog
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
                $check = $this->checkRefrilog($params);
                if ($check) {
                    DB::table('mpz_refrilog')->insert($params);
                    $params = [
                        'point_no' => $params['point_no'],
                        'ldate' => $params['ldate'],
                        'duser' => $params['duser'],
                        'ddate' => $params['ddate'],
                        'point_type' => 'R', 
                    ];
                    DB::table('mpz_point_log')->insert($params);
                } else {
                    DB::update("
                        update mpz_refrilog
                        set duser = :duser, ddate = :ddate, state = :state, rmk = :rmk, error_item = :error_item
                            mo_temp = :mo_temp, mo_putt = :mo_putt, mo_bell = :mo_bell, mo_light = :mo_light, 
                                mo_time = :mo_time, mo_user = :mo_user, mo_rmk = :mo_rmk,
                            af_temp = :af_temp, af_putt = :af_putt, af_bell = :af_bell, af_light = :af_light, 
                                af_time = :af_time, af_user = :af_user, af_rmk = :af_rmk
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

        $log = DB::selectOne("
            select *
            from mpz_refrilog
            where point_no = '$point_no' and ldate = $ldate
        ");

        $params = $this->setLogTime($params, $log, 'mo');
        $params = $this->setLogTime($params, $log, 'af');

        $params = $this->setErrorItem($params, $log);

        return $params;
    }

    private function setErrorItem($params, $log)
    {
        if ($params['error_item'] !== $log['error_item']) {
            $params['error_user'] = $params['duser'];
        }
        return $params;
    }

    private function setLogTime($params, $log, $cls)
    {
        $date = (int)date("Hi");
        $log = json_decode(json_encode($log), true);
        if ($params[$cls.'_temp'] !== null) {
            $params[$cls.'_temp'] = (float) $params[$cls.'_temp'];
            if (!isset($log[$cls.'_temp'])) {
                $params[$cls.'_time'] = (int) $date;
                $params[$cls.'_user'] = $params['duser'];
            } else {
                $params[$cls.'_time'] = (int) $log[$cls.'_time'];
                $params[$cls.'_user'] = $log[$cls.'_user'];
            }
        }
        return $params;
    }

    private function checkRefrilog($params)
    {
        $point_no = $params['point_no'];
        $ldate = $params['ldate'];
        $check = DB::selectOne("
            select count(*) count
            from mpz_point_log
            where point_no = '$point_no' and ldate = $ldate and point_type = 'R'
        ");

        if ($check->count === '0') {
            return true;
        }
        return false;
     }
}