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
     * @param InventoryService $inventoryService
     * @throws Exception
     */
    public function __construct(InventoryService $inventoryService) 
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * get inventory list
     *
     * @param string $date
     * @throws Exception
     * @return mixed
     */
    public function getInventoryList($date = null)
    {
        try {
            $list = $this->inventoryService->getInventoryList($date);
            return response()->json($list, 200);
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
    public function getInventoryItem($cyno)
    {
        try {
            $item = $this->inventoryService->getInventoryItem($cyno);
            return response()->json($item, 200);
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
            $cyno = request()->input('cyno');
            $locn = request()->input('locn');
            $litm = request()->input('litm');
            $lotn = request()->input('lotn');
            $amount = request()->input('amount');
            $nextItem = $this->inventoryService->saveInventory($id, $cyno, $locn, $litm, $lotn, $amount);
            return response()->json(['item' => $nextItem], 200);
        } catch (Exception $e) {
            return response()->json($this->getException($e), 400);
        }
    }
}
