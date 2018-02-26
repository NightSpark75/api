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
            $dev = $this->getDevInfo($point_no);
            $zone = $this->getZoneInfo($point_no);
            $rule = $this->getPointRule($point_no);
            $result = [
                'result' => true,
                'log_data' => $data,
                'ldate' => $ldate,
                'rule' => $rule,
                'dev' => $dev,
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

    private function getDevInfo($point_no)
    {
        $dev = DB::selectOne("
            select p.mach_no, f.stadjj, f.stadlj
                from mpz_point p, jdv_f594343@JDBPRD.STANDARD.COM.TW f
                where p.mach_no = f.stapid(+) and p.point_no = '$point_no'
        ");
        return $dev;
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

    private function getPointRule($point_no)
    {
        $arr = [];
        $rule = DB::select("
            select r.*
                from mpz_point_rule r, mpz_point p
                where p.point_type = r.point_type and (r.device_type = p.device_type or r.device_type = 'N')
                    and p.point_no = '$point_no'
        ");
        $rule = json_decode(json_encode($rule), true);
        for ($i = 0; $i < count($rule); $i++) {
            $rname = $rule[$i]['rname'];
            $item = $rule[$i];
            $arr[$rname] = $item;
        }
        return $arr;
    }
    
    public function save($params)
    {
        try{
            DB::transaction( function () use($params) {
                $user = auth()->user()->id;
                $point_no = $params['point_no'];
                $ldate = date('Ymd');
                $date = date('Y-m-d H:i:s');
                $data = DB::selectOne("
                    select *
                        from mpz_templog
                        where point_no = '$point_no' and ldate = $ldate
                ");
                if (isset($data)) {
                    $upd = $this->setUpdateSQL($user, $params, $data);
                    DB::update("
                        update mpz_templog
                            set $upd
                            where point_no = $point_no and ldate = $ldate
                    ");
                } else {
                    $params['state'] = 'Y';
                    $params['duser'] = $user;
                    $params['ddate'] = $date;
                    $params = $this->setInsertParams($user, $params);
                    DB::table('mpz_templog')->insert($params);
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

    private function setInsertParams($user, $params)
    {
        if (isset($params['mo_temp'])) {
            return $this->setParams($user, $params, 'mo');
        }
        if (isset($params['af_temp'])) {
            return $this->setParams($user, $params, 'af');
        }
        if (isset($params['ev_temp'])) {
            return $this->setParams($user, $params, 'ev');
        }
        return $params;
    }

    private function setParams($user, $params, $type)
    {
        $time = date('Hi');
        $params[$type.'_time'] = $time;
        $params[$type.'_user'] = $user;
        return $params;
    }

    private function setUpdateSQL($user, $params, $data)
    {
        $time = date('Hi');
        if (isset($params['mo_temp']) && isset($data->mo_time)) {
            return $this->getUpdateString($user, $params, 'mo');
        }
        if (isset($params['af_temp']) && isset($data->af_time)) {
            return $this->getUpdateString($user, $params, 'af');
        }
        if (isset($params['ev_temp']) && isset($data->ev_time)) {
            return $this->getUpdateString($user, $params, 'ev');
        }
        return '';
    }
    
    private function getUpdateString($user, $params, $type)
    {
        $temp = $params[$type.'_temp'];
        $hum = $params[$type.'_hum'];
        $ed = $params[$type.'_ed'];
        $eth = $params[$type.'_eth'];
        $edevia = $params[$type.'_edevia'];
        $str = "
            duser = '$user', ddate = sysdate,
            mo_temp = $temp, mo_hum = $hum,
            mo_ed = '$mo_ed', mo_eth = '$mo_eth', mo_edevia = '$edevia'
            mo_time = $time, mo_user = $user
        ";
        if ($type === 'mo') {
            $rmk = $params[$type.'_rmk'];
            $dis = $params[$type.'_dis'];
            $str = $str . ", mo_rmk = '$rmk', mo_dis = '$dis'";
        }
        return $str;
    }
}