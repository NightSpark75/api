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

use App\Repositories\ProductWarehouse\ShippingListRepository;
use App\Repositories\ProductWarehouse\ShippingItemsRepository;
use Exception;
use DB;

/**
 * Class ShippingService
 *
 * @package App\Services
 */
class ShippingService {

    /**
     * @var ShippingListRepository
     */
    private $shippingListRepository;
    
    /**
     * @var ShippingItemsRepository
     */
    private $shippingItemsRepository;

    /**
     * @param ShippingListRepository $shippingListRepository
     * @param ShippingItemsRepository $shippingItemsRepository
     */
    public function __construct(
        ShippingListRepository $shippingListRepository,
        ShippingItemsRepository $shippingItemsRepository
    ) {
        $this->shippingListRepository = $shippingListRepository;
        $this->shippingItemsRepository = $shippingItemsRepository;
    }

    /**
     * get today shipping items
     *
     * @param string $spno
     * @return mixed
     */
    public function getShippingItems($spno, $date)
    {
        //$date = $today? $today: date('Y-m-d').' 00:00:00';
        $date = $date? $date: date('Ymd', strtotime("20180321")).' 00:00:00';
        $list = $this->shippingItemsRepository->getShippingItems($spno, $date);
        return $list;
    }

    /**
     * get today shipping list
     *
     * @return mixed
     */
    public function getTodayShippingList($date = null)
    {
        //$date = $today? $today: date('Y-m-d').' 00:00:00';
        $date = $date? $date: date('Ymd', strtotime("20180321")).' 00:00:00';
        $list = $this->shippingListRepository->getShippingList($date);
        return $list;
    }

    /**
     * post start shipping update start time
     *
     * @param string $spno
     * @param string $empno
     */
    public function startShipping($spno, $user, $date = null)
    {
        $date = $date? $date: date('Y-m-d').' 00:00:00';
        $shipping = $this->shippingListRepository->getShipping($spno, $date);
        
        if ($shipping) {
            $addj = date_format(date_create($date), 'Y/m/d');
            $this->shippingListRepository->startShipping($spno, $addj, $user);
            return true;
        }
        throw new Exception("spno='$spno' and addj='$date', data not found!");
    }

    /**
     * post end shipping and update end time and status
     *
     * @param string $spno
     * @param string $empno
     */
    public function endShipping($spno, $user, $date = null)
    {
        $date = $date? $date: date('Y-m-d').' 00:00:00';
        $shipping = $this->shippingListRepository->getshipping($spno, $date);
        if ($shipping) {
            $addj = date_format(date_create($date), 'Y/m/d');
            $this->shippingListRepository->endshipping($spno, $addj, $user);
            return true;
        }
        throw new Exception("spno='$spno' and addj='$date', data not found!");
    }
}