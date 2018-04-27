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
    public function getPickingItem($stop, $user, $date = null)
    {
        $date = $date? $date: date('Ymd');
        $list = $this->pickingItemsRepository->getPickingItems($stop, $date, $user);
        $first = $list ? $list[0] : $list;
        return $first;
    }

    public function getPickingItems($stop, $date = null)
    {
        $date = $date? $date: date('Ymd');
        $list = $this->pickingItemsRepository->getPickingItems($stop, $date);
        return $list;
    }

    /**
     * get today picking list
     *
     * @param string $date
     * @return mixed
     */
    public function getPickingList($user, $date = null)
    {
        $date = $date? $date: date('Ymd');
        $list = [];
        $current = $this->pickingListRepository->getCurrent($user, $date);
        if (!$current) {
            $list = $this->pickingListRepository->getPickingList($user, $date);
        }
        return compact('current', 'list');
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
        $date = $date? $date: date('Ymd');
        $check = $this->pickingListRepository->checkStartPicking($stop, $date);
        if ($check) {
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
        $date = $date? $date: date('Ymd');
        $check = $this->pickingListRepository->checkPicking($stop, $date, $user);
        if ($check) {
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
        $date = $date? $date: date('Ymd');
        $check = $this->pickingListRepository->checkPicking($stop, $date, $user);
        if ($check) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingListRepository->pausePicking($stop, $staddj, $user);
            return true;
        }
        throw new Exception("ststop='$stop' and staddj='$date', data not found!");
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
        $date = $date? $date: date('Ymd');
        $check = $this->pickingListRepository->checkPicking($stop, $date, $user);
        if ($check) {
            $staddj = date_format(date_create($date), 'Y/m/d');
            $this->pickingItemsRepository->pickup($stop, $staddj, $rmk, $litm, $lotn, $user);
            $item = $this->pickingItemsRepository->getPickingItems($stop, $date, $user);
            return $item[0];
        }
        throw new Exception("
            ststop='$stop' and staddj='$date' 
            and rmk = '$rmk' and litm = '$litm' and lotn = '$lotn' 
            , data not found!
        ");
    }
}