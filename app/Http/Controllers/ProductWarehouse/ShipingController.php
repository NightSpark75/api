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
use App\Services\ProductWarehouse\PickingService;
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
    public function __construct(PickingService $pickingService) 
    {
        $this->pickingService = $pickingService;
    }

    /**
     * get today picking list
     *
     * @throws Exception
     * @return mixed
     */
    public function getPickingList()
    {
        try {
            $list = $this->pickingService->getTodayPickingList();
            return response()->json($list, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get picking items by stop at today
     *
     * @param string $stop
     * @throws Exception
     * @return mixed
     */
    public function getPickingItems($stop)
    {
        try {
            $items = $this->pickingService->getPickingItems($stop);
            return response()->json($items, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * start picking
     *
     * @throws Exception
     * @return mixed
     */
    public function startPicking()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->pickingService->startPicking($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * end picking
     *
     * @throws Exception
     * @return mixed
     */
    public function endPicking()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $result = $this->pickingService->endPicking($stop, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}