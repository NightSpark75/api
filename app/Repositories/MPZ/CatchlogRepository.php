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

use App\Models\MPZ\MPZ_CATCHLOG;
use App\Models\MPZ\MPZ_POINT_LOG;

/**
 * Class CatchlogRepository
 *
 * @package App\Repositories
 */
class CatchlogRepository
{   
    private $catchlog;
    private $point_log;

    public function __construct(
        MPZ_CATCHLOG $catchlog, 
        MPZ_POINT_LOG $point_log
    ) {
        $this->catchlog = $catchlog;
        $this->point_log = $point_log;
    }

    public function catchCount($point_no, $ldate)
    {
        try{
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
                'thisMonth' => $thisMonth,
                'lastMonth' => $lastMonth,
                'changeDate' => $changeDate,
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage()
            ];
            return $result;
        }
    }

    private function getCatchCount($point_no, $month)
    {
        $count = $this->catchlog
            ->where('ldate', 'like', $month.'%')
            ->where('point_no', $point_no)
            ->select(DB::raw('count(catch_num1) + count(catch_num2) + count(catch_num3) 
                + count(catch_num4) + count(catch_num5) + count(catch_num6) as n'))
            ->first();
        return $count->n;
    }   

    private function getChangeDate($point_no, $ldate, $item)
    {
        $date = $this->catchlog
            ->where('point_no', $point_no)
            ->where('ldate', '<', $ldate)
            ->where($item, 'Y')
            ->select(DB::raw("max(ldate) as d"))
            ->first();
        return $date->d;
    }

    public function save($params)
    {
        try{
            $params['duser'] = auth()->user()->id;
            $params['ddate'] = date("Y-m-d H:i:s");
            $this->catchlog->insert($params);
            $params = [
                'point_no' => $params['point_no'],
                'ldate' => $params['ldate'],
                'duser' => $params['duser'],
                'ddate' => $params['ddate'],
            ];
            $this->point_log->insert($params);
            $result = [
                'result' => true,
                'msg' => '新增檢查點資料成功(#0003)'
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }
}