<?php
/**
 * picking service
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: handle picking business logic
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace App\Services\ProductWarehouse;

use App\Repositories\ProductWarehouse\PickingListRepository;
use App\Repositories\ProductWarehouse\PickingItemsRepository;
use Exception;

/**
 * Class PickingService
 *
 * @package App\Services
 */
class PickingService {

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
     * get today picking items
     *
     * @param string $stop
     * @param string $date
     * @return mixed
     */
    public function getPickingItems($stop, $date = null)
    {
        $date = $date? $date: date('Y-m-d') . ' 00:00:00';
        $list = $this->pickingItemsRepository->getPickingItems($stop, $date);
        return $list;
    }

    /**
     * get today picking list
     *
     * @param string $date
     * @return mixed
     */
    public function getTodayPickingList($date = null)
    {
        $date = $date? $date: date('Y-m-d') . ' 00:00:00';
        $list = $this->pickingListRepository->getPickingList($date);
        return $list;
    }

    /**
     * post start picking update start time
     *
     * @param string $stop
     * @param string $empno
     * @param string $date
     * @return mixed
     */
    public function startPicking($stop, $user, $date = null)
    {
        $date = $date? $date: date('Y-m-d') . ' 00:00:00';
        $picking = $this->pickingListRepository->getPicking($stop, $date);
        
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->startPicking($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }

    /**
     * post end picking and update end time and status
     *
     * @param string $stop
     * @param string $empno
     * @param string $date
     * @return mixed
     */
    public function endPicking($stop, $user, $date = null)
    {
        $date = $date? $date: date('Y-m-d') . ' 00:00:00';
        $picking = $this->pickingListRepository->getPicking($stop, $date);
        if ($picking) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->endPicking($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
    }

    /**
     * pause picking
     * 
     * @param string $stop
     * @param string $date
     * @param string $user
     * @return mixed
     */
    public function pausePicking($stop, $date, $user)
    {   

    }

    /**
     * restart picking
     * 
     * @param string $stop
     * @param string $date
     * @param string $user
     * @return mixed
     */
    public function restartPicking($stop, $date, $user)
    {

    }

    /**
     * pickup
     * 
     * @param string $stop
     * @param string $date
     * @param string $rmk
     * @param string $litm
     * @param string $lotn
     * @param string $user
     * @return mixed
     */
    public function pickup($stop, $date, $rmk, $litm, $lotn, $user)
    {

    }
}