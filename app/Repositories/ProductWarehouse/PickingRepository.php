<?php
/**
 * 揀貨資料處理
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * 
 */
namespace App\Repositories\ProductWarehouse;

use DB;
use Exception;
use App\Traits\Sqlexecute;
use App\Traits\Oracle;
use App\Repositories\Repository;

/**
 * Class PickingRepository
 *
 * @package App\Repositories
 */
class PickingRepository extends Repository
{   
    use Sqlexecute;
    use Oracle;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\Pick';
    }

    /**
     * get picking list by date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    function getPickingList($date)
    {
        $list = $this->model
            ->where('staddj', $date)
            ->where('stky3', null)
            ->get();
        return $list;
    }

    /**
     * get picking by stop date
     * 
     * @param string $stop
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    function getPicking($stop, $date)
    {
        $picking = $this->model
            ->where('ststop', $stop)
            ->where('staddj', $date)
            ->first();
        return $picking;
    }
}