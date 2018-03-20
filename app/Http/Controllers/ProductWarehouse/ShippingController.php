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
use App\Services\ProductWarehouse\shippingService;
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
    private $ShippingService;

    /**
     * @param ShippingService $shippingService
     * @throws Exception
     */
    public function __construct(ShippingService $shippingService) 
    {
        $this->shippingService = $shippingService;
    }

    /**
     * get today shipping list
     *
     * @throws Exception
     * @return mixed
     */
    public function getshippingList()
    {
        try {
            $list = $this->shippingService->getTodayshippingList();
            return response()->json($list, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get shipping items by stop at today
     *
     * @param string $stop
     * @throws Exception
     * @return mixed
     */
    public function getShippingItems($stop)
    {
        try {
            $items = $this->shippingService->getShippingItems($stop);
            return response()->json($items, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * start shipping
     *
     * @throws Exception
     * @return mixed
     */
    public function startShipping()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->shippingService->startShipping($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * end shipping
     *
     * @throws Exception
     * @return mixed
     */
    public function endshipping()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->shippingService->endShipping($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
