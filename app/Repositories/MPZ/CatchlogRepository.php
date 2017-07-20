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

    public function __construct(
        MPZ_CATCHLOG $catchlog, 
        MPZ_DEVICE $device,
        MPZ_POINT $point
    ) {
        $this->chatchlog = $catchlog;
        $this->device = $device;
        $this->point = $point;
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
        $msg = '';
        $today =  date('Ymd');
        $check = $this->catchlog
            ->where('point_no', $point_no)
            ->where('ldate', $today)
            ->first();
        if (isset($check)) {
            $result = [
                'result' => false,
                'msg' => '此檢查點今日已記錄完畢!(#0004)'
            ];
            return $result;
        }
        $result = [
            'result' => true,
            'msg' => '',
        ];
        return $result;
    }

    public function catchCount($point_no)
    {
        try{
            $thisMonth = $this->getCatchCount($point_no, (int)date('Ym'));
            $lastMonth = $this->getCatchCount($point_no, (int)date('Ym') - 1);
            $result = [
                'result' => true,
                'msg' => '',
                'thisMonth' => $thisMonth,
                'lastMonth' => $lastMonth,
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
                + count(catch_num4) + count(catch_num5) + count(catch_num6)'))
            ->first();
        return $count;
    }

    private function getLastMonth($point_no)
    {
                $thisMonth = (int) date('Ym');
    }   

    public function insert($params)
    {
        try{
            $this->catchlog->insert($params);
            $result = [
                'result' => true,
                'msg' => '新增檢查點資料成功(#0003)'
            ];
            return $result;
        } catch (\Exception $e) {
            $result = [
                'result' => false,
                'msg' => $e->getMessage(),
            ];
            return $result;
        }
    }
}