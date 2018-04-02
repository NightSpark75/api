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
    public function getShippingInfo($spno)
    {
        $list = $this->shippingListRepository->getShippingInfo($spno);
        if (isset($list)) {
            return $list;
        }
        throw new Exception("查貨號 = $spno, 查詢不到資料");
    }

    /**
     * save shippping pieces, and check shipping info
     * 
     * @param string $spno
     * @param string $date
     * @param string $user 
     * @param string $pieces
     * @return mixed
     */
    public function savePieces($spno, $date, $user, $pieces)
    {
        $shipping = $this->shippingListRepository->checkShippingInfo($spno, $date);
        
        if ($shipping === 1) {
            //$tmtrdj = date_format(date_create($shipping->tmtrdj), 'Y/m/d') . ' 00:00:00';
            $this->shippingListRepository->savePieces($spno, $date, $user, $pieces);
            return true;
        }
        throw new Exception("spno='$spno', data not found!");
    }
}