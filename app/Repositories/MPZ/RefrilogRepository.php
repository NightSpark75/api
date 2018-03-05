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

    private $type;

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
        if (isset($params['mo_pa'])) {
            $this->type = 'mo';
            return $this->setParams($user, $params);
        }
        if (isset($params['af_pa'])) {
            $this->type = 'af';
            return $this->setParams($user, $params);
        }
        return $params;
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
        if (isset($params['mo_temp']) && isset($data->mo_time)) {
            $this->type = 'mo';
            return $this->getUpdateString($user, $params);
        }
        if (isset($params['af_temp']) && isset($data->af_time)) {
            $this->type = 'af';
            return $this->getUpdateString($user, $params);
        }
        return '';
    }
    
    private function getUpdateString($user, $params)
    {
        $time = date('Hi');
        $k_temp = $this->type.'_temp';
        $k_ed = $this->type.'_ed';
        $k_ep = $this->type.'_ep';
        $k_devia = $this->type.'_devia';
        $k_time = $this->type.'_time';
        $k_user = $this->type.'_user';
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
        if ($this->type === 'mo') {
            $putt = $params['mo_putt'];
            $bell = $params['mo_bell'];
            $light = $params['mo_light'];
            $rmk = $params['mo_rmk'];
            $dis = $params['mo_dis'];
            $str = $str . "mo_putt = '$putt', mo_bell = '$mo_bell', mo_light = '$mo_light', mo_rmk = '$rmk', mo_dis = '$dis'";
        }
        return $str;
    }

    private function mailhandler($params)
    {
        $point_no = $params['point_no'];
        $subject = '冷藏櫃開立偏差通知!';
        $sender = 'mpz.system@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        if ($this->type === 'mo' && $params['mo_devia'] === 'Y') {
            $content = '位置編號['.$point_no.']上午開立偏差';
            $this->sendMail($subject, $sender, $recipient, $content);
        }
        if ($this->type === 'af' && $params['af_devia'] === 'Y') {
            $content = '位置編號['.$point_no.']下午開立偏差';
            $this->sendMail($subject, $sender, $recipient, $content);
        }
    }

    private function sendMail($subject, $sender, $recipient, $content)
    {
        $nu = null;
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin pk_mail.proc_mail_02(:f, :t1, :t2, :t3, :c1, :c2, :c3, :s, :m); end;");
        $stmt->bindParam(':f', $sender);
        $stmt->bindParam(':t1', $recipient);
        $stmt->bindParam(':t2', $nu);
        $stmt->bindParam(':t3', $nu);
        $stmt->bindParam(':c1', $nu);
        $stmt->bindParam(':c2', $nu);
        $stmt->bindParam(':c3', $nu);
        $stmt->bindParam(':s', $subject);
        $stmt->bindParam(':m', $content);
        $stmt->execute();
    }
}