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
            $data = DB::selectOne("
                select *
                from mpz_catchlog
                where point_no = '$point_no' and ldate = $ldate
            ");
            $month = (int)substr($ldate, 0, 6);
            $thisMonth = $this->getCatchCount($point_no, $month);
            $lastMonth = $this->getCatchCount($point_no, $month - 1);
            $changeDate = [
                'change1' => $this->getChangeDate($point_no, $ldate, 'change1'),
                'change2' => $this->getChangeDate($point_no, $ldate, 'change2'),
                'change3' => $this->getChangeDate($point_no, $ldate, 'change3'),
                'change4' => $this->getChangeDate($point_no, $ldate, 'change4'),
                'change5' => $this->getChangeDate($point_no, $ldate, 'change5'),
                'change6' => $this->getChangeDate($point_no, $ldate, 'change6'),
            ];
            $result = [
                'result' => true,
                'msg' => '',
                'log_data' => $data,
                'ldate' => $ldate,
                'thisMonth' => $thisMonth,
                'lastMonth' => $lastMonth,
                'changeDate' => $changeDate,
            ];
            return $result;
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    private function getCatchCount($point_no, $month)
    {
        $count = DB::selectOne("
            select count(catch_num1) + count(catch_num2) + count(catch_num3) 
                + count(catch_num4) + count(catch_num5) + count(catch_num6) as n
            from mpz_catchlog
            where ldate like '$month%' and point_no = '$point_no'
        ");
        return $count->n;
    }   

    private function getChangeDate($point_no, $ldate, $item)
    {
        $date = DB::selectOne("
            select max(ldate) as d
            from mpz_catchlog
            where point_no = '$point_no' and ldate < $ldate and $item = 'Y'
        ");
        return $date->d;
    }

    public function save($params)
    {
        try{
            DB::transaction( function () use($params) {
                $params['duser'] = auth()->user()->id;
                $params['ddate'] = date("Y-m-d H:i:s");
                $params['state'] = 'Y';
                DB::table('mpz_catchlog')->insert($params);

                $params = [
                    'point_no' => $params['point_no'],
                    'ldate' => $params['ldate'],
                    'duser' => $params['duser'],
                    'ddate' => $params['ddate'],
                    'point_type' => 'C', 
                ];
                DB::table('mpz_point_log')->insert($params);
                $result = [
                    'result' => true,
                    'msg' => '新增檢查點資料成功(#0003)'
                ];
                
            });
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            return $this->exception($e);
        }
    }
}