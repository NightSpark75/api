<?php
/**
 * shiping controller
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/07
 * @since 1.0.0 spark: build basic function
 * 
 */
namespace App\Http\Controllers\ProductWarehouse;

use App\Http\Controllers\Controller;
use App\Services\ProductWarehouse\shipingService;
use Exception;
use App\Traits\Common;

/**
 * Class ShipingController
 *
 * @package App\Http\Controllers\ProductWarehouse
 */
class ShipingController extends Controller
{
    use Common;

    /**
     * @var ShipinggService
     */
    private $ShipingService;

    /**
     * @param ShipingService $shipingService
     * @throws Exception
     */
    public function __construct(ShipingService $shipingService) 
    {
        $this->shipingService = $shipingService;
    }

    /**
     * get today shiping list
     *
     * @throws Exception
     * @return mixed
     */
    public function getshipingList()
    {
        try {
            $list = $this->shipingService->getTodayshipingList();
            return response()->json($list, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get shiping items by stop at today
     *
     * @param string $stop
     * @throws Exception
     * @return mixed
     */
    public function getShipingItems($stop)
    {
        try {
            $items = $this->shipingService->getShipingItems($stop);
            return response()->json($items, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * start shiping
     *
     * @throws Exception
     * @return mixed
     */
    public function startShiping()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->shipingService->startShiping($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * end shiping
     *
     * @throws Exception
     * @return mixed
     */
    public function endshiping()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->shipingService->endShiping($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
