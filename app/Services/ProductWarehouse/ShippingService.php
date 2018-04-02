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
     * save shippping pieces, and check shipping info
     * 
     * @param string $spno
     * @param string $date
     * @param string $user 
     * @param string $pieces
     * @return mixed
     */
    public function savePieces($spno, $user, $pieces)
    {
        $shipping = $this->shippingListRepository->getShippingInfo($spno);
        
        if ($shipping) {
            $tmtrdj = $shipping->tmtrdj;
            $this->shippingListRepository->savePieces($spno, $tmtrdj, $user, $pieces);
            return true;
        }
        throw new Exception("spno='$spno' and tmtrdj='$tmtrdj', data not found!");
    }
}