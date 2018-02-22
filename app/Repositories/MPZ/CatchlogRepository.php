<?php
/**
 * 鼠蟲防治記錄資料處理
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
 * Class CatchlogRepository
 *
 * @package App\Repositories
 */
class CatchlogRepository
{   
    use Sqlexecute;

    public function __construct() {

    }

    public function init($point_no)
    {
        try{
            $ldate = date('Ymd');
            $thisMonth = (int)substr($ldate, 0, 6);
            $lastMonth = (int)substr(date('Ymd', strtotime('-1 month')), 0, 6);
            $data = $this->getLogData($point_no, $ldate);
            $rule = $this->getPointRule($point_no);

            $thisAllCount = $this->getAllCatchCount($point_no, $thisMonth);
            $lastAllCount = $this->getAllCatchCount($point_no, $lastMonth);

            $thisTotalCount = array_sum($thisAllCount);
            $lastTotalCount = array_sum($lastAllCount);

            $lastGrowth = $this->getLastMonthGrowth($point_no, $lastTotalCount);
            
            $changeDate = $this->getAllChangeDate($point_no, $ldate);
            
            $result = [
                'result' => true,
                'msg' => '',
                'log_data' => $data,
                'rule' => $rule,
                'ldate' => $ldate,
                'thisAllCount' => $thisAllCount,
                'lastAllCount' => $lastAllCount,
                'thisTotalCount' => $thisTotalCount,
                'lastTotalCount' => $lastTotalCount,
                'lastGrowth' => $lastGrowth,
                'changeDate' => $changeDate,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    private function getLogData($point_no, $ldate)
    {
        $data = DB::selectOne("
            select *
            from mpz_catchlog
            where point_no = '$point_no' and ldate = $ldate
        ");
        return $data;
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

    private function getCatchCount($point_no, $month, $num)
    {
        $count = DB::selectOne("
            select sum($num) as n
            from mpz_catchlog
            where ldate like '$month%' and point_no = '$point_no'
        ");
        return $count->n;
    }

    private function getLastMonthGrowth($point_no, $lastTotalCount)
    {
        $twoMonthAgo = (int)substr(date('Ymd', strtotime('-2 month')), 0, 6);
        $twoMonthAgoCount = array_sum($this->getAllCatchCount($point_no, $twoMonthAgo));
        $growth = 0;
        if ($twoMonthAgoCount > 0) {
            $growth = ($lastTotalCount - $twoMonthAgoCount) / $twoMonthAgoCount;
        }
        return $growth;
    }

    private function getAllCatchCount($point_no, $month)
    {
        $catchCount = [
            'catch1' => (int)$this->getCatchCount($point_no, $month, 'catch_num1'),
            'catch2' => (int)$this->getCatchCount($point_no, $month, 'catch_num2'),
            'catch3' => (int)$this->getCatchCount($point_no, $month, 'catch_num3'),
            'catch4' => (int)$this->getCatchCount($point_no, $month, 'catch_num4'),
            'catch5' => (int)$this->getCatchCount($point_no, $month, 'catch_num5'),
            'catch6' => (int)$this->getCatchCount($point_no, $month, 'catch_num6'),
        ];
        return $catchCount;
    }

    private function getAllChangeDate($point_no, $ldate)
    {
        $changeDate = [
            'change1' => $this->getChangeDate($point_no, $ldate, 'change1'),
            'change2' => $this->getChangeDate($point_no, $ldate, 'change2'),
            'change3' => $this->getChangeDate($point_no, $ldate, 'change3'),
            'change4' => $this->getChangeDate($point_no, $ldate, 'change4'),
            'change5' => $this->getChangeDate($point_no, $ldate, 'change5'),
            'change6' => $this->getChangeDate($point_no, $ldate, 'change6'),
        ];
        return $changeDate;
    }

    private function getChangeDate($point_no, $ldate, $item)
    {
        $date = DB::selectOne("
            select max(ldate) as pday, to_date($ldate, 'YYYYMMDD') - to_date(max(ldate), 'YYYYMMDD') as dday
            from mpz_catchlog
            where point_no = '$point_no' and ldate < $ldate and $item = 'Y'
        ");
        return $date;
    }

    public function save($params)
    {
        try{
            DB::transaction( function () use($params) {
                $params['duser'] = auth()->user()->id;
                $params['ddate'] = date("Y-m-d H:i:s");
                $params['state'] = 'Y';
                DB::table('mpz_catchlog')->insert($params);

                $params2 = [
                    'point_no' => $params['point_no'],
                    'ldate' => $params['ldate'],
                    'duser' => $params['duser'],
                    'ddate' => $params['ddate'],
                    'point_type' => 'C', 
                ];
                DB::table('mpz_point_log')->insert($params2);
                DB::commit();
                if ($params['deviation'] === 'Y') {
                    $this->mailhandler($params['point_no']);
                }
            });
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

    private function mailhandler($point_no)
    {
        $subject = '鼠蟲防治開立偏差通知!';
        $sender = 'mpz.system@standard.com.tw';
        //$recipient = 'Lin.Guanwei@standard.com.tw';
        $recipient = 'Lin.Yupin@standard.com.tw';
        $content = '位置編號['.$point_no.']開立偏差';
        $this->sendMail($subject, $sender, $recipient, $content);
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