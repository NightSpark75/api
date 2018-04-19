<?php
/**
 * inventory controller
 *
 * @version 1.0.0
 * @author spark Lin.yupin@standart.com.tw
 * @date 18/04/19
 * @since 1.0.0 spark: build basic function
 * 
 */
namespace App\Http\Controllers\ProductWarehouse;

use App\Http\Controllers\Controller;
use App\Services\ProductWarehouse\InventoryService;
use Exception;
use App\Traits\Common;

/**
 * Class InventoryController
 *
 * @package App\Http\Controllers\ProductWarehouse
 */
class InventoryController extends Controller
{
    use Common;

    /**
     * @var InventoryService
     */
    private $inventoryService;

    /**
     * @param InventoryService $shippingService
     * @throws Exception
     */
    public function __construct(InventoryService $shippingService) 
    {
        $this->shippingService = $shippingService;
    }

    /**
     * get inventory list
     *
     * @param string $date
     * @throws Exception
     * @return mixed
     */
    public function getInventoryList($date)
    {
        try {
            $info = $this->shippingService->getInventoryInfo($spno, $date);
            return response()->json($info, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * get inventory item
     *
     * @param string $cyno
     * @param string $date
     * @throws Exception
     * @return mixed
     */
    public function getInventoryItem($cyno, $date)
    {
        try {
            $info = $this->shippingService->getInventoryInfo($spno, $date);
            return response()->json($info, 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }

    /**
     * save inventory amount
     *
     * @throws Exception
     * @return mixed
     */
    public function saveInventory()
    {
        try {
            $user = session('user');
            $id = $user->id;
            $spno = request()->input('spno');
            $date = request()->input('date');
            $pieces = request()->input('pieces');
            $this->shippingService->savePieces($spno, $date, $id, $pieces);
            return response()->json(['result' => true], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
