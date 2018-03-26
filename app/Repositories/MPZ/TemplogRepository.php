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

    private $type;

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
                where p.mach_no = trim(f.stapid(+)) and p.point_no = '$point_no'
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
                    $params['ldate'] = $ldate;
                    $params = $this->setInsertParams($user, $params);
                    DB::table('mpz_templog')->insert($params);
                }
                $this->mailhandler($params);
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
        $this->type = $this->getCurrent($params['point_no']);
        if ($this->type !== '') {
            return $this->setParams($user, $params);
        }
    }

    private function setParams($user, $params)
    {
        $time = date('Hi');
        $params[$this->type.'_time'] = $time;
        $params[$this->type.'_user'] = $user;
        return $params;
    }

    private function setUpdateSQL($user, $params, $data)
    {
        $time = date('Hi');
        $this->type = $this->getCurrent($params['point_no']);
        if ($this->type !== '') {
            return $this->getUpdateString($user, $params);
        }
    }

    private function getCurrent($point_no)
    {
        $time = date('Hi');
        $rule = $this->getPointRule($point_no);
        if ((int)$rule['MO_START']['val'] <= (int)$time && (int)$rule['MO_OTHER']['val'] >= (int)$time) {
            return 'mo';
        }
        if ((int)$rule['AF_START']['val'] <= (int)$time && (int)$rule['AF_END']['val'] >= (int)$time) {
            return 'af';
        }
        if ((int)$rule['EV_START']['val'] <= (int)$time && (int)$rule['EV_END']['val'] >= (int)$time) {
            return 'ev';
        }
        return '';
    }
    
    private function getUpdateString($user, $params)
    {
        $time = date('Hi');
        $k_temp = $this->type.'_temp';
        $k_hum = $this->type.'_hum';
        $k_ed = $this->type.'_ed';
        $k_eth = $this->type.'_eth';
        $k_devia = $this->type.'_devia';
        $k_time = $this->type.'_time';
        $k_user = $this->type.'_user';
        $temp = $params[$k_temp];
        $hum = $params[$k_hum];
        $ed = $params[$k_ed];
        $eth = $params[$k_eth];
        $devia = $params[$k_devia];
        $str = "
            duser = '$user', ddate = sysdate,
            $k_temp = $temp, $k_hum = $hum,
            $k_ed = '$mo_ed', $k_eth = '$eth', $k_devia = '$devia',
            $k_time = $time, $k_user = $user
        ";
        if ($this->type === 'mo') {
            $rmk = $params['mo_rmk'];
            $dis = $params['mo_dis'];
            $str = $str . ", mo_rmk = '$rmk', mo_dis = '$dis'";
        }
        return $str;
    }

    private function mailhandler($params)
    {
        $point_no = $params['point_no'];
        $subject = '溫溼度開立偏差通知!';
        $sender = 'mpz.system@standard.com.tw';
        //$recipient = 'Lin.Yupin@standard.com.tw';
        $recipient = 'Lin.Guanwei@standard.com.tw';
        $point_name = DB::selectOne("select point_name from mpz_point where point_no = '$point_no'")->point_name;
        if ($this->type === 'mo' && $params['mo_devia'] === 'Y') {
            $content = '位置['.$point_name.']上午開立偏差';
            $this->sendMail($subject, $sender, $recipient, $content);
        }
        if ($this->type === 'af' && $params['af_devia'] === 'Y') {
            $content = '位置['.$point_name.']下午1開立偏差';
            $this->sendMail($subject, $sender, $recipient, $content);
        }
        if ($this->type === 'ev' && $params['ev_devia'] === 'Y') {
            $content = '位置['.$point_name.']下午2開立偏差';
            $this->sendMail($subject, $sender, $recipient, $content);
        }
    }

    private function sendMail($subject, $sender, $recipient, $content)
    {
        $nu = null;
        $t2 = 'Lin.Yupin@standard.com.tw';
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin pk_mail.proc_mail_02(:f, :t1, :t2, :t3, :c1, :c2, :c3, :s, :m); end;");
        $stmt->bindParam(':f', $sender);
        $stmt->bindParam(':t1', $recipient);
        $stmt->bindParam(':t2', $t2);
        $stmt->bindParam(':t3', $nu);
        $stmt->bindParam(':c1', $nu);
        $stmt->bindParam(':c2', $nu);
        $stmt->bindParam(':c3', $nu);
        $stmt->bindParam(':s', $subject);
        $stmt->bindParam(':m', $content);
        $stmt->execute();
    }
}