<?php
/**
 * shipping items repository
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Repositories\Repository;

/**
 * Class ShippingItemsRepository
 *
 * @package App\Repositories
 */
class ShippingItemsRepository extends Repository
{      
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\ShippingItems';
    }

    /**
     * get shipping items by date and stop
     * 
     * @param string $stop 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getShippingItems($spno, $date)
    {   
        $list = $this->model
            ->where('sdtrdj', $date)
            ->where('sdy59spno', $stop)
            ->select('sdy59spno', 'sdtrdj', 'sdlitm', 'sdlotn', 'sduorg')
            ->orderBy('sdlitm')
            ->orderBy('sdlotn')
            ->get();
        return $list;
    }
}