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
     * @param ShippingListRepository $shippingListRepository
     */
    public function __construct(
        ShippingListRepository $shippingListRepository
    ) {
        $this->shippingListRepository = $shippingListRepository;
    }

    /**
     * get today shipping items
     *
     * @param string $spno
     * @return mixed
     */
    public function getShippingInfo($spno, $date)
    {
        $date = $date? $date: date('Ymd', strtotime("20180321")).' 00:00:00';
        $list = $this->shippingListRepository->getShippingInfo($spno, $date);
        if (!isset($list)) {
            throw new Exception("查貨號 = $spno, 日期 = $date, 查詢不到資料");
        }
        return $list;
    }

    /**
     * 
     */
    public function savePieces($spno, $date, $user, $pieces)
    {
        $date = $date? $date: date('Y-m-d') . ' 00:00:00';
        $shipping = $this->shippingListRepository->getShippingInfo($spno, $date);
        
        if ($shipping) {
            $addj = date_format(date_create($date), 'Y/m/d');
            $this->shippingListRepository->savePieces($spno, $addj, $user, $pieces);
            return true;
        }
        throw new Exception("spno='$spno' and addj='$date', data not found!");
    }
}