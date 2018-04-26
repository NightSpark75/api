<?php
/**
 * picking controller
 *
 * @version 1.0.2
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/03/20
 * @since 1.0.0 spark: build basic function
 * @since 1.0.2 spark: completed unit test and optimized code
 * 
 */
namespace App\Http\Controllers\ProductWarehouse;

use App\Http\Controllers\Controller;
use App\Services\ProductWarehouse\PickingService;
use Exception;
use App\Traits\Common;

/**
 * Class PickingController
 *
 * @package App\Http\Controllers\ProductWarehouse
 */
class PickingController extends Controller
{
    use Common;

    /**
     * @var PickingService
     */
    private $pickingService;

    /**
     * @param PickingService $pickingService
     * @throws Exception
     */
    public function __construct(PickingService $pickingService) 
    {
        $this->pickingService = $pickingService;
    }

    /**
     * get picking list by date
     *
     * @throws Exception
     * @return mixed
     */
    public function getPickingList($date = null)
    {
        try {
            $user = session('user');
            $id = $user->id;
            $result = $this->pickingService->getPickingList($id, $date);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get picking items by stop at date
     *
     * @param string $stop
     * @throws Exception
     * @return mixed
     */
    public function getPickingItem($stop, $date = null)
    {
        try {
            $user = session('user');
            $id = $user->id;
            $item = $this->pickingService->getPickingItem($stop, $id, $date);
            return response()->json($item, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    public function getPickingItems($stop, $date = null)
    {
        try {
            $items = $this->pickingService->getPickingItems($stop, $date);
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
            $date = request()->input('date');
            $result = $this->pickingService->startPicking($stop, $id, $date);
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
            $date = request()->input('date');
            $result = $this->pickingService->endPicking($stop, $id, $date);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * pause picking
     *
     * @throws Exception
     * @return mixed
     */
    public function pausePicking()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $date = request()->input('date');
            $result = $this->pickingService->pausePicking($stop, $date, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * restart picking
     *
     * @throws Exception
     * @return mixed
     */
    public function restartPicking()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $date = request()->input('date');
            $result = $this->pickingService->restartPicking($stop, $date, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * pickup
     *
     * @throws Exception
     * @return mixed
     */
    public function pickup()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $stop = request()->input('stop');
            $date = request()->input('date');
            $rmk = request()->input('rmk');
            $litm = request()->input('litm');
            $lotn = request()->input('lotn');
            $result = $this->pickingService->pickup($stop, $date, $rmk, $litm, $lotn, $id);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
