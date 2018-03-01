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
            $range = $this->getTempRange($point_no);
            $dev = $this->getDevInfo($point_no);
            $rule = $this->getPointRule($point_no);
            $data = DB::selectOne("
                select *
                from mpz_refrilog
                where point_no = '$point_no' and ldate = $ldate
            ");
            $result = [
                'result' => true,
                'log_data' => $data,
                'dev' => $dev,
                'rule' => $rule,
                'temp_low' => $range->temp_low,
                'temp_high' => $range->temp_high,
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


    private function getTempRange($point_no)
    {
        $range = DB::selectOne("
            select temp_low, temp_high
                from mpz_point
                where point_no = '$point_no' and point_type = 'R'
        ");
        return $range;
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
                        from mpz_refrilog
                        where point_no = '$point_no' and ldate = $ldate
                ");
                if (isset($data)) {
                    $upd = $this->setUpdateSQL($user, $params, $data);
                    DB::update("
                        update mpz_refrilog
                            set $upd
                            where point_no = $point_no and ldate = $ldate
                    ");
                } else {
                    $params['state'] = 'Y';
                    $params['duser'] = $user;
                    $params['ddate'] = $date;
                    $params['ldate'] = $ldate;
                    $params = $this->setInsertParams($user, $params);
                    DB::table('mpz_refrilog')->insert($params);
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
        if (isset($params['mo_pa'])) {
            return $this->setParams($user, $params, 'mo');
        }
        if (isset($params['af_pa'])) {
            return $this->setParams($user, $params, 'af');
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
        return '';
    }
    
    private function getUpdateString($user, $params, $type)
    {
        $time = date('Hi');
        $k_temp = $type.'_temp';
        $k_ed = $type.'_ed';
        $k_ep = $type.'_ep';
        $k_devia = $type.'_devia';
        $k_time = $type.'_time';
        $k_user = $type.'_user';
        $temp = $params[$k_temp];
        $ed = $params[$k_ed];
        $ep = $params[$k_ep];
        $devia = $params[$k_devia];
        $str = "
            duser = '$user', ddate = sysdate,
            $k_temp = $temp,  
            $k_ed = '$ed', $k_ep = '$ep', $k_devia = '$devia'
            $k_time = $time, $k_user = $user
        ";
        if ($type === 'mo') {
            $putt = $params['mo_putt'];
            $bell = $params['mo_bell'];
            $light = $params['mo_light'];
            $rmk = $params['mo_rmk'];
            $dis = $params['mo_dis'];
            $str = $str . "mo_putt = '$putt', mo_bell = '$mo_bell', mo_light = '$mo_light', mo_rmk = '$rmk', mo_dis = '$dis'";
        }
        return $str;
    }
}