<?php
/**
 * shiping service
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/07
 * @since 1.0.0 spark: handle shiping business logic
 * 
 */
namespace App\Services\ProductWarehouse;

use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use Exception;
use DB;

/**
 * Class ShipingService
 *
 * @package App\Services
 */
class ShipingService {

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
     * @param PickingItemRepository $pickingItemsRepository
     */
    public function __construct(
        PickingListRepository $pickingListRepository,
        PickingItemsRepository $pickingItemsRepository
    ) {
        $this->pickingListRepository = $pickingListRepository;
        $this->pickingItemsRepository = $pickingItemsRepository;
    }

    /**
     * get today shiping items
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
     * get today shiping list
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
     * post start shiping update start time
     *
     * @param string $stop
     * @param string $empno
     */
    public function startShiping($stop, $user, $today = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getShiping($stop, $date);
        
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->startShiping($stop, $staddj, $user);
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
    public function endShiping($stop, $user, $today = null)
    {
        $date = $today? $today: date('Y-m-d').' 00:00:00';
        $picking = $this->pickingListRepository->getshiping($stop, $date);
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->endshiping($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }
}