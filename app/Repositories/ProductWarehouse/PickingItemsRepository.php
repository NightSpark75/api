<?php
/**
 * picking items repository
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace App\Repositories\ProductWarehouse;

use DB;
use App\Repositories\Repository;

/**
 * Class PickingItemsRepository
 *
 * @package App\Repositories
 */
class PickingItemsRepository extends Repository
{       
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\PickingItems';
    }

    /**
     * get picking items by date and stop
     * 
     * @param string $stop 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getPickingItems($stop, $date)
    {   
        $stop = str_pad($stop, 3, " ", STR_PAD_RIGHT);
        $list = $this->model
            ->where('psaddj', $date)
            ->where('psstop', $stop)
            ->select('psicu', 'psaddj', 'psstop', 'pslocn', 'psrmk', 'pslitm', 'pslotn', 'pssoqs', 'pspqoh', 'psuom')
            ->orderBy('pslocn')
            ->orderBy('psrmk')
            ->orderBy('pslitm')
            ->get();
        return $list;
    }
}