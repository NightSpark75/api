<?php
/**
 * shipping controller
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/07
 * @since 1.0.0 spark: build basic function
 * 
 */
namespace App\Http\Controllers\ProductWarehouse;

use App\Http\Controllers\Controller;
use App\Services\ProductWarehouse\ShippingService;
use Exception;
use App\Traits\Common;

/**
 * Class ShippingController
 *
 * @package App\Http\Controllers\ProductWarehouse
 */
class ShippingController extends Controller
{
    use Common;

    /**
     * @var ShippinggService
     */
    private $shippingService;

    /**
     * @param ShippingService $shippingService
     * @throws Exception
     */
    public function __construct(ShippingService $shippingService) 
    {
        $this->shippingService = $shippingService;
    }

    /**
     * get shipping items by stop at today
     *
     * @param string $stop
     * @throws Exception
     * @return mixed
     */
    public function getShippingInfo($spno, $date)
    {
        try {
            $info = $this->shippingService->getShippingInfo($spno, $date);
            return response()->json($info, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
