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
            $dev = $this->getDevInfo($point_no);
            $hum_range = $this->getHumRange($point_no);
            $rule = $this->getPointRule($point_no);
            $data = DB::selectOne("
                select *
                from mpz_wetestlog
                where point_no = '$point_no' and ldate = $ldate
            ");
            $result = [
                'result' => true,
                'log_data' => $data,
                'rule' => $rule,
                'dev' => $dev,
                'humi_low' => $hum_range->humi_high,
                'humi_high' => $hum_range->humi_high,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
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

    private function getDevInfo($point_no)
    {
        $dev = DB::selectOne("
            select p.mach_no, f.stadjj, f.stadlj
                from mpz_point p, jdv_f594343@JDBPRD.STANDARD.COM.TW f
                where p.mach_no = f.stapid(+) and p.point_no = '$point_no'
        ");
        return $dev;
    }

    private function getHumRange($point_no)
    {
        $hum_range = DB::selectOne("
            select humi_low, humi_high
                from mpz_point
                where point_no = '$point_no' and point_type = 'W'
        ");
        return $hum_range;
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
                        from mpz_wetestlog
                        where point_no = '$point_no' and ldate = $ldate
                ");
                if (isset($data)) {
                    $upd = $this->setUpdateSQL($user, $params, $data);
                    DB::update("
                        update mpz_wetestlog
                            set $upd
                            where point_no = $point_no and ldate = $ldate
                    ");
                } else {
                    $params['state'] = 'Y';
                    $params['duser'] = $user;
                    $params['ddate'] = $date;
                    $params['ldate'] = $ldate;
                    $params = $this->setInsertParams($user, $params);
                    DB::table('mpz_wetestlog')->insert($params);
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
        if (isset($params['mo_hum'])) {
            return $this->setParams($user, $params, 'mo');
        }
        if (isset($params['af_hum'])) {
            return $this->setParams($user, $params, 'af');
        }
        if (isset($params['ev_hum'])) {
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
        if (isset($params['mo_hum']) && isset($data->mo_time)) {
            return $this->getUpdateString($user, $params, 'mo');
        }
        if (isset($params['af_hum']) && isset($data->af_time)) {
            return $this->getUpdateString($user, $params, 'af');
        }
        if (isset($params['ev_hum']) && isset($data->ev_time)) {
            return $this->getUpdateString($user, $params, 'ev');
        }
        return '';
    }
    
    private function getUpdateString($user, $params, $type)
    {
        $time = date('Hi');
        $k_hum = $type.'_hum';
        $k_max = $type.'_max';
        $k_min = $type.'_min';
        $k_ed = $type.'_ed';
        $k_eh = $type.'_eh';
        $k_devia = $type.'_devia';
        $k_time = $type.'_time';
        $k_user = $type.'_user';
        $hum = $params[$k_hum];
        $max = $params[$k_max];
        $min = $params[$k_min];
        $ed = $params[$k_ed];
        $eh = $params[$k_eh];
        $devia = $params[$k_devia];
        $str = "
            duser = '$user', ddate = sysdate,
            $k_hum = $hum, $k_max = $max, $k_min = $min
            $k_ed = '$ed', $k_eh = '$eh', $k_devia = '$devia'
            $k_time = $time, $k_user = $user
        ";
        if ($type === 'mo') {
            $rmk = $params['mo_rmk'];
            $dis = $params['mo_dis'];
            $str = $str . ", mo_rmk = '$rmk', mo_dis = '$dis'";
        }
        return $str;
    }
}