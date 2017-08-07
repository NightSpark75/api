<?php
/**
 * 檢查點記錄資料處理
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

use App\Models\MPZ\MPZ_POINT;
use App\Models\MPZ\MPZ_POINT_LOG;

/**
 * Class PointlogRepository
 *
 * @package App\Repositories
 */
class PointlogRepository
{   
    use Sqlexecute;
    private $point;
    private $point_log;

    public function __construct(
        MPZ_POINT $point,
        MPZ_POINT_LOG $point_log
    ) {
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
            ->select('mpz_point.point_no' ,'point_name', 'mpz_point.device_type', 'device_name', 'point_type')
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
            $check = $this->point_log
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
}