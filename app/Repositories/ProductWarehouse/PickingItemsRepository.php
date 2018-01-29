<?php
/**
 * picking items repository
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/01/22
 * @since 1.0.0 spark: build database I/O function 
 * 
 */
namespace App\Repositories\ProductWarehouse;

use App\Interfaces\RepositoryInterface;
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
        return 'App\Models\ProductWarehouse\PickingList';
    }

    /**
     * get picking list by date
     * 
     * @param string $date => 'Y-m-d 00:00:00'
     * @return mixed
     */
    function getPickingItems($stop, $date)
    {
        return true;
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
        return true;
    }
}