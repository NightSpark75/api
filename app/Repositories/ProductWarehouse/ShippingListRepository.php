<?php
/**
 * shipping repository
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * @since 1.0.1 spark: file name
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Repositories\Repository;

/**
 * Class ShippingListRepository
 *
 * @package App\Repositories
 */
class ShippingListRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\ProductWarehouse\ShippingList';
    }

    /**
     * get shipping info by spno and date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    public function getShippingInfo($spno, $date)
    {
        $list = $this->model
            ->where('tmy59spno', $spno)
            ->where('tmtrdj', $date)
            ->select('tmtrdj', 'tmaddj', 'tmy59spno', 'tmcars', 'cars_na', 'tman8', 'tmalph', 'tm1in1', 'dltm_na', 'tmalph1')
            ->first();
        return $list;
    }
}