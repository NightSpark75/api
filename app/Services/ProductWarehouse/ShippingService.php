<?php
/**
 * shipping service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/07
 * @since 1.0.0 spark: handle shipping business logic
 * 
 */
namespace App\Services\ProductWarehouse;

use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use Exception;
use DB;

/**
 * Class ShippingService
 *
 * @package App\Services
 */
class ShippingService {

    /**
     * @var PickingListRepository
     */
    private $pickingListRepository;
    
    /**
     * @var PickingItemsRepository
     */
    private $pickingItemsRepository;

    /**
     * @param PickingListRepository $pickingListRepository
     * @param PickingItemsRepository $pickingItemsRepository
     */
    public function __construct(
        PickingListRepository $pickingListRepository,
        PickingItemsRepository $pickingItemsRepository
    ) {
        $this->pickingListRepository = $pickingListRepository;
        $this->pickingItemsRepository = $pickingItemsRepository;
    }

    /**
     * get today shipping items
     *
     * @param string $stop
     * @return mixed
     */
    public function getPickingItems($stop)
    {
        //$date = $today? $today: date('Y-m-d').' 00:00:00';
        $date = date('Ymd', strtotime("20180305")).' 00:00:00';
        $list = $this->pickingItemsRepository->getPickingItems($stop, $date);
        return $list;
    }

    /**
     * get today shipping list
     *
     * @return mixed
     */
    public function getTodayPickingList($today = null)
    {
        //$date = $today? $today: date('Y-m-d').' 00:00:00';
        $date = date('Ymd', strtotime("20180306")).' 00:00:00';
        $list = $this->pickingListRepository->getPickingList($date);
        return $list;
    }

    /**
     * post start shipping update start time
     *
     * @param string $stop
     * @param string $empno
     */
    public function startShipping($stop, $user, $today = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getShipping($stop, $date);
        
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->startShipping($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }

    /**
     * post end picking and update end time and status
     *
     * @param string $stop
     * @param string $empno
     */
    public function endShipping($stop, $user, $today = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getshipping($stop, $date);
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->endshipping($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }
}