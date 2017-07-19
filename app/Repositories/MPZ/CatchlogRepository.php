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

    public function getPoint()
    {
        $list = $this->point->join('mpz_device', 'mpz_point.device_type', '=', 'mpz_device.device_no')
            ->where('mpz_point.state', 'Y')
            ->select('mpz_point.point_no' ,'point_name', 'mpz_point.device_type', 'device_name')
            ->get();
        if (isset($list)) {
            return $list;
        }
        throw new Exception('查詢不到檢查點資料!(#0002)');
    }

    public function check()
    {

    }

    public function insert($params)
    {
        try{
            $this->catchlog->insert($params);
            $result = [
                'result' => true,
                'msg' => '新增檢查點資料成功(#0001)'
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