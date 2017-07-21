<?php
/**
 * 鼠蟲防治記錄資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 17/06/22
 * @since 1.0.0 spark: 建立檔案寫入與讀取相關的資料處理
 * 
 */
namespace App\Repositories\MPZ;

use DB;
use Exception;
use App\Traits\Sqlexecute;

use App\Models\MPZ\MPZ_CATCHLOG;
use App\Models\MPZ\MPZ_DEVICE;
use App\Models\MPZ\MPZ_POINT;
use App\Models\MPZ\MPZ_POINT_LOG;

/**
 * Class CatchlogRepository
 *
 * @package App\Repositories
 */
class CatchlogRepository
{   
    use Sqlexecute;
    
    private $catchlog;
    private $device;
    private $point;
    private $point_log;

    public function __construct(
        MPZ_CATCHLOG $catchlog, 
        MPZ_DEVICE $device,
        MPZ_POINT $point,
        MPZ_POINT_LOG $point_log
    ) {
        $this->catchlog = $catchlog;
        $this->device = $device;
        $this->point = $point;
        $this->point_log = $point_log;
    }

    public function init()
    {
        try{
            $point = $this->getPoint();
            $result = [
                'result' => true,
                'msg' => '已成功取得檢查點資料!(#0001)',
                'point' => $point,
            ];
            return $result;
        } catch (Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e.getMessage(),
            ];
            return $result;
        }
    }

    private function getPoint()
    {
        $list = $this->point->join('mpz_device', 'mpz_point.device_type', '=', 'mpz_device.device_no')
            ->where('mpz_point.state', 'Y')
            ->select('mpz_point.point_no' ,'point_name', 'mpz_point.device_type', 'device_name')
            ->get()->toArray();
        if (isset($list)) {
            return $list;
        }
        throw new Exception('查詢不到檢查點資料!(#0002)');
    }

    public function check($point_no)
    {
        try {
            $msg = '';
            $today =  (int)date('Ymd');
            $check = $this->catchlog
                ->where('point_no', $point_no)
                ->where('ldate', $today)
                ->first();
            if (isset($check)) {
                $result = [
                    'result' => false,
                    'msg' => '此檢查點今日已記錄完畢!(#0004)',
                ];
                return $result;
            }
            $result = [
                'result' => true,
                'msg' => '此檢查點今日尚未記錄(#0005)',
                'ldate' => $today,
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